<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\Rak;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\RevenueService;
use App\Services\HistoryService;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Halaman Checkout / Bayar
     */
    public function bayar($id)
    {
        try {
            $rak = Rak::findOrFail($id);

            // Cek apakah rak masih tersedia
            if ($rak->status !== 'tersedia') {
                return redirect()->route('customer.list-rak.list-rak')
                    ->with('error', 'Rak tidak tersedia untuk disewa.');
            }

            // Generate Order ID yang unik
            $orderId = 'ORDER-' . time() . '-' . $rak->id;

            // Parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $rak->harga_sewa_perbulan,
                ],
                'item_details' => [
                    [
                        'id' => $rak->id,
                        'price' => (int) $rak->harga_sewa_perbulan,
                        'quantity' => 1,
                        'name' => $rak->nama_rak
                    ]
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ]
            ];

            // Generate Snap token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // SIMPAN TRANSAKSI KE DATABASE DENGAN STATUS PENDING
            $transaction = Transaction::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'rak_id' => $rak->id,
                'amount' => $rak->harga_sewa_perbulan,
                'transaction_status' => 'pending',
                'snap_token' => $snapToken,
                'transaction_time' => now()
            ]);

            // Log untuk debugging
            Log::info('Transaction Created', [
                'transaction_id' => $transaction->id,
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'rak_id' => $rak->id,
                'amount' => $rak->harga_sewa_perbulan,
                'status' => 'pending'
            ]);

            // Tampilkan halaman checkout
            return view('customer.payment.checkout', compact('snapToken', 'rak'));
        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update Status Transaksi (Dipanggil dari Frontend setelah pembayaran)
     */
    public function updateStatus(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'order_id' => 'required|string',
                'transaction_status' => 'required|string'
            ]);

            $orderId = $request->order_id;
            $transactionStatus = $request->transaction_status;
            $paymentType = $request->payment_type ?? null;

            Log::info('Update Status Request', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'user_id' => Auth::id()
            ]);

            // Cari transaksi di database
            $transaction = Transaction::where('order_id', $orderId)
                ->where('user_id', Auth::id()) // Pastikan user yang update adalah pemiliknya
                ->first();

            if (!$transaction) {
                Log::error('Transaction not found', [
                    'order_id' => $orderId,
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            try {
                // Update status transaksi
                $transaction->update([
                    'transaction_status' => $transactionStatus,
                    'payment_type' => $paymentType
                ]);

                Log::info('Transaction Status Updated', [
                    'transaction_id' => $transaction->id,
                    'old_status' => $transaction->getOriginal('transaction_status'),
                    'new_status' => $transactionStatus
                ]);

                // Jika pembayaran sukses, update status rak
                if (in_array($transactionStatus, ['capture', 'settlement'])) {
                    $this->handleSuccessPayment($transaction);

                    // ===========================================
                    // TAMBAHKAN LOG HISTORY PEMBAYARAN BERHASIL
                    // ===========================================
                    try {
                        HistoryService::logPaymentSuccess(
                            Auth::id(),
                            $transaction->amount,
                            $transaction->id,
                            $paymentType ?? 'Midtrans',
                            Auth::user()->name
                        );

                        Log::info('Payment history logged successfully', [
                            'transaction_id' => $transaction->id,
                            'customer_id' => Auth::id()
                        ]);
                    } catch (\Exception $historyError) {
                        Log::error('Failed to log payment history: ' . $historyError->getMessage());
                        // Jangan rollback transaksi utama hanya karena gagal log history
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diupdate',
                    'transaction' => [
                        'id' => $transaction->id,
                        'order_id' => $transaction->order_id,
                        'status' => $transaction->transaction_status,
                        'amount' => $transaction->formatted_amount
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in DB Transaction: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Update Status Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Callback dari Midtrans Server (untuk verifikasi tambahan)
     */
    public function callback(Request $request)
    {
        try {
            // Gunakan Midtrans Notification untuk verifikasi signature
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $paymentType = $notification->payment_type ?? null;

            Log::info('Midtrans Callback Received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType
            ]);

            // Cari transaksi di database
            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::error('Transaction not found in callback', ['order_id' => $orderId]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            DB::beginTransaction();

            try {
                // Update data transaksi dengan response lengkap dari Midtrans
                $transaction->update([
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus,
                    'payment_type' => $paymentType,
                    'midtrans_response' => $notification->getResponse()
                ]);

                Log::info('Transaction Updated from Callback', [
                    'transaction_id' => $transaction->id,
                    'status' => $transactionStatus
                ]);

                // Handle berdasarkan status pembayaran
                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $this->handleSuccessPayment($transaction);

                        // ===========================================
                        // TAMBAHKAN LOG HISTORY DARI CALLBACK
                        // ===========================================
                        try {
                            HistoryService::logPaymentSuccess(
                                $transaction->user_id,
                                $transaction->amount,
                                $transaction->id,
                                $paymentType ?? 'Midtrans',
                                'System'
                            );

                            Log::info('Payment history logged from callback', [
                                'transaction_id' => $transaction->id,
                                'customer_id' => $transaction->user_id
                            ]);
                        } catch (\Exception $historyError) {
                            Log::error('Failed to log payment history from callback: ' . $historyError->getMessage());
                        }
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $this->handleSuccessPayment($transaction);

                    // ===========================================
                    // TAMBAHKAN LOG HISTORY DARI CALLBACK
                    // ===========================================
                    try {
                        HistoryService::logPaymentSuccess(
                            $transaction->user_id,
                            $transaction->amount,
                            $transaction->id,
                            $paymentType ?? 'Midtrans',
                            'System'
                        );

                        Log::info('Payment history logged from callback settlement', [
                            'transaction_id' => $transaction->id,
                            'customer_id' => $transaction->user_id
                        ]);
                    } catch (\Exception $historyError) {
                        Log::error('Failed to log payment history from callback settlement: ' . $historyError->getMessage());
                    }
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    Log::info('Payment Failed/Cancelled', ['order_id' => $orderId]);
                    // Bisa tambahkan logic untuk handle pembayaran gagal

                    // ===========================================
                    // TAMBAHKAN LOG HISTORY PEMBAYARAN GAGAL
                    // ===========================================
                    try {
                        HistoryService::logPaymentFailed(
                            $transaction->user_id,
                            $transaction->amount,
                            $transaction->id,
                            $transactionStatus,
                            'System'
                        );

                        Log::info('Failed payment history logged', [
                            'transaction_id' => $transaction->id,
                            'customer_id' => $transaction->user_id,
                            'status' => $transactionStatus
                        ]);
                    } catch (\Exception $historyError) {
                        Log::error('Failed to log failed payment history: ' . $historyError->getMessage());
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Callback processed successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing callback: ' . $e->getMessage());
                return response()->json(['message' => 'Error processing callback'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Callback Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());

            return response()->json(['message' => 'Invalid notification'], 400);
        }
    }

    /**
     * Handle Pembayaran Sukses
     */
    private function handleSuccessPayment($transaction)
    {
        $rak = Rak::find($transaction->rak_id);

        if ($rak && $rak->status === 'tersedia') {
            $rak->update(['status' => 'terisi']);

            Log::info('Rak Status Updated to Terisi', [
                'rak_id' => $rak->id,
                'rak_name' => $rak->nama_rak,
                'transaction_id' => $transaction->id
            ]);

            // Log History Sewa Rak
            try {
                HistoryService::logNewRental(
                    $transaction->user_id,
                    $rak->kode_rak ?? $rak->nama_rak,
                    30,
                    $transaction->amount,
                    'System'
                );

                Log::info('New rental history logged', [
                    'transaction_id' => $transaction->id,
                    'customer_id' => $transaction->user_id,
                    'rak_id' => $rak->id
                ]);
            } catch (\Exception $historyError) {
                Log::error('Failed to log new rental history: ' . $historyError->getMessage());
            }

            // =====================================================
            // AUTO GENERATE LAPORAN PENDAPATAN
            // =====================================================
            try {
                $year = $transaction->transaction_time->year;
                $month = $transaction->transaction_time->month;

                RevenueService::generateMonthlyReport($year, $month);

                Log::info('Revenue report auto-generated', [
                    'transaction_id' => $transaction->id,
                    'year' => $year,
                    'month' => $month
                ]);
            } catch (\Exception $revenueError) {
                Log::error('Failed to generate revenue report: ' . $revenueError->getMessage());
            }
        }
    }
}

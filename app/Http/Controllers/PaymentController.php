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
// TAMBAHKAN INI - Import HistoryService
use App\Services\HistoryService;
use App\Services\RevenueService;

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
        $userId = Auth::id();

        // ==============================================
        // CEK DULU APAKAH SUDAH ADA TRANSAKSI PENDING
        // ==============================================
        $existingPendingTransaction = Transaction::where('user_id', $userId)
            ->where('rak_id', $rak->id)
            ->where('transaction_status', 'pending')
            ->first();

        if ($existingPendingTransaction) {
            // Jika sudah ada transaksi pending untuk rak ini
            // Redirect langsung ke halaman tagihan
            return redirect()->route('customer.tagihan')
                ->with('info', 'Anda sudah memiliki transaksi pending untuk rak ini. Silakan selesaikan pembayaran di halaman Tagihan.');
        }

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

        // ==============================================
        // TIDAK BUAT TRANSAKSI DI SINI
        // Hanya simpan ke session untuk nanti
        // ==============================================
        session([
            'payment_checkout' => [ // Ganti nama session menjadi 'payment_checkout'
                'order_id' => $orderId,
                'rak_id' => $rak->id,
                'rak_nama' => $rak->nama_rak,
                'amount' => $rak->harga_sewa_perbulan,
                'snap_token' => $snapToken,
                'created_at' => now()
            ]
        ]);

        Log::info('Checkout session created', [
            'user_id' => $userId,
            'rak_id' => $rak->id,
            'order_id' => $orderId
        ]);

        // Tampilkan halaman checkout
        return view('customer.payment.checkout', compact('snapToken', 'rak'));

    } catch (\Exception $e) {
        Log::error('Payment Checkout Error: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function processPayment(Request $request)
{
    try {
        // Get data dari session checkout
        $checkoutData = session('payment_checkout');
        
        if (!$checkoutData) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi checkout tidak ditemukan. Silakan ulangi dari awal.'
            ], 400);
        }

        $userId = Auth::id();
        $rakId = $checkoutData['rak_id'];

        // ==============================================
        // CEK LAGI APAKAH SUDAH ADA TRANSAKSI PENDING
        // Ini penting untuk double-check
        // ==============================================
        $existingPendingTransaction = Transaction::where('user_id', $userId)
            ->where('rak_id', $rakId)
            ->where('transaction_status', 'pending')
            ->first();

        if ($existingPendingTransaction) {
            // Hapus session karena sudah ada transaksi
            session()->forget('payment_checkout');
            
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada transaksi pending untuk rak ini.',
                'redirect_url' => route('customer.tagihan')
            ], 400);
        }

        // ==============================================
        // SEKARANG BARU BUAT TRANSAKSI
        // ==============================================
        $transaction = Transaction::create([
            'order_id' => $checkoutData['order_id'],
            'user_id' => $userId,
            'rak_id' => $rakId,
            'amount' => $checkoutData['amount'],
            'transaction_status' => 'pending', // Status awal
            'snap_token' => $checkoutData['snap_token'],
            'payment_type' => 'midtrans',
            'transaction_time' => now()
        ]);

        // Hapus session checkout
        session()->forget('payment_checkout');

        Log::info('Transaction created from checkout', [
            'transaction_id' => $transaction->id,
            'user_id' => $userId,
            'rak_id' => $rakId,
            'order_id' => $checkoutData['order_id']
        ]);

        // Kirim response dengan snap token untuk Midtrans
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'snap_token' => $checkoutData['snap_token'],
            'transaction_id' => $transaction->id
        ]);

    } catch (\Exception $e) {
        Log::error('Process Payment Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

public function cancelCheckout()
{
    try {
        $checkoutData = session('payment_checkout');
        
        if ($checkoutData) {
            Log::info('User canceled checkout', [
                'user_id' => Auth::id(),
                'rak_id' => $checkoutData['rak_id'],
                'order_id' => $checkoutData['order_id']
            ]);
            
            session()->forget('payment_checkout');
        }

        return redirect()->route('customer.list-rak.list-rak')
            ->with('info', 'Checkout dibatalkan.');

    } catch (\Exception $e) {
        Log::error('Cancel Checkout Error: ' . $e->getMessage());
        return redirect()->route('customer.list-rak.list-rak');
    }
}

    /**
     * Update Status Transaksi (Dipanggil dari Frontend setelah pembayaran)
     */
    public function updateStatus(Request $request)
{
    try {
        $request->validate([
            'order_id' => 'required|string',
            'transaction_status' => 'required|string',
            'payment_type' => 'nullable|string',
            'transaction_id' => 'nullable|integer'
        ]);

        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $paymentType = $request->payment_type ?? null;
        $transactionId = $request->transaction_id;

        // Cari transaksi
        $transaction = null;
        if ($transactionId) {
            $transaction = Transaction::where('id', $transactionId)
                ->where('user_id', Auth::id())
                ->first();
        }
        
        if (!$transaction) {
            $transaction = Transaction::where('order_id', $orderId)
                ->where('user_id', Auth::id())
                ->first();
        }

        if (!$transaction) {
            Log::error('Transaction not found for update', ['order_id' => $orderId]);
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        // Update status transaksi
        $transaction->update([
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType,
            'transaction_time' => now()
        ]);

        Log::info('Transaction Status Updated from Tagihan', [
            'transaction_id' => $transaction->id,
            'status' => $transactionStatus
        ]);

        // Jika pembayaran sukses, update status rak
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $this->handleSuccessPayment($transaction);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate'
        ]);

    } catch (\Exception $e) {
        Log::error('Update Status Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal update status'
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
    public function handlePaymentReturn(Request $request)
{
    try {
        // Get data dari session
        $pendingData = session('pending_payment');
        
        if (!$pendingData) {
            return redirect()->route('customer.list-rak.list-rak')
                ->with('error', 'Sesi pembayaran tidak ditemukan. Silakan ulangi dari awal.');
        }

        // Ambil data dari request Midtrans (jika ada)
        $transactionStatus = $request->transaction_status ?? 'pending';
        $paymentType = $request->payment_type ?? 'midtrans';
        $orderId = $pendingData['order_id'];

        // ==============================================
        // SEKARANG BUAT TRANSAKSI DI DATABASE
        // ==============================================
        $transaction = Transaction::create([
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'rak_id' => $pendingData['rak_id'],
            'amount' => $pendingData['amount'],
            'transaction_status' => $transactionStatus,
            'snap_token' => $pendingData['snap_token'],
            'payment_type' => $paymentType,
            'transaction_time' => now()
        ]);

        // Hapus session
        session()->forget('pending_payment');

        Log::info('Transaction Created After Midtrans Popup', [
            'transaction_id' => $transaction->id,
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'status' => $transactionStatus,
            'rak_id' => $pendingData['rak_id']
        ]);

        // Redirect ke halaman tagihan
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'redirect_url' => route('customer.tagihan')
        ]);

    } catch (\Exception $e) {
        Log::error('Handle Payment Return Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Handle Pembayaran Sukses
     */
    private function handleSuccessPayment($transaction)
{
    $rak = Rak::find($transaction->rak_id);

    if ($rak && $rak->status === 'tersedia') {

        // ============================
        // SET TANGGAL MULAI & BERAKHIR
        // ============================
        $durasi = $rak->durasi_sewa_hari ?? 30; // default 30 hari jika tidak ada

        $transaction->sewa_mulai = now();
        $transaction->sewa_berakhir = now()->addDays($durasi);
        $transaction->save();

        Log::info('Durasi sewa dihitung', [
            'transaction_id' => $transaction->id,
            'sewa_mulai' => $transaction->sewa_mulai,
            'sewa_berakhir' => $transaction->sewa_berakhir,
            'durasi' => $durasi
        ]);

        // ============================
        // UBAH STATUS RAK
        // ============================
        $rak->update(['status' => 'terisi']);

        Log::info('Rak Status Updated to Terisi', [
            'rak_id' => $rak->id,
            'rak_name' => $rak->nama_rak,
            'transaction_id' => $transaction->id
        ]);

        // ============================
        // LOG HISTORY SEWA BARU
        // ============================
        try {
            HistoryService::logNewRental(
                $transaction->user_id,
                $rak->kode_rak ?? $rak->nama_rak,
                $durasi,
                $transaction->amount,
                'System'
            );
        } catch (\Exception $historyError) {
            Log::error('Failed to log new rental history: ' . $historyError->getMessage());
            }
        }

        try {
            $year = $transaction->transaction_time->year;
            $month = $transaction->transaction_time->month;

            RevenueService::generateMonthlyReport($year, $month);

            Log::info('Revenue report berhasil dibuat otomatis', [
                'transaction_id' => $transaction->id,
                'year' => $year,
                'month' => $month
            ]);
        } catch (\Exception $revenueError) {
            Log::error('Gagal membuat laporan pendapatan: ' . $revenueError->getMessage());
            }
        }

        /**
 * Callback khusus untuk transaksi perpanjangan
 */
public function renewalCallback(Request $request)
{
    try {
        $notification = new Notification();
        
        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        
        // Cari transaksi renewal
        $transaction = Transaction::where('order_id', $orderId)
            ->where('is_renewal', true)
            ->first();
            
        if (!$transaction) {
            Log::error('Renewal transaction not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        
        // Update status
        $transaction->update([
            'transaction_status' => $transactionStatus,
            'midtrans_response' => $notification->getResponse()
        ]);
        
        // Jika sukses, handle renewal
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $tagihanController = new TagihanController();
            $tagihanController->handleRenewalSuccess($transaction);
        }
        
        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        Log::error('Renewal Callback Error: ' . $e->getMessage());
        return response()->json(['message' => 'Error processing callback'], 500);
    }
}

/**
 * Update status untuk transaksi renewal
 */
public function updateRenewalStatus(Request $request)
{
    try {
        $request->validate([
            'order_id' => 'required|string',
            'transaction_status' => 'required|string'
        ]);

        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;

        // Cari transaksi renewal
        $transaction = Transaction::where('order_id', $orderId)
            ->where('is_renewal', true)
            ->first();

        if (!$transaction) {
            Log::error('Renewal transaction not found', ['order_id' => $orderId]);
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Update status
        $transaction->update(['transaction_status' => $transactionStatus]);

        // Jika pembayaran sukses
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $success = $transaction->handleRenewalSuccess();
            
            if ($success) {
                Log::info('Renewal payment successful', [
                    'transaction_id' => $transaction->id,
                    'original_transaction_id' => $transaction->parent_transaction_id
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status renewal berhasil diupdate'
        ]);

    } catch (\Exception $e) {
        Log::error('Update Renewal Status Error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal update status'], 500);
    }
}
    }
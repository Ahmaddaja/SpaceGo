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

public function renewal($transaction_id)
{
    try {
        // Ambil transaksi aktif
        $transaction = Transaction::where('id', $transaction_id)
            ->where('user_id', Auth::id())
            ->whereIn('transaction_status', ['settlement', 'capture'])
            ->firstOrFail();

        $rak = Rak::findOrFail($transaction->rak_id);

        // Hitung keterlambatan
        $now = now()->startOfDay();
        $end = \Carbon\Carbon::parse($transaction->sewa_berakhir)->startOfDay();
        $daysDiff = $now->diffInDays($end, false);

        // Hitung denda (Rp 50.000 per hari)
        $dendaPerHari = 50000;
        $totalDenda = $daysDiff < 0 ? abs($daysDiff) * $dendaPerHari : 0;
        
        // Total pembayaran
        $hargaSewa = $rak->harga_sewa_perbulan;
        $totalBayar = $hargaSewa + $totalDenda;

        // Generate Order ID baru untuk perpanjangan
        $orderId = 'RENEWAL-' . time() . '-' . $transaction->id;

        // Item details untuk Midtrans
        $itemDetails = [
            [
                'id' => 'rental-' . $rak->id,
                'price' => (int) $hargaSewa,
                'quantity' => 1,
                'name' => 'Perpanjangan Sewa ' . $rak->nama_rak
            ]
        ];

        // Tambahkan denda jika ada
        if ($totalDenda > 0) {
            $itemDetails[] = [
                'id' => 'penalty-' . $transaction->id,
                'price' => (int) $totalDenda,
                'quantity' => 1,
                'name' => 'Denda Keterlambatan (' . abs($daysDiff) . ' hari)'
            ];
        }

        // Parameter untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $totalBayar,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'custom_field1' => 'renewal', // Penanda bahwa ini perpanjangan
            'custom_field2' => $transaction->id, // ID transaksi yang diperpanjang
        ];

        // Generate Snap token
        $snapToken = Snap::getSnapToken($params);

        // Simpan transaksi perpanjangan
        $renewalTransaction = Transaction::create([
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'rak_id' => $rak->id,
            'amount' => $totalBayar,
            'transaction_status' => 'pending',
            'snap_token' => $snapToken,
            'transaction_time' => now(),
            'parent_transaction_id' => $transaction->id, // Link ke transaksi sebelumnya
            'penalty_amount' => $totalDenda,
            'is_renewal' => true
        ]);

        Log::info('Renewal Transaction Created', [
            'renewal_transaction_id' => $renewalTransaction->id,
            'parent_transaction_id' => $transaction->id,
            'order_id' => $orderId,
            'amount' => $totalBayar,
            'penalty' => $totalDenda,
            'days_late' => abs($daysDiff)
        ]);

        // Tampilkan halaman checkout perpanjangan
        return view('customer.payment.renewal-checkout', compact(
            'snapToken', 
            'rak', 
            'transaction',
            'renewalTransaction',
            'totalDenda',
            'daysDiff',
            'hargaSewa',
            'totalBayar'
        ));

    } catch (\Exception $e) {
        Log::error('Renewal Error: ' . $e->getMessage());
        Log::error('Stack Trace: ' . $e->getTraceAsString());

        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}


private function handleSuccessPayment($transaction)
{
    $rak = Rak::find($transaction->rak_id);

    if ($rak) {
        // Cek apakah ini transaksi perpanjangan
        if ($transaction->is_renewal && $transaction->parent_transaction_id) {
            // Ini adalah perpanjangan sewa
            $durasi = $rak->durasi_sewa_hari ?? 30;
            
            // Ambil transaksi parent
            $parentTransaction = Transaction::find($transaction->parent_transaction_id);
            
            if ($parentTransaction) {
                // Hitung tanggal mulai dari akhir sewa sebelumnya atau sekarang (mana yang lebih besar)
                $sewaMulai = max(
                    now(),
                    \Carbon\Carbon::parse($parentTransaction->sewa_berakhir)
                );
                
                $transaction->sewa_mulai = $sewaMulai;
                $transaction->sewa_berakhir = $sewaMulai->copy()->addDays($durasi);
                $transaction->save();

                Log::info('Renewal dates calculated', [
                    'transaction_id' => $transaction->id,
                    'parent_end' => $parentTransaction->sewa_berakhir,
                    'new_start' => $transaction->sewa_mulai,
                    'new_end' => $transaction->sewa_berakhir,
                    'duration' => $durasi
                ]);

                // Log history perpanjangan
                try {
                    HistoryService::logRenewalRental(
                        $transaction->user_id,
                        $rak->kode_rak ?? $rak->nama_rak,
                        $durasi,
                        $transaction->amount,
                        $transaction->penalty_amount ?? 0,
                        'System'
                    );
                } catch (\Exception $historyError) {
                    Log::error('Failed to log renewal history: ' . $historyError->getMessage());
                }
            }
        } else {
            // Ini adalah sewa baru (kode lama)
            $durasi = $rak->durasi_sewa_hari ?? 30;

            $transaction->sewa_mulai = now();
            $transaction->sewa_berakhir = now()->addDays($durasi);
            $transaction->save();

            Log::info('Durasi sewa dihitung', [
                'transaction_id' => $transaction->id,
                'sewa_mulai' => $transaction->sewa_mulai,
                'sewa_berakhir' => $transaction->sewa_berakhir,
                'durasi' => $durasi
            ]);

            // Ubah status rak menjadi terisi (hanya untuk sewa baru)
            if ($rak->status === 'tersedia') {
                $rak->update(['status' => 'terisi']);

                Log::info('Rak Status Updated to Terisi', [
                    'rak_id' => $rak->id,
                    'rak_name' => $rak->nama_rak,
                    'transaction_id' => $transaction->id
                ]);
            }

            // Log history sewa baru
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

        // Generate revenue report
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
}}
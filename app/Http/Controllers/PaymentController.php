<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\Rak;
use App\Models\Transaction;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\HistoryService;
use App\Services\RevenueService;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    // app/Http/Controllers/PaymentController.php

    public function bayar($id)
    {
        try {
            $rak = Rak::findOrFail($id);
            $userId = Auth::id();

            // Cek transaksi pending untuk rak ini (dari user manapun)
            $existingPendingTransaction = Transaction::where('rak_id', $rak->id)
                ->where('transaction_status', 'pending')
                ->exists();

            if ($existingPendingTransaction) {
                return redirect()->route('customer.list-rak.list-rak')
                    ->with('error', 'Rak ini sedang dalam proses pembayaran oleh user lain. Silakan pilih rak lain.');
            }

            // Cek apakah user sudah punya pending untuk rak ini
            $userPendingTagihan = Tagihan::where('user_id', $userId)
                ->where('rak_id', $rak->id)
                ->where('status', 'pending')
                ->first();

            if ($userPendingTagihan) {
                return redirect()->route('customer.tagihan')
                    ->with('info', 'Anda sudah memiliki tagihan pending untuk rak ini.');
            }

            if ($rak->status !== 'tersedia') {
                return redirect()->route('customer.list-rak.list-rak')
                    ->with('error', 'Rak tidak tersedia untuk disewa.');
            }

            // ... kode selanjutnya tetap sama

            $orderId = 'ORDER-' . time() . '-' . $rak->id;

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
                ],
                'callbacks' => [
                    'finish' => route('customer.list-rak.rak')
                ]
            ];

            $snapToken = Snap::getSnapToken($params);

            session([
                'payment_checkout' => [
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
            $checkoutData = session('payment_checkout');

            if (!$checkoutData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi checkout tidak ditemukan. Silakan ulangi dari awal.'
                ], 400);
            }

            $userId = Auth::id();
            $rakId = $checkoutData['rak_id'];

            $existingPendingTagihan = Tagihan::where('user_id', $userId)
                ->where('rak_id', $rakId)
                ->where('status', 'pending')
                ->first();

            if ($existingPendingTagihan) {
                session()->forget('payment_checkout');

                return response()->json([
                    'success' => false,
                    'message' => 'Sudah ada tagihan pending untuk rak ini.',
                    'redirect_url' => route('customer.tagihan')
                ], 400);
            }

            // ✅ FIX: Ambil data rak untuk durasi sewa
            $rak = Rak::findOrFail($rakId);
            $durasi = $rak->durasi_sewa_hari ?? 30;

            // ✅ FIX: Hitung sewa_mulai dan sewa_berakhir
            $sewaMulai = now();
            $sewaBerakhir = now()->addDays($durasi);

            $transaction = Transaction::create([
                'order_id' => $checkoutData['order_id'],
                'user_id' => $userId,
                'rak_id' => $rakId,
                'amount' => $checkoutData['amount'],
                'transaction_status' => 'pending',
                'snap_token' => $checkoutData['snap_token'],
                'payment_type' => 'midtrans',
                'transaction_time' => now(),
                'sewa_mulai' => $sewaMulai,           // ✅ FIX: Tambahkan ini
                'sewa_berakhir' => $sewaBerakhir,     // ✅ FIX: Tambahkan ini
            ]);

            session()->forget('payment_checkout');

            Log::info('Transaction & Tagihan auto-created from checkout', [
                'transaction_id' => $transaction->id,
                'user_id' => $userId,
                'rak_id' => $rakId,
                'order_id' => $checkoutData['order_id'],
                'sewa_mulai' => $sewaMulai->format('Y-m-d H:i:s'),
                'sewa_berakhir' => $sewaBerakhir->format('Y-m-d H:i:s'),
                'durasi' => $durasi
            ]);

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

    public function updateStatus(Request $request)
    {
        try {
            Log::info('Update Status Request Received', [
                'request_data' => $request->all()
            ]);

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

            // Cari transaction
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
                Log::error('Transaction not found', [
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            Log::info('Transaction found, updating...', [
                'transaction_id' => $transaction->id,
                'old_status' => $transaction->transaction_status,
                'new_status' => $transactionStatus
            ]);

            // Update transaction
            $transaction->update([
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_time' => now()
            ]);

            Log::info('Transaction updated successfully', [
                'transaction_id' => $transaction->id,
                'new_status' => $transaction->transaction_status
            ]);

            // ✅ SYNC KE TAGIHAN
            $tagihan = \App\Models\Tagihan::where('transaction_id', $transaction->id)->first();
            if ($tagihan) {
                $tagihan->update([
                    'status' => $transactionStatus,
                    'paid_at' => in_array($transactionStatus, ['capture', 'settlement']) ? now() : null
                ]);

                Log::info('Tagihan synced', [
                    'tagihan_id' => $tagihan->id,
                    'status' => $transactionStatus
                ]);
            } else {
                Log::warning('Tagihan not found for transaction', [
                    'transaction_id' => $transaction->id
                ]);
            }

            // Handle success payment
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                Log::info('Handling success payment...');
                $this->handleSuccessPayment($transaction);
            }

            Log::info('Update Status Complete', [
                'transaction_id' => $transaction->id,
                'final_status' => $transaction->transaction_status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'status' => $transaction->transaction_status,
                    'updated_at' => $transaction->updated_at
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update Status Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
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

            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::error('Transaction not found in callback', ['order_id' => $orderId]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            DB::beginTransaction();

            try {
                $transaction->update([
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus,
                    'payment_type' => $paymentType,
                    'midtrans_response' => method_exists($notification, 'getResponse') ? $notification->getResponse() : json_encode($notification)
                ]);

                Log::info('Transaction & Tagihan Updated from Callback', [
                    'transaction_id' => $transaction->id,
                    'status' => $transactionStatus
                ]);

                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $this->handleSuccessPayment($transaction);

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
            $pendingData = session('pending_payment');

            if (!$pendingData) {
                return redirect()->route('customer.list-rak.list-rak')
                    ->with('error', 'Sesi pembayaran tidak ditemukan. Silakan ulangi dari awal.');
            }

            $transactionStatus = $request->transaction_status ?? 'pending';
            $paymentType = $request->payment_type ?? 'midtrans';
            $orderId = $pendingData['order_id'];

            // ✅ FIX: Ambil data rak untuk durasi sewa
            $rak = Rak::findOrFail($pendingData['rak_id']);
            $durasi = $rak->durasi_sewa_hari ?? 30;

            // ✅ FIX: Hitung sewa_mulai dan sewa_berakhir
            $sewaMulai = now();
            $sewaBerakhir = now()->addDays($durasi);

            $transaction = Transaction::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'rak_id' => $pendingData['rak_id'],
                'amount' => $pendingData['amount'],
                'transaction_status' => $transactionStatus,
                'snap_token' => $pendingData['snap_token'],
                'payment_type' => $paymentType,
                'transaction_time' => now(),
                'sewa_mulai' => $sewaMulai,           // ✅ FIX: Tambahkan ini
                'sewa_berakhir' => $sewaBerakhir,     // ✅ FIX: Tambahkan ini
            ]);

            session()->forget('pending_payment');

            Log::info('Transaction & Tagihan Created After Midtrans Popup', [
                'transaction_id' => $transaction->id,
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'status' => $transactionStatus,
                'rak_id' => $pendingData['rak_id'],
                'sewa_mulai' => $sewaMulai->format('Y-m-d H:i:s'),
                'sewa_berakhir' => $sewaBerakhir->format('Y-m-d H:i:s'),
                'durasi' => $durasi
            ]);

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

    public function renewal($transaction_id)
    {
        try {
            $transaction = Transaction::where('id', $transaction_id)
                ->where('user_id', Auth::id())
                ->whereIn('transaction_status', ['settlement', 'capture'])
                ->firstOrFail();

            // BLOKIR JIKA SEDANG PENGOSONGAN
            if ($transaction->is_pengosongan) {
                Log::warning('Renewal blocked: Transaction in pengosongan period', [
                    'transaction_id' => $transaction->id,
                    'user_id' => Auth::id()
                ]);

                return redirect()->back()
                    ->with('error', 'Anda tidak bisa membayar atau memperpanjang masa sewa lagi karena rak sudah memasuki masa pengosongan.');
            }

            // CEK APAKAH AKAN MASUK PENGOSONGAN (dengan datetime precision)
            $currentDbTime = DB::selectOne('SELECT NOW() as db_time')->db_time;
            $now = \Carbon\Carbon::parse($currentDbTime);
            $end = \Carbon\Carbon::parse($transaction->sewa_berakhir);

            // Hitung dalam menit untuk akurasi lebih tinggi
            $minutesDiff = $now->diffInMinutes($end, false);
            $daysDiff = $now->diffInDays($end, false);

            $gracePeriodDays = 3;
            $maxLateDays = 30;

            $totalLateDays = 0;
            if ($daysDiff < 0) {
                $totalLateDays = abs($daysDiff) - $gracePeriodDays;
            }

            $isEnteringPengosongan = $daysDiff < 0 && $totalLateDays >= $maxLateDays;

            if ($isEnteringPengosongan) {
                Log::warning('Renewal blocked: Transaction entering pengosongan', [
                    'transaction_id' => $transaction->id,
                    'user_id' => Auth::id(),
                    'total_late_days' => $totalLateDays
                ]);

                return redirect()->back()
                    ->with('error', 'Anda tidak bisa membayar atau memperpanjang masa sewa lagi karena rak akan memasuki masa pengosongan.');
            }

            $rak = Rak::findOrFail($transaction->rak_id);

            $dendaPerHari = 50000;
            $totalDenda = 0;

            if ($daysDiff < 0 && abs($daysDiff) > $gracePeriodDays) {
                $latenessDays = abs($daysDiff) - $gracePeriodDays;
                $totalDenda = $latenessDays * $dendaPerHari;
            }

            $hargaSewa = $rak->harga_sewa_perbulan;
            $totalBayar = $hargaSewa + $totalDenda;

            $orderId = 'RENEWAL-' . time() . '-' . $transaction->id;

            $itemDetails = [
                [
                    'id' => 'rental-' . $rak->id,
                    'price' => (int) $hargaSewa,
                    'quantity' => 1,
                    'name' => 'Perpanjangan Sewa ' . $rak->nama_rak
                ]
            ];

            if ($totalDenda > 0) {
                $latenessDays = abs($daysDiff) - $gracePeriodDays;
                $itemDetails[] = [
                    'id' => 'penalty-' . $transaction->id,
                    'price' => (int) $totalDenda,
                    'quantity' => 1,
                    'name' => 'Denda Keterlambatan (' . $latenessDays . ' hari)'
                ];
            }

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
                'custom_field1' => 'renewal',
                'custom_field2' => $transaction->id,
            ];

            $snapToken = Snap::getSnapToken($params);

            $transaction->update([
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'penalty_amount' => $totalDenda,
                'is_renewal' => true
            ]);

            // ✅ FIX: Ambil atau buat tagihan terkait
            $tagihan = Tagihan::where('transaction_id', $transaction->id)->first();

            // Jika tidak ada tagihan (misal dari transaksi lama), buat data sementara untuk view
            if (!$tagihan) {
                $tagihan = (object) [
                    'sewa_berakhir' => $transaction->sewa_berakhir,
                    'total_tagihan' => $totalBayar,
                    'harga_sewa' => $hargaSewa,
                    'penalty_amount' => $totalDenda,
                ];
            }

            Log::info('Renewal Snap Token Generated (Tagihan auto-synced)', [
                'transaction_id' => $transaction->id,
                'order_id' => $orderId,
                'amount' => $totalBayar,
                'penalty' => $totalDenda,
                'days_diff' => $daysDiff,
                'minutes_diff' => $minutesDiff
            ]);

            return view('customer.payment.renewal-checkout', compact(
                'snapToken',
                'rak',
                'transaction',
                'tagihan',
                'totalDenda',
                'daysDiff',
                'hargaSewa',
                'totalBayar',
                'gracePeriodDays'
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
            if ($transaction->is_renewal) {
                $durasi = $rak->durasi_sewa_hari ?? 30;

                $sewaMulai = max(
                    now(),
                    Carbon::parse($transaction->sewa_berakhir)
                );

                $sewaBerahir = $sewaMulai->copy()->addDays($durasi);

                // ✅ UPDATE TRANSACTION
                $transaction->update([
                    'sewa_mulai' => $sewaMulai,
                    'sewa_berakhir' => $sewaBerahir
                ]);

                // ✅ SYNC KE TAGIHAN
                $tagihan = Tagihan::where('transaction_id', $transaction->id)->first();
                if ($tagihan) {
                    $tagihan->update([
                        'sewa_mulai' => $sewaMulai,
                        'sewa_berakhir' => $sewaBerahir,
                        'status' => 'settlement',
                        'paid_at' => now()
                    ]);

                    Log::info('Tagihan dates synced (renewal)', [
                        'tagihan_id' => $tagihan->id,
                        'sewa_mulai' => $sewaMulai->format('Y-m-d H:i:s'),
                        'sewa_berakhir' => $sewaBerahir->format('Y-m-d H:i:s')
                    ]);
                }

                Log::info('Renewal dates calculated', [
                    'transaction_id' => $transaction->id,
                    'new_start' => $sewaMulai->format('Y-m-d H:i:s'),
                    'new_end' => $sewaBerahir->format('Y-m-d H:i:s'),
                    'duration' => $durasi
                ]);

                try {
                    HistoryService::logRentalExtension(
                        $transaction->user_id,
                        $rak->kode_rak ?? $rak->nama_rak,
                        $durasi,
                        $transaction->penalty_amount ?? 0,
                        'System'
                    );
                } catch (\Exception $historyError) {
                    Log::error('Failed to log renewal history: ' . $historyError->getMessage());
                }
            } else {
                $durasi = $rak->durasi_sewa_hari ?? 30;

                $sewaMulai = now();
                $sewaBerahir = now()->addDays($durasi);

                // ✅ UPDATE TRANSACTION
                $transaction->update([
                    'sewa_mulai' => $sewaMulai,
                    'sewa_berakhir' => $sewaBerahir
                ]);

                // ✅ SYNC KE TAGIHAN (INI YANG PENTING!)
                $tagihan = Tagihan::where('transaction_id', $transaction->id)->first();
                if ($tagihan) {
                    $tagihan->update([
                        'sewa_mulai' => $sewaMulai,
                        'sewa_berakhir' => $sewaBerahir,
                        'status' => 'settlement',
                        'paid_at' => now()
                    ]);

                    Log::info('Tagihan dates synced (new rental)', [
                        'tagihan_id' => $tagihan->id,
                        'sewa_mulai' => $sewaMulai->format('Y-m-d H:i:s'),
                        'sewa_berakhir' => $sewaBerahir->format('Y-m-d H:i:s')
                    ]);
                } else {
                    Log::error('Tagihan not found for sync', [
                        'transaction_id' => $transaction->id
                    ]);
                }

                Log::info('Durasi sewa dihitung', [
                    'transaction_id' => $transaction->id,
                    'sewa_mulai' => $sewaMulai->format('Y-m-d H:i:s'),
                    'sewa_berakhir' => $sewaBerahir->format('Y-m-d H:i:s'),
                    'durasi' => $durasi
                ]);

                if ($rak->status === 'tersedia') {
                    $rak->update(['status' => 'terisi']);

                    Log::info('Rak Status Updated to Terisi', [
                        'rak_id' => $rak->id,
                        'rak_name' => $rak->nama_rak,
                        'transaction_id' => $transaction->id
                    ]);
                }

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
    }

    public function renewalCallback(Request $request)
    {
        try {
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;

            $transaction = Transaction::where('order_id', $orderId)
                ->where('is_renewal', true)
                ->first();

            if (!$transaction) {
                Log::error('Renewal transaction not found', ['order_id' => $orderId]);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $transaction->update([
                'transaction_status' => $transactionStatus,
                'midtrans_response' => method_exists($notification, 'getResponse') ? $notification->getResponse() : json_encode($notification)
            ]);

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $this->handleSuccessPayment($transaction);

                try {
                    HistoryService::logPaymentSuccess(
                        $transaction->user_id,
                        $transaction->amount,
                        $transaction->id,
                        $notification->payment_type ?? 'Midtrans',
                        'System'
                    );
                } catch (\Exception $historyError) {
                    Log::error('Failed to log payment history for renewal callback: ' . $historyError->getMessage());
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Renewal Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing callback'], 500);
        }
    }

    public function updateRenewalStatus(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|string',
                'transaction_status' => 'required|string'
            ]);

            $orderId = $request->order_id;
            $transactionStatus = $request->transaction_status;

            $transaction = Transaction::where('order_id', $orderId)
                ->where('is_renewal', true)
                ->where('user_id', Auth::id())
                ->first();

            if (!$transaction) {
                Log::error('Renewal transaction not found', ['order_id' => $orderId]);
                return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
            }

            $transaction->update(['transaction_status' => $transactionStatus]);

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $this->handleSuccessPayment($transaction);

                Log::info('Renewal payment successful (Tagihan auto-synced)', [
                    'transaction_id' => $transaction->id
                ]);
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

    public function autoCheckExpiredRentals()
    {
        try {
            $currentDbTime = DB::selectOne('SELECT NOW() as db_time')->db_time;
            $now = \Carbon\Carbon::parse($currentDbTime);

            // Ambil semua transaksi aktif yang belum dikosongkan
            $expiredTransactions = Transaction::whereIn('transaction_status', ['settlement', 'capture'])
                ->where('is_dikosongkan', false)
                ->whereNotNull('sewa_berakhir')
                ->get();

            $updatedCount = 0;

            foreach ($expiredTransactions as $transaction) {
                $end = \Carbon\Carbon::parse($transaction->sewa_berakhir);
                $daysPassed = $now->diffInDays($end, false);

                // Jika sudah lewat 37 hari
                if ($daysPassed < -37) {
                    DB::beginTransaction();

                    try {
                        $rak = Rak::find($transaction->rak_id);

                        if ($rak && in_array($rak->status, ['terisi', 'pengosongan'])) {
                            // Update status rak menjadi tersedia
                            $rak->update(['status' => 'tersedia']);

                            // Tandai transaksi sebagai sudah dikosongkan
                            $transaction->update([
                                'is_dikosongkan' => true,
                                'dikosongkan_at' => $now
                            ]);

                            $updatedCount++;

                            Log::info('Auto-check: Rak dikosongkan otomatis', [
                                'rak_id' => $rak->id,
                                'rak_name' => $rak->nama_rak,
                                'transaction_id' => $transaction->id,
                                'days_passed' => abs($daysPassed),
                                'user_id' => $transaction->user_id
                            ]);

                            // Optional: Log ke history
                            try {
                                HistoryService::logRakEmptied(
                                    $transaction->user_id,
                                    $rak->kode_rak ?? $rak->nama_rak,
                                    abs($daysPassed),
                                    'System Auto-Check'
                                );
                            } catch (\Exception $historyError) {
                                Log::error('Failed to log rak emptied history: ' . $historyError->getMessage());
                            }
                        }

                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Error auto-checking expired rental: ' . $e->getMessage(), [
                            'transaction_id' => $transaction->id
                        ]);
                    }
                }
            }

            Log::info("Auto-check completed: {$updatedCount} rak(s) dikosongkan", [
                'checked_count' => $expiredTransactions->count(),
                'updated_count' => $updatedCount,
                'timestamp' => $now->format('Y-m-d H:i:s')
            ]);

            return response()->json([
                'success' => true,
                'message' => "Auto-check completed: {$updatedCount} rak(s) dikosongkan",
                'checked' => $expiredTransactions->count(),
                'updated' => $updatedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Auto-check expired rentals error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error during auto-check: ' . $e->getMessage()
            ], 500);
        }
    }
}

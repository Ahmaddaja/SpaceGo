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

    public function bayar($id)
    {
        try {
            $rak = Rak::findOrFail($id);
            $userId = Auth::id();

            $existingPendingTransaction = Transaction::where('user_id', $userId)
                ->where('rak_id', $rak->id)
                ->where('transaction_status', 'pending')
                ->first();

            if ($existingPendingTransaction) {
                return redirect()->route('customer.tagihan')
                    ->with('info', 'Anda sudah memiliki transaksi pending untuk rak ini. Silakan selesaikan pembayaran di halaman Tagihan.');
            }

            if ($rak->status !== 'tersedia') {
                return redirect()->route('customer.list-rak.list-rak')
                    ->with('error', 'Rak tidak tersedia untuk disewa.');
            }

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

            $existingPendingTransaction = Transaction::where('user_id', $userId)
                ->where('rak_id', $rakId)
                ->where('transaction_status', 'pending')
                ->first();

            if ($existingPendingTransaction) {
                session()->forget('payment_checkout');

                return response()->json([
                    'success' => false,
                    'message' => 'Sudah ada transaksi pending untuk rak ini.',
                    'redirect_url' => route('customer.tagihan')
                ], 400);
            }

            $transaction = Transaction::create([
                'order_id' => $checkoutData['order_id'],
                'user_id' => $userId,
                'rak_id' => $rakId,
                'amount' => $checkoutData['amount'],
                'transaction_status' => 'pending',
                'snap_token' => $checkoutData['snap_token'],
                'payment_type' => 'midtrans',
                'transaction_time' => now()
            ]);

            session()->forget('payment_checkout');

            Log::info('Transaction created from checkout', [
                'transaction_id' => $transaction->id,
                'user_id' => $userId,
                'rak_id' => $rakId,
                'order_id' => $checkoutData['order_id']
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

            $transaction->update([
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_time' => now()
            ]);

            Log::info('Transaction Status Updated from Tagihan', [
                'transaction_id' => $transaction->id,
                'status' => $transactionStatus
            ]);

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

                Log::info('Transaction Updated from Callback', [
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

            session()->forget('pending_payment');

            Log::info('Transaction Created After Midtrans Popup', [
                'transaction_id' => $transaction->id,
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'status' => $transactionStatus,
                'rak_id' => $pendingData['rak_id']
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

            $rak = Rak::findOrFail($transaction->rak_id);

            $now = now()->startOfDay();
            $end = \Carbon\Carbon::parse($transaction->sewa_berakhir)->startOfDay();
            $daysDiff = $now->diffInDays($end, false);

            $dendaPerHari = 50000;
            $totalDenda = $daysDiff < 0 ? abs($daysDiff) * $dendaPerHari : 0;

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
                $itemDetails[] = [
                    'id' => 'penalty-' . $transaction->id,
                    'price' => (int) $totalDenda,
                    'quantity' => 1,
                    'name' => 'Denda Keterlambatan (' . abs($daysDiff) . ' hari)'
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

            Log::info('Renewal Snap Token Generated', [
                'transaction_id' => $transaction->id,
                'order_id' => $orderId,
                'amount' => $totalBayar,
                'penalty' => $totalDenda,
                'days_late' => abs($daysDiff)
            ]);

            return view('customer.payment.renewal-checkout', compact(
                'snapToken',
                'rak',
                'transaction',
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
            if ($transaction->is_renewal) {
                $durasi = $rak->durasi_sewa_hari ?? 30;

                $sewaMulai = max(
                    now(),
                    \Carbon\Carbon::parse($transaction->sewa_berakhir)
                );

                $transaction->sewa_mulai = $sewaMulai;
                $transaction->sewa_berakhir = $sewaMulai->copy()->addDays($durasi);
                $transaction->save();

                Log::info('Renewal dates calculated', [
                    'transaction_id' => $transaction->id,
                    'new_start' => $transaction->sewa_mulai,
                    'new_end' => $transaction->sewa_berakhir,
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

                $transaction->sewa_mulai = now();
                $transaction->sewa_berakhir = now()->addDays($durasi);
                $transaction->save();

                Log::info('Durasi sewa dihitung', [
                    'transaction_id' => $transaction->id,
                    'sewa_mulai' => $transaction->sewa_mulai,
                    'sewa_berakhir' => $transaction->sewa_berakhir,
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

                Log::info('Renewal payment successful', [
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
}
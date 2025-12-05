<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagihanController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        $userId = Auth::id();
        
        if (session('payment_checkout')) {
            session()->forget('payment_checkout');
        }
        
        Transaction::where('user_id', $userId)
            ->where('transaction_status', 'pending')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->update(['transaction_status' => 'expired']);
        
        $pendingTransactions = Transaction::with('rak')
            ->where('user_id', $userId)
            ->where('transaction_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) {
                return $item->rak_id;
            });

        $expiredTransactions = Transaction::with('rak')
            ->where('user_id', $userId)
            ->where('transaction_status', 'expired')
            ->orderBy('created_at', 'desc')
            ->get();

        $rakIdsWithRenewal = Transaction::where('user_id', $userId)
            ->where('is_renewal', true)
            ->whereIn('transaction_status', ['pending', 'settlement','expired','overdue'])
            ->pluck('rak_id')
            ->unique()
            ->toArray();

        $now = Carbon::now();
        $oneDayFromNow = Carbon::now()->addDay();
        
        $overdueTransactions = Transaction::with('rak')
            ->where('user_id', $userId)
            ->where('transaction_status', 'settlement')
            ->where('is_renewal', false)
            ->whereNotIn('rak_id', $rakIdsWithRenewal)
            ->where(function($query) use ($now, $oneDayFromNow) {
                $query->whereDate('sewa_berakhir', '<', $now)
                      ->orWhere(function($q) use ($now, $oneDayFromNow) {
                          $q->whereDate('sewa_berakhir', '>=', $now)
                            ->whereDate('sewa_berakhir', '<=', $oneDayFromNow);
                      });
            })
            ->orderBy('sewa_berakhir', 'asc')
            ->get();

        return view('customer.tagihan.index', compact(
            'pendingTransactions', 
            'expiredTransactions', 
            'overdueTransactions'
        ));
    }

    public function createPayment($id)
    {
        try {
            $originalTransaction = Transaction::with('rak')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $rak = $originalTransaction->rak;
            if (!$rak) {
                return redirect()->route('customer.tagihan')
                    ->with('error', 'Rak tidak ditemukan.');
            }

            // Cek apakah sudah ada renewal aktif
            $existingRenewal = Transaction::where('rak_id', $rak->id)
                ->where('user_id', Auth::id())
                ->where('is_renewal', true)
                ->whereNotIn('transaction_status', ['expired', 'failed', 'cancel', 'overdue'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingRenewal) {
                if ($existingRenewal->snap_token) {
                    // Hitung ulang denda untuk tampilan - DISESUAIKAN DENGAN SHOW.BLADE.PHP
                    $sewaBerahir = Carbon::parse($originalTransaction->sewa_berakhir)->startOfDay();
                    $now = Carbon::now()->startOfDay();
                    
                    // daysDiff positif = masih ada sisa hari, negatif = sudah lewat
                    $daysDiff = $now->diffInDays($sewaBerahir, false);
                    
                    $gracePeriodDays = 3;
                    $dendaPerHari = 50000;
                    $totalDenda = 0;
                    
                    // Denda hanya dihitung jika sudah melewati masa tenggang
                    if ($daysDiff < 0 && abs($daysDiff) > $gracePeriodDays) {
                        $latenessDays = abs($daysDiff) - $gracePeriodDays;
                        $totalDenda = $latenessDays * $dendaPerHari;
                    }
                    
                    return view('customer.payment.renewal-checkout', [
                        'snapToken' => $existingRenewal->snap_token,
                        'rak' => $existingRenewal->rak,
                        'transaction' => $originalTransaction,
                        'daysDiff' => $daysDiff,
                        'totalDenda' => $totalDenda,
                        'hargaSewa' => $rak->harga_sewa_perbulan,
                        'totalBayar' => $rak->harga_sewa_perbulan + $totalDenda,
                        'gracePeriodDays' => $gracePeriodDays,
                    ]);
                }

                $snapToken = Snap::getSnapToken([
                    'transaction_details' => [
                        'order_id' => $existingRenewal->order_id,
                        'gross_amount' => (int) $existingRenewal->amount,
                    ],
                    'customer_details' => [
                        'first_name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ],
                ]);

                $existingRenewal->update(['snap_token' => $snapToken]);

                return view('customer.payment.renewal-checkout', [
                    'snapToken' => $snapToken,
                    'rak' => $existingRenewal->rak,
                    'transaction' => $originalTransaction
                ]);
            }

            // Buat renewal baru
            if (!in_array($originalTransaction->transaction_status, ['settlement', 'expired'])) {
                return redirect()->route('customer.tagihan')
                    ->with('error', 'Transaksi tidak dapat diperpanjang saat ini.');
            }

            $orderId = 'RENEW-' . time() . '-' . $rak->id . '-' . $originalTransaction->id;
            $durasi = $rak->durasi_sewa_hari ?? 30;
            $hargaSewa = $rak->harga_sewa_perbulan;

            // LOGIKA DENDA DISESUAIKAN DENGAN SHOW.BLADE.PHP
            $sewaBerahir = Carbon::parse($originalTransaction->sewa_berakhir)->startOfDay();
            $now = Carbon::now()->startOfDay();
            
            // daysDiff positif (+) = masih ada sisa hari
            // daysDiff nol (0) = hari terakhir
            // daysDiff negatif (-) = sudah lewat
            $daysDiff = $now->diffInDays($sewaBerahir, false);
            
            $gracePeriodDays = 3;
            $dendaPerHari = 50000;
            $totalDenda = 0;
            
            // Denda HANYA dihitung jika sudah melewati masa tenggang
            // Contoh: sewa_berakhir = 1 Jan, now = 6 Jan
            // daysDiff = -5 (lewat 5 hari)
            // abs(-5) = 5 > 3 (grace period), maka denda = (5-3) * 50000 = 100000
            if ($daysDiff < 0 && abs($daysDiff) > $gracePeriodDays) {
                $latenessDays = abs($daysDiff) - $gracePeriodDays;
                $totalDenda = $latenessDays * $dendaPerHari;
            }

            $totalBayar = $hargaSewa + $totalDenda;

            // Midtrans item details
            $itemDetails = [
                [
                    'id' => $rak->id . '-RENEW',
                    'price' => (int) $hargaSewa,
                    'quantity' => 1,
                    'name' => 'Perpanjangan Sewa - ' . $rak->nama_rak
                ]
            ];

            // Item denda hanya ditambahkan jika totalDenda > 0
            if ($totalDenda > 0) {
                $latenessDays = abs($daysDiff) - $gracePeriodDays;
                $itemDetails[] = [
                    'id' => 'PENALTY-' . $originalTransaction->id,
                    'price' => (int) $totalDenda,
                    'quantity' => 1,
                    'name' => 'Denda Keterlambatan (' . $latenessDays . ' hari)'
                ];
            }

            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $totalBayar,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'custom_field1' => $originalTransaction->id,
                'custom_field2' => 'renewal'
            ]);

            $sewaMulaiBaru = max(now(), Carbon::parse($originalTransaction->sewa_berakhir));
            $sewaberakhirBaru = $sewaMulaiBaru->copy()->addDays($durasi);

            Transaction::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'rak_id' => $rak->id,
                'amount' => $totalBayar,
                'transaction_status' => 'pending',
                'snap_token' => $snapToken,
                'transaction_time' => now(),
                'sewa_mulai' => $sewaMulaiBaru,
                'sewa_berakhir' => $sewaberakhirBaru,
                'parent_transaction_id' => $originalTransaction->id,
                'is_renewal' => true,
                'penalty_amount' => $totalDenda
            ]);

            Log::info('Renewal payment created', [
                'order_id' => $orderId,
                'days_diff' => $daysDiff,
                'grace_period' => $gracePeriodDays,
                'penalty' => $totalDenda,
                'total' => $totalBayar
            ]);

            return view('customer.payment.renewal-checkout', [
                'snapToken' => $snapToken,
                'rak' => $rak,
                'transaction' => $originalTransaction,
                'daysDiff' => $daysDiff,
                'totalDenda' => $totalDenda,
                'hargaSewa' => $hargaSewa,
                'totalBayar' => $totalBayar,
                'gracePeriodDays' => $gracePeriodDays,
            ]);

        } catch (\Exception $e) {
            Log::error('Renewal Payment Error: ' . $e->getMessage());

            return redirect()->route('customer.tagihan')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function createRenewal(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id'
        ]);

        $originalTransaction = Transaction::find($request->transaction_id);

        if ($originalTransaction->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $existingRenewal = Transaction::where('rak_id', $originalTransaction->rak_id)
            ->where('user_id', Auth::id())
            ->where('is_renewal', true)
            ->whereNotIn('transaction_status', ['expired', 'failed', 'cancel', 'deny'])
            ->exists();

        if ($existingRenewal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki permintaan perpanjangan aktif untuk rak ini.'
            ], 400);
        }

        $rak = $originalTransaction->rak;
        $durasi = $rak->durasi_sewa_hari ?? 30;

        $sewaMulaiBaru = max(
            now(),
            Carbon::parse($originalTransaction->sewa_berakhir)
        );
        $sewaberakhirBaru = $sewaMulaiBaru->copy()->addDays($durasi);

        $pricePerMonth = $rak->harga_sewa_perbulan;
        $priceForDuration = ($pricePerMonth / 30) * $durasi;
        $hargaSewa = ceil($priceForDuration);

        $renewal = Transaction::create([
            'order_id' => 'RENEW-' . time() . '-' . $rak->id,
            'user_id' => Auth::id(),
            'rak_id' => $originalTransaction->rak_id,
            'transaction_status' => 'pending',
            'sewa_mulai' => $sewaMulaiBaru,
            'sewa_berakhir' => $sewaberakhirBaru,
            'amount' => $hargaSewa,
            'is_renewal' => true,
            'parent_transaction_id' => $originalTransaction->id,
            'transaction_time' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan perpanjangan berhasil dibuat!',
            'data' => $renewal
        ]);
    }

    public function checkStatus($id)
    {
        try {
            $transaction = Transaction::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'status' => $transaction->transaction_status,
                    'amount' => number_format($transaction->amount, 0, ',', '.'),
                    'sewa_berakhir' => $transaction->sewa_berakhir 
                        ? $transaction->sewa_berakhir->format('d M Y') 
                        : '-',
                    'is_renewal' => $transaction->is_renewal,
                    'created_at' => $transaction->created_at->format('d M Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    public function processExpired(Request $request, $id)
    {
        try {
            $transaction = Transaction::with('rak')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            DB::beginTransaction();

            $transaction->update([
                'transaction_status' => 'expired',
                'updated_at' => now()
            ]);

            if ($transaction->rak) {
                $transaction->rak->update([
                    'status' => 'tersedia',
                    'updated_at' => now()
                ]);
            }

            Transaction::where('rak_id', $transaction->rak_id)
                ->where('user_id', Auth::id())
                ->where('is_renewal', true)
                ->where('transaction_status', 'pending')
                ->update([
                    'transaction_status' => 'expired',
                    'updated_at' => now()
                ]);

            DB::commit();

            Log::info('Rak berhasil dilepas secara manual', [
                'transaction_id' => $transaction->id,
                'rak_id' => $transaction->rak_id,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rak berhasil dilepas dan status diubah menjadi kadaluarsa.',
                    'data' => [
                        'transaction_id' => $transaction->id,
                        'transaction_status' => $transaction->transaction_status,
                        'rak_status' => $transaction->rak ? $transaction->rak->status : null,
                        'timestamp' => now()->format('Y-m-d H:i:s')
                    ]
                ]);
            }

            return redirect()->route('customer.tagihan')
                ->with('success', 'Rak berhasil dilepas dan status diubah menjadi kadaluarsa.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Transaksi tidak ditemukan saat melepas rak: ' . $e->getMessage(), [
                'transaction_id' => $id,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan.'
                ], 404);
            }

            return redirect()->route('customer.tagihan')
                ->with('error', 'Transaksi tidak ditemukan.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Gagal melepas rak: ' . $e->getMessage(), [
                'transaction_id' => $id,
                'user_id' => Auth::id(),
                'error_trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal melepas rak: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('customer.tagihan')
                ->with('error', 'Gagal melepas rak: ' . $e->getMessage());
        }
    }

    public function paymentDetails($id)
    {
        try {
            $transaction = Transaction::with('rak')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return view('customer.tagihan.payment-details', compact('transaction'));
        } catch (\Exception $e) {
            return redirect()->route('customer.tagihan')
                ->with('error', 'Transaksi tidak ditemukan');
        }
    }

    public function checkOverdue()
    {
        try {
            $userId = Auth::id();
            
            $expiredPendingCount = Transaction::where('user_id', $userId)
                ->where('transaction_status', 'pending')
                ->where('created_at', '<', Carbon::now()->subHours(24))
                ->update(['transaction_status' => 'expired']);
            
            $transactions = Transaction::with('rak')
                ->where('user_id', $userId)
                ->where('transaction_status', 'settlement')
                ->whereDate('sewa_berakhir', '<', Carbon::now())
                ->get();
            
            $updatedCount = 0;
            $warningCount = 0;
            
            foreach ($transactions as $transaction) {
                $daysOverdue = Carbon::now()->diffInDays($transaction->sewa_berakhir);
                
                if ($daysOverdue > 3) {
                    $transaction->update(['transaction_status' => 'expired']);
                    
                    $rak = $transaction->rak;
                    if ($rak) {
                        $rak->update(['status' => 'tersedia']);
                    }
                    
                    $updatedCount++;
                    
                    Log::info('Manual check: Transaction expired', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $userId,
                        'days_overdue' => $daysOverdue
                    ]);
                } else if ($daysOverdue > 0) {
                    $warningCount++;
                }
            }
            
            $message = "Pengecekan selesai. ";
            
            if ($expiredPendingCount > 0) {
                $message .= "{$expiredPendingCount} transaksi pending telah kadaluarsa. ";
            }
            
            if ($updatedCount > 0) {
                $message .= "{$updatedCount} transaksi sewa telah kadaluarsa. ";
            }
            
            if ($warningCount > 0) {
                $message .= "{$warningCount} transaksi mendekati kadaluarsa. ";
            }
            
            if ($expiredPendingCount == 0 && $updatedCount == 0 && $warningCount == 0) {
                $message = "Semua transaksi Anda dalam keadaan baik.";
            }
            
            return redirect()->route('customer.tagihan')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            Log::error('Manual Check Overdue Error: ' . $e->getMessage());
            return redirect()->route('customer.tagihan')
                ->with('error', 'Terjadi kesalahan saat mengecek status.');
        }
    }
}
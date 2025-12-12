<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Tagihan;
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

        // Ambil waktu dari database
        $now = Carbon::parse(DB::selectOne('SELECT NOW() as db_time')->db_time);

        // Auto expire tagihan pending
        Tagihan::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('expired_at', '<=', $now)
            ->each(function ($tagihan) use ($now) {
                $tagihan->update([
                    'status' => 'expired',
                    'cancelled_at' => $now
                ]);

                if ($tagihan->transaction) {
                    $tagihan->transaction->update(['transaction_status' => 'expired']);
                }
            });

        // Data pending
        $pendingTransactions = Tagihan::with(['transaction', 'rak'])
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at_db', 'desc')
            ->get();

        // Data expired
        $expiredTransactions = Tagihan::with(['transaction', 'rak'])
            ->where('user_id', $userId)
            ->where('status', 'expired')
            ->orderBy('created_at_db', 'desc')
            ->get();

        // Rak yang punya renewal aktif
        $rakIdsWithRenewal = Tagihan::where('user_id', $userId)
            ->where('is_renewal', true)
            ->whereIn('status', ['pending', 'settlement', 'expired', 'overdue'])
            ->pluck('rak_id')
            ->unique()
            ->toArray();

        $oneDayFromNow = $now->copy()->addDay();

        // Overdue
        $overdueTransactions = Tagihan::with(['transaction', 'rak'])
            ->where('user_id', $userId)
            ->where('status', 'settlement')
            ->where('is_renewal', false)
            ->whereNotIn('rak_id', $rakIdsWithRenewal)
            ->where(function ($query) use ($now, $oneDayFromNow) {
                $query->whereDate('sewa_berakhir', '<', $now)
                    ->orWhere(function ($q) use ($now, $oneDayFromNow) {
                        $q->whereDate('sewa_berakhir', '>=', $now)
                            ->whereDate('sewa_berakhir', '<=', $oneDayFromNow);
                    });
            })
            // TAMBAHAN: Filter out rak yang sudah dikosongkan
            ->whereHas('transaction', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('is_dikosongkan')
                    ->orWhere('is_dikosongkan', false);
                });
            })
            ->orderBy('sewa_berakhir', 'asc')
            ->get();

        return view('customer.tagihan.index', compact(
            'pendingTransactions',
            'expiredTransactions',
            'overdueTransactions',
            'now'
        ));
    }

    public function createPayment($id)
    {
        try {
            $tagihan = Tagihan::with(['transaction', 'rak'])
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $originalTransaction = $tagihan->transaction;
            $rak = $tagihan->rak;

            if (!$rak) {
                return redirect()->route('customer.tagihan')
                    ->with('error', 'Rak tidak ditemukan.');
            }

            // Cek renewal aktif
            $existingRenewal = Tagihan::where('rak_id', $rak->id)
                ->where('user_id', Auth::id())
                ->where('is_renewal', true)
                ->whereNotIn('status', ['expired', 'failed', 'cancel', 'overdue'])
                ->orderBy('created_at_db', 'desc')
                ->first();

            // Jika token renewal lama masih ada, pakai itu
            if ($existingRenewal && $existingRenewal->transaction?->snap_token) {
                $sewaBerahir = Carbon::parse($originalTransaction->sewa_berakhir)->startOfDay();
                $now = Carbon::parse(DB::selectOne('SELECT NOW() as db_time')->db_time)->startOfDay();

                $daysDiff = $now->diffInDays($sewaBerahir, false);

                $gracePeriodDays = 3;
                $dendaPerHari = 50000;
                $totalDenda = 0;

                if ($daysDiff < 0 && abs($daysDiff) > $gracePeriodDays) {
                    $latenessDays = abs($daysDiff) - $gracePeriodDays;
                    $totalDenda = $latenessDays * $dendaPerHari;
                }

                return view('customer.payment.renewal-checkout', [
                    'snapToken' => $existingRenewal->transaction->snap_token,
                    'rak' => $rak,
                    'transaction' => $originalTransaction,
                    'tagihan' => $existingRenewal,
                    'daysDiff' => $daysDiff,
                    'totalDenda' => $totalDenda,
                    'hargaSewa' => $rak->harga_sewa_perbulan,
                    'totalBayar' => $rak->harga_sewa_perbulan + $totalDenda,
                    'gracePeriodDays' => $gracePeriodDays,
                ]);
            }

            // Tidak ada renewal lama → buat baru
            if (!in_array($originalTransaction->transaction_status, ['settlement', 'expired'])) {
                return redirect()->route('customer.tagihan')
                    ->with('error', 'Transaksi tidak dapat diperpanjang saat ini.');
            }

            $orderId = 'RENEW-' . time() . '-' . $rak->id . '-' . $originalTransaction->id;
            $durasi = $rak->durasi_sewa_hari ?? 30;
            $hargaSewa = $rak->harga_sewa_perbulan;

            // Hitung denda
            $sewaBerahir = Carbon::parse($originalTransaction->sewa_berakhir)->startOfDay();
            $now = Carbon::parse(DB::selectOne('SELECT NOW() as db_time')->db_time)->startOfDay();

            $daysDiff = $now->diffInDays($sewaBerahir, false);

            $gracePeriodDays = 3;
            $dendaPerHari = 50000;
            $totalDenda = 0;

            if ($daysDiff < 0 && abs($daysDiff) > $gracePeriodDays) {
                $latenessDays = abs($daysDiff) - $gracePeriodDays;
                $totalDenda = $latenessDays * $dendaPerHari;
            }

            $totalBayar = $hargaSewa + $totalDenda;

            // Item details
            $itemDetails = [
                [
                    'id' => $rak->id . '-RENEW',
                    'price' => (int) $hargaSewa,
                    'quantity' => 1,
                    'name' => 'Perpanjangan Sewa - ' . $rak->nama_rak
                ]
            ];

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
                'custom_field2' => 'renewal',
                'callbacks' => [
                    'finish' => route('customer.list-rak.rak'),
                ]
            ]);

            // Hitung tanggal sewa baru
            $sewaMulaiBaru = max(now(), Carbon::parse($originalTransaction->sewa_berakhir));
            $sewaBerakhirBaru = $sewaMulaiBaru->copy()->addDays($durasi);

            // Create transaction → akan auto-create tagihan lewat observer
            $newTransaction = Transaction::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'rak_id' => $rak->id,
                'amount' => $totalBayar,
                'transaction_status' => 'pending',
                'snap_token' => $snapToken,
                'transaction_time' => now(),
                'sewa_mulai' => $sewaMulaiBaru,
                'sewa_berakhir' => $sewaBerakhirBaru,
                'parent_transaction_id' => $originalTransaction->id,
                'is_renewal' => true,
                'penalty_amount' => $totalDenda
            ]);

            // Ambil tagihan yang baru dibuat observer
            $newTagihan = Tagihan::where('transaction_id', $newTransaction->id)->first();

            Log::info('Renewal payment created', [
                'order_id' => $orderId,
                'tagihan_id' => $newTagihan?->id,
                'days_diff' => $daysDiff,
                'penalty' => $totalDenda,
                'total' => $totalBayar
            ]);

            return view('customer.payment.renewal-checkout', [
                'snapToken' => $snapToken,
                'rak' => $rak,
                'transaction' => $originalTransaction,
                'tagihan' => $newTagihan,
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

    public function checkStatus($id)
    {
        try {
            $tagihan = Tagihan::with('transaction')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'tagihan' => [
                    'id' => $tagihan->id,
                    'tagihan_code' => $tagihan->tagihan_code,
                    'status' => $tagihan->status,
                    'amount' => number_format($tagihan->total_tagihan, 0, ',', '.'),
                    'expired_at' => $tagihan->expired_at ? $tagihan->expired_at->format('d M Y H:i') : '-',
                    'paid_at' => $tagihan->paid_at ? $tagihan->paid_at->format('d M Y H:i') : '-',
                    'remaining_time' => $tagihan->remaining_time,
                    'is_renewal' => $tagihan->is_renewal,
                    'created_at' => $tagihan->created_at_db->format('d M Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan'
            ], 404);
        }
    }

    public function processExpired(Request $request, $id)
    {
        try {
            $tagihan = Tagihan::with(['transaction', 'rak'])
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            DB::beginTransaction();

            // 1. Balikin rak
            if ($tagihan->rak) {
                $tagihan->rak->update([
                    'status' => 'tersedia'
                ]);
            }

            // 2. Hapus transaksi kalau ada
            if ($tagihan->transaction) {
                $tagihan->transaction->delete();
            }

            // 3. Hapus tagihan UTAMA
            $tagihan->delete();

            // 4. Hapus tagihan perpanjangan pending
            Tagihan::where('rak_id', $tagihan->rak_id)
                ->where('user_id', Auth::id())
                ->where('is_renewal', true)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rak berhasil dilepas dan tagihan dihapus permanen.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal melepas rak: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkOverdue()
    {
        try {
            $userId = Auth::id();
            $now = Carbon::parse(DB::selectOne('SELECT NOW() as db_time')->db_time);

            // Auto expire pending
            $expiredPendingCount = Tagihan::where('user_id', $userId)
                ->where('status', 'pending')
                ->where('expired_at', '<=', $now)
                ->count();

            Tagihan::where('user_id', $userId)
                ->where('status', 'pending')
                ->where('expired_at', '<=', $now)
                ->each(function ($tagihan) use ($now) {
                    $tagihan->markAsExpired();
                    if ($tagihan->transaction) {
                        $tagihan->transaction->update(['transaction_status' => 'expired']);
                    }
                });

            // Cek overdue
            $tagihanOverdue = Tagihan::with(['transaction', 'rak'])
                ->where('user_id', $userId)
                ->where('status', 'settlement')
                ->whereDate('sewa_berakhir', '<', $now)
                ->get();

            $updatedCount = 0;
            $warningCount = 0;

            foreach ($tagihanOverdue as $tagihan) {
                $daysOverdue = $now->diffInDays($tagihan->sewa_berakhir);

                if ($daysOverdue > 3) {
                    $tagihan->markAsExpired();

                    if ($tagihan->transaction) {
                        $tagihan->transaction->update(['transaction_status' => 'expired']);
                    }

                    if ($tagihan->rak) {
                        $tagihan->rak->update(['status' => 'tersedia']);
                    }

                    $updatedCount++;
                } elseif ($daysOverdue > 0) {
                    $warningCount++;
                }
            }

            $message = "Pengecekan selesai. ";

            if ($expiredPendingCount > 0) {
                $message .= "{$expiredPendingCount} tagihan pending telah kadaluarsa. ";
            }

            if ($updatedCount > 0) {
                $message .= "{$updatedCount} tagihan sewa telah kadaluarsa. ";
            }

            if ($warningCount > 0) {
                $message .= "{$warningCount} tagihan mendekati kadaluarsa. ";
            }

            if ($expiredPendingCount == 0 && $updatedCount == 0 && $warningCount == 0) {
                $message = "Semua tagihan Anda dalam keadaan baik.";
            }

            return redirect()->route('customer.tagihan')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('customer.tagihan')
                ->with('error', 'Terjadi kesalahan saat mengecek status.');
        }
    }

    public function debugMissingTokens()
    {
        try {
            $tagihanPending = Tagihan::with('transaction')
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->get();

            $missingTokens = [];

            foreach ($tagihanPending as $tagihan) {
                if (!$tagihan->transaction || !$tagihan->transaction->snap_token) {
                    $missingTokens[] = [
                        'tagihan_id' => $tagihan->id,
                        'tagihan_code' => $tagihan->tagihan_code,
                        'transaction_id' => $tagihan->transaction_id,
                        'has_transaction' => $tagihan->transaction ? 'Yes' : 'No',
                        'snap_token' => $tagihan->transaction?->snap_token ?? 'NULL'
                    ];
                }
            }

            return response()->json([
                'total_pending' => $tagihanPending->count(),
                'missing_tokens' => count($missingTokens),
                'details' => $missingTokens
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function regenerateToken($tagihanId)
    {
        try {
            $tagihan = Tagihan::with(['transaction', 'rak'])
                ->where('id', $tagihanId)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            if (!$tagihan->transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction tidak ditemukan'
                ], 404);
            }

            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $tagihan->transaction->order_id,
                    'gross_amount' => (int) $tagihan->total_tagihan,
                ],
                'item_details' => [
                    [
                        'id' => $tagihan->rak_id,
                        'price' => (int) $tagihan->harga_sewa,
                        'quantity' => 1,
                        'name' => $tagihan->rak->nama_rak ?? 'Sewa Rak'
                    ]
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ]
            ]);

            $tagihan->transaction->update(['snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil dibuat ulang',
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat ulang token: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetail($id)
    {
        try {
            $tagihan = Tagihan::with(['transaction', 'rak'])
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $now = Carbon::parse(DB::selectOne('SELECT NOW() as db_time')->db_time);

            $remainingTime = 'Expired';
            if ($tagihan->expired_at && $now->lt($tagihan->expired_at)) {
                $remainingTime = $now->diffForHumans($tagihan->expired_at, true) . ' lagi';
            }

            return response()->json([
                'success' => true,
                'tagihan' => [
                    'tagihan_code' => $tagihan->tagihan_code,
                    'order_id' => $tagihan->transaction->order_id ?? '-',
                    'rak_nama' => $tagihan->rak->nama_rak ?? 'Rak',
                    'harga_sewa' => 'Rp ' . number_format($tagihan->harga_sewa, 0, ',', '.'),
                    'penalty' => 'Rp ' . number_format($tagihan->penalty_amount, 0, ',', '.'),
                    'penalty_amount' => $tagihan->penalty_amount,
                    'total_tagihan' => 'Rp ' . number_format($tagihan->total_tagihan, 0, ',', '.'),
                    'status' => $tagihan->status,
                    'is_renewal' => $tagihan->is_renewal,
                    'created_at' => $tagihan->created_at_db->format('d M Y H:i'),
                    'expired_at' => $tagihan->expired_at ? $tagihan->expired_at->format('d M Y H:i') : '-',
                    'remaining_time' => $remainingTime
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan'
            ], 404);
        }
    }

    public function cancelTagihan($id)
    {
        try {
            DB::beginTransaction();

            $tagihan = Tagihan::with(['transaction', 'rak'])
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            $tagihan->update([
                'status' => 'cancel',
                'cancelled_at' => now()
            ]);

            if ($tagihan->transaction) {
                $tagihan->transaction->update([
                    'transaction_status' => 'cancel',
                    'updated_at' => now()
                ]);
            }

            if ($tagihan->rak && $tagihan->rak->status !== 'terisi') {
                $tagihan->rak->update([
                    'status' => 'tersedia',
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil dibatalkan'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan atau tidak dapat dibatalkan'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan tagihan: ' . $e->getMessage()
            ], 500);
        }
    }
}

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
        // Konfigurasi Midtrans untuk pembayaran perpanjangan
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Halaman Tagihan
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Hapus session checkout jika ada
        if (session('payment_checkout')) {
            session()->forget('payment_checkout');
        }
        
        // PERBAIKAN: Otomatis update transaksi pending yang sudah kadaluarsa lebih dari 24 jam
        Transaction::where('user_id', $userId)
            ->where('transaction_status', 'pending')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->update(['transaction_status' => 'expired']);
        
        // 1. Get pending transactions - Hapus duplikat
        $pendingTransactions = Transaction::with('rak')
            ->where('user_id', $userId)
            ->where('transaction_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) {
                return $item->rak_id;
            });

        // 2. Get expired transactions - PERBAIKAN: Include semua transaksi dengan status expired
        $expiredTransactions = Transaction::with('rak')
            ->where('user_id', $userId)
            ->where('transaction_status', 'expired')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. PERBAIKAN: Get rak_id yang sudah memiliki renewal dengan status apapun kecuali expired/failed
        // Ini akan mengecek semua transaksi renewal yang masih aktif atau sudah berhasil
      $rakIdsWithRenewal = Transaction::where('user_id', $userId)
    ->where('is_renewal', true)
    ->whereIn('transaction_status', ['pending', 'settlement','expired','overdue'])
    ->pluck('rak_id')
    ->unique()
    ->toArray();


        // 4. Get transactions where sewa_berakhir has passed OR within 1 day before expiry
        // EXCLUDE rak yang sudah punya renewal aktif
        $now = Carbon::now();
        $oneDayFromNow = Carbon::now()->addDay();
        
        $overdueTransactions = Transaction::with('rak')
            ->where('user_id', $userId)
            ->where('transaction_status', 'settlement')
            ->where('is_renewal', false) // Pastikan ini bukan transaksi renewal
            ->whereNotIn('rak_id', $rakIdsWithRenewal) // Exclude rak dengan renewal aktif
            ->where(function($query) use ($now, $oneDayFromNow) {
                // Transaksi yang sudah lewat masa sewa
                $query->whereDate('sewa_berakhir', '<', $now)
                      // ATAU transaksi yang akan berakhir dalam 1 hari
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

    /**
     * Create new payment for overdue/extend transaction
     */
    public function createPayment($id)
    {
        try {
            $originalTransaction = Transaction::with('rak')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // PERBAIKAN: Cek apakah sudah ada renewal aktif untuk rak ini
            $existingRenewal = Transaction::where('rak_id', $originalTransaction->rak_id)
                ->where('user_id', Auth::id())
                ->where('is_renewal', true)
                ->whereNotIn('transaction_status', ['expired', 'failed', 'cancel', 'deny'])
                ->exists();

            if ($existingRenewal) {
                return redirect()->route('customer.tagihan')
                    ->with('info', 'Anda sudah memiliki permintaan perpanjangan aktif untuk rak ini.');
            }

            // Pastikan transaksi boleh diperpanjang
            if (!in_array($originalTransaction->transaction_status, ['settlement', 'expired'])) {
                return redirect()->route('customer.tagihan')
                    ->with('error', 'Transaksi tidak dapat diperpanjang saat ini.');
            }

            $rak = $originalTransaction->rak;
            if (!$rak) {
                return redirect()->route('customer.tagihan')
                    ->with('error', 'Rak tidak ditemukan.');
            }

            // Generate order ID baru
            $orderId = 'RENEW-' . time() . '-' . $rak->id . '-' . $originalTransaction->id;

            // Durasi sewa
            $durasi = $rak->durasi_sewa_hari ?? 30;

            // Hitung harga
            $pricePerMonth = $rak->harga_sewa_perbulan;
            $priceForDuration = ($pricePerMonth / 30) * $durasi;
            $hargaSewa = ceil($priceForDuration);

            // Midtrans params
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $hargaSewa,
                ],
                'item_details' => [
                    [
                        'id' => $rak->id . '-RENEW',
                        'price' => (int) $hargaSewa,
                        'quantity' => 1,
                        'name' => 'Perpanjangan Sewa - ' . $rak->nama_rak
                    ]
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'custom_field1' => $originalTransaction->id,
                'custom_field2' => 'renewal'
            ];

            $snapToken = Snap::getSnapToken($params);

            // Hitung tanggal mulai dari akhir sewa lama atau sekarang (ambil yang lebih besar)
            $sewaMulaiBaru = max(
                now(),
                Carbon::parse($originalTransaction->sewa_berakhir)
            );
            $sewaberakhirBaru = $sewaMulaiBaru->copy()->addDays($durasi);

            // Buat transaksi renewal
            $newTransaction = Transaction::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'rak_id' => $rak->id,
                'amount' => $hargaSewa,
                'transaction_status' => 'pending',
                'snap_token' => $snapToken,
                'transaction_time' => now(),
                'sewa_mulai' => $sewaMulaiBaru,
                'sewa_berakhir' => $sewaberakhirBaru,
                'parent_transaction_id' => $originalTransaction->id,
                'is_renewal' => true
            ]);

            /*
            |--------------------------------------------------------------------------
            | HITUNG TERLAMBAT & DENDA
            |--------------------------------------------------------------------------
            */
            $daysDiff = now()->diffInDays(
                Carbon::parse($originalTransaction->sewa_berakhir),
                false // false = menghitung selisih negatif
            );

            // Denda: Rp 50.000 per hari (sesuai dengan tampilan di view)
            $dendaPerHari = 50000;
            $totalDenda = $daysDiff < 0 ? abs($daysDiff) * $dendaPerHari : 0;
            $totalBayar = $hargaSewa + $totalDenda;

            // Kirim data ke blade
            return view('customer.payment.renewal-checkout', [
                'snapToken' => $snapToken,
                'rak' => $rak,
                'transaction' => $originalTransaction,
                'daysDiff' => $daysDiff,
                'totalDenda' => $totalDenda,
                'hargaSewa' => $hargaSewa,
                'totalBayar' => $totalBayar,
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

        // Cek ownership
        if ($originalTransaction->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Cek apakah sudah ada renewal aktif
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

        // Hitung tanggal mulai dari akhir sewa lama atau sekarang (ambil yang lebih besar)
        $sewaMulaiBaru = max(
            now(),
            Carbon::parse($originalTransaction->sewa_berakhir)
        );
        $sewaberakhirBaru = $sewaMulaiBaru->copy()->addDays($durasi);

        // Ambil harga dari rak
        $pricePerMonth = $rak->harga_sewa_perbulan;
        $priceForDuration = ($pricePerMonth / 30) * $durasi;
        $hargaSewa = ceil($priceForDuration);

        // Buat renewal baru dengan data dari transaksi lama
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

    /**
     * Check payment status for pending transactions
     */
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

    /**
     * Process expired transaction (manual trigger) - FIXED VERSION
     */
    public function processExpired(Request $request, $id)
    {
        try {
            $transaction = Transaction::with('rak')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            DB::beginTransaction();

            // Update transaksi utama menjadi expired
            $transaction->update([
                'transaction_status' => 'expired',
                'updated_at' => now()
            ]);

            // Jika ada rak terkait, ubah statusnya menjadi tersedia
            if ($transaction->rak) {
                $transaction->rak->update([
                    'status' => 'tersedia',
                    'updated_at' => now()
                ]);
            }

            // Cari dan update semua transaksi renewal yang masih pending untuk rak ini
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

            // Kembalikan response JSON untuk AJAX
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

            // Jika bukan request AJAX, redirect seperti biasa
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

    /**
     * Get payment details for a transaction
     */
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

    /**
     * Manual check for overdue transactions (bisa dipanggil via route atau job)
     */
    public function checkOverdue()
    {
        try {
            $userId = Auth::id();
            
            // PERBAIKAN: Juga update transaksi pending yang sudah kadaluarsa di sini
            $expiredPendingCount = Transaction::where('user_id', $userId)
                ->where('transaction_status', 'pending')
                ->where('created_at', '<', Carbon::now()->subHours(24))
                ->update(['transaction_status' => 'expired']);
            
            // Get user's transactions that need checking
            $transactions = Transaction::with('rak')
                ->where('user_id', $userId)
                ->where('transaction_status', 'settlement')
                ->whereDate('sewa_berakhir', '<', Carbon::now())
                ->get();
            
            $updatedCount = 0;
            $warningCount = 0;
            
            foreach ($transactions as $transaction) {
                $daysOverdue = Carbon::now()->diffInDays($transaction->sewa_berakhir);
                
                // Check if overdue more than 3 days
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
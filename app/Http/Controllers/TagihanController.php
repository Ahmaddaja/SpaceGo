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
        
        // 1. Get pending transactions - Hapus duplikat
        $pendingTransactions = Transaction::with('rak')
            ->where('user_id', $userId)
            ->where('transaction_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) {
                return $item->rak_id;
            });

        // 2. Get expired transactions
        $expiredTransactions = Transaction::with('rak')
            ->where('user_id', $userId)
            ->where('transaction_status', 'expired')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Get rak_id yang sudah memiliki renewal dengan status apapun kecuali expired/failed
        $rakIdsWithRenewal = Transaction::where('user_id', $userId)
            ->where('is_renewal', true)
            ->whereNotIn('transaction_status', ['expired', 'failed', 'cancel', 'deny'])
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

        $rak = $originalTransaction->rak;
        if (!$rak) {
            return redirect()->route('customer.tagihan')
                ->with('error', 'Rak tidak ditemukan.');
        }

        // ================================
        // 🔍 CEK APAKAH SUDAH ADA RENEWAL AKTIF UNTUK RAK INI
        // ================================
        $existingRenewal = Transaction::where('rak_id', $rak->id)
            ->where('user_id', Auth::id())
            ->where('is_renewal', true)
            ->whereNotIn('transaction_status', ['expired', 'failed', 'cancel', 'deny'])
            ->orderBy('created_at', 'desc')
            ->first();

        // ⚠️ Jika renewal sudah ada → TAMPILKAN CHECKOUT dari transaksi itu
        if ($existingRenewal) {

            // Jika sudah ada snapToken → langsung ke halaman checkout
            if ($existingRenewal->snap_token) {
                return view('customer.payment.renewal-checkout', [
                    'snapToken' => $existingRenewal->snap_token,
                    'rak' => $existingRenewal->rak,
                    'transaction' => $originalTransaction,
                    'daysDiff' => 0,
                    'totalDenda' => $existingRenewal->penalty_amount ?? 0,
                    'hargaSewa' => $existingRenewal->amount,
                    'totalBayar' => $existingRenewal->amount,
                    'gracePeriodDays' => 3,
                ]);
            }

            // Jika snap token tidak ada → generate ulang tanpa membuat transaksi baru
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

        // ================================
        // ❗ JIKA TIDAK ADA RENEWAL AKTIF → BUAT BARU
        // ================================

        if (!in_array($originalTransaction->transaction_status, ['settlement', 'expired'])) {
            return redirect()->route('customer.tagihan')
                ->with('error', 'Transaksi tidak dapat diperpanjang saat ini.');
        }

        // Generate order ID
        $orderId = 'RENEW-' . time() . '-' . $rak->id . '-' . $originalTransaction->id;

        // Durasi sewa
        $durasi = $rak->durasi_sewa_hari ?? 30;

        // Hitung harga
        $pricePerMonth = $rak->harga_sewa_perbulan;
        $hargaSewa = $pricePerMonth;

        // Hitung denda
        $sewaBerahir = Carbon::parse($originalTransaction->sewa_berakhir)->startOfDay();
        $now = Carbon::now()->startOfDay();

        $daysDiff = $sewaBerahir->diffInDays($now, false);
        $gracePeriodDays = 3;
        $dendaPerHari = 50000;

        $totalDenda = 0;
        if ($daysDiff > $gracePeriodDays) {
            $latenessDays = $daysDiff - $gracePeriodDays;
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

        if ($totalDenda > 0) {
            $itemDetails[] = [
                'id' => 'PENALTY-' . $originalTransaction->id,
                'price' => (int) $totalDenda,
                'quantity' => 1,
                'name' => 'Denda Keterlambatan'
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

        // Hitung tanggal sewa baru
        $sewaMulaiBaru = max(now(), Carbon::parse($originalTransaction->sewa_berakhir));
        $sewaberakhirBaru = $sewaMulaiBaru->copy()->addDays($durasi);

        // Buat transaksi renewal baru
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
     * Process expired transaction (manual trigger)
     */
    public function processExpired($id)
    {
        try {
            $transaction = Transaction::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Only process settlement transactions with passed sewa_berakhir
            if ($transaction->transaction_status === 'settlement' && 
                $transaction->sewa_berakhir && 
                $transaction->sewa_berakhir < now()) {
                
                $transaction->update(['transaction_status' => 'expired']);
                
                // Update rak status to tersedia
                $rak = $transaction->rak;
                if ($rak) {
                    $rak->update(['status' => 'tersedia']);
                }

                Log::info('Transaction marked as expired', [
                    'transaction_id' => $transaction->id,
                    'rak_id' => $rak ? $rak->id : null
                ]);

                return redirect()->route('customer.tagihan')
                    ->with('success', 'Status rak telah diperbarui menjadi tersedia.');
            }

            return redirect()->route('customer.tagihan')
                ->with('info', 'Transaksi tidak memerlukan penanganan saat ini.');

        } catch (\Exception $e) {
            Log::error('Process Expired Error: ' . $e->getMessage());
            
            return redirect()->route('customer.tagihan')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            
            if ($updatedCount > 0) {
                $message .= "{$updatedCount} transaksi telah kadaluarsa. ";
            }
            
            if ($warningCount > 0) {
                $message .= "{$warningCount} transaksi mendekati kadaluarsa. ";
            }
            
            if ($updatedCount == 0 && $warningCount == 0) {
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
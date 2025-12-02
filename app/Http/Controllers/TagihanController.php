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
            return $item->rak_id; // Hanya ambil 1 per rak
        });

    // 2. Get expired transactions
    $expiredTransactions = Transaction::with('rak')
        ->where('user_id', $userId)
        ->where('transaction_status', 'expired')
        ->orderBy('created_at', 'desc')
        ->get();

    // 3. Get transactions where sewa_berakhir has passed
    $overdueTransactions = Transaction::with('rak')
        ->where('user_id', $userId)
        ->where('transaction_status', 'settlement')
        ->whereDate('sewa_berakhir', '<', Carbon::now())
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

            // Check if transaction is eligible for renewal
            if (!in_array($originalTransaction->transaction_status, ['settlement', 'expired'])) {
                return redirect()->route('customer.tagihan')
                    ->with('error', 'Transaksi tidak dapat diperpanjang saat ini.');
            }

            // Check if rak still exists
            $rak = $originalTransaction->rak;
            if (!$rak) {
                return redirect()->route('customer.tagihan')
                    ->with('error', 'Rak tidak ditemukan.');
            }

            // Generate unique Order ID for renewal
            $orderId = 'RENEW-' . time() . '-' . $rak->id . '-' . $originalTransaction->id;

            // Calculate duration (default 30 days or from rak settings)
            $durasi = $rak->durasi_sewa_hari ?? 30;

            // Calculate price
            $pricePerMonth = $rak->harga_sewa_perbulan;
            
            // For simplicity, calculate price based on days
            $priceForDuration = ($pricePerMonth / 30) * $durasi;

            // Parameters for Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) ceil($priceForDuration),
                ],
                'item_details' => [
                    [
                        'id' => $rak->id . '-RENEW',
                        'price' => (int) ceil($priceForDuration),
                        'quantity' => 1,
                        'name' => 'Perpanjangan Sewa - ' . $rak->nama_rak
                    ]
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'custom_field1' => $originalTransaction->id, // Store original transaction ID
                'custom_field2' => 'renewal' // Mark as renewal transaction
            ];

            // Generate Snap token
            $snapToken = Snap::getSnapToken($params);

            // Create new transaction for renewal
            $newTransaction = Transaction::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'rak_id' => $rak->id,
                'amount' => ceil($priceForDuration),
                'transaction_status' => 'pending',
                'snap_token' => $snapToken,
                'transaction_time' => now(),
                'parent_transaction_id' => $originalTransaction->id, // Link to original
                'is_renewal' => true
            ]);

            // Log for debugging
            Log::info('Renewal Transaction Created', [
                'original_transaction_id' => $originalTransaction->id,
                'new_transaction_id' => $newTransaction->id,
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'rak_id' => $rak->id,
                'amount' => $priceForDuration
            ]);

            // Show checkout page for renewal
            return view('customer.payment.renewal-checkout', compact('snapToken', 'rak', 'newTransaction'));

        } catch (\Exception $e) {
            Log::error('Renewal Payment Error: ' . $e->getMessage());
            
            return redirect()->route('customer.tagihan')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
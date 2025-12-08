<?php

namespace App\Http\Controllers;

use App\Models\CustomerHistory;
use App\Models\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    // Menampilkan semua history customer
    public function index()
    {
        $customer = Auth::user();
        
        $histories = CustomerHistory::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Cari tabel transaksi yang ada
        $tableName = $this->findTransactionTable();
        
        $paymentSuccessCount = 0;
        $rakActiveCount = 0;
        
        if ($tableName) {
            // Hitung pembayaran berhasil
            $paymentSuccessCount = DB::table($tableName)
                ->where('user_id', $customer->id)
                ->where('transaction_status', 'settlement')
                ->count();
            
            // Hitung rak aktif (hanya dari transaksi yang settlement)
            $rakIds = DB::table($tableName)
                ->where('user_id', $customer->id)
                ->where('transaction_status', 'settlement')
                ->whereNotNull('rak_id')
                ->pluck('rak_id')
                ->unique()
                ->toArray();
            
            $rakActiveCount = count($rakIds);
        }

        $raks = Rak::all();

        // Tampilkan stats hanya di halaman ini
        $showStats = true;

        return view('customer.history.index', compact(
            'histories', 
            'paymentSuccessCount', 
            'raks',
            'showStats',
            'rakActiveCount'
        ));
    }

    // Menampilkan history pembayaran khusus
    public function paymentHistory()
    {
        $customer = Auth::user();
        
        // Cari tabel transaksi yang ada
        $tableName = $this->findTransactionTable();
        
        // Inisialisasi variabel statistik
        $paymentStats = [
            'total_transactions' => 0,
            'successful_payments' => 0,
            'pending_payments' => 0,
            'failed_payments' => 0,
            'total_amount_all' => 0,      // SEMUA transaksi (termasuk pending)
            'total_amount_settled' => 0,  // HANYA settlement
            'average_amount_settled' => 0 // Rata-rata settlement
        ];
        
        if ($tableName) {
            // Ambil data langsung dari tabel transaksi
            $rawPayments = DB::table($tableName)
                ->where('user_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Hitung statistik berdasarkan status
            $paymentStats['total_transactions'] = $rawPayments->count();
            $paymentStats['successful_payments'] = $rawPayments->where('transaction_status', 'settlement')->count();
            $paymentStats['pending_payments'] = $rawPayments->where('transaction_status', 'pending')->count();
            $paymentStats['failed_payments'] = $rawPayments->whereIn('transaction_status', ['expire', 'deny', 'cancel'])->count();
            
            // Hitung total amount: SEMUA transaksi
            $paymentStats['total_amount_all'] = $rawPayments->sum('amount');
            
            // Hitung total amount: HANYA settlement (tidak termasuk pending/gagal)
            $paymentStats['total_amount_settled'] = $rawPayments
                ->where('transaction_status', 'settlement')
                ->sum('amount');
            
            // Hitung rata-rata amount: HANYA settlement
            $paymentStats['average_amount_settled'] = $paymentStats['successful_payments'] > 0 
                ? $paymentStats['total_amount_settled'] / $paymentStats['successful_payments'] 
                : 0;
            
            // Format data transaksi seperti CustomerHistory
            $payments = $rawPayments->map(function ($payment) {
                return (object) [
                    'id' => $payment->id ?? null,
                    'activity_type' => 'PAYMENT_SUCCESS',
                    'description' => $this->generatePaymentDescription($payment),
                    'additional_data' => [
                        'amount' => $payment->amount ?? 0,
                        'payment_method' => $payment->payment_type ?? 'Unknown',
                        'transaction_id' => $payment->order_id ?? $payment->id,
                        'rak_id' => $payment->rak_id ?? null,
                        'status' => $payment->transaction_status ?? 'unknown'
                    ],
                    'created_at' => isset($payment->created_at) 
                        ? \Carbon\Carbon::parse($payment->created_at)
                        : now(),
                    'transaction_status' => $payment->transaction_status ?? null,
                    'payment_type' => $payment->payment_type ?? null,
                    'amount' => $payment->amount ?? 0,
                    'order_id' => $payment->order_id ?? null,
                    'rak_id' => $payment->rak_id ?? null
                ];
            });
            
            // Paginasi manual
            $page = request()->get('page', 1);
            $perPage = 10;
            $paginatedPayments = new \Illuminate\Pagination\LengthAwarePaginator(
                $payments->forPage($page, $perPage),
                $payments->count(),
                $perPage,
                $page,
                ['path' => request()->url()]
            );
            
        } else {
            // Fallback ke CustomerHistory jika tabel transaksi tidak ditemukan
            $paginatedPayments = CustomerHistory::where('customer_id', $customer->id)
                ->whereIn('activity_type', ['PAYMENT_SUCCESS', 'PAYMENT_FAILED', 'RENTAL_PAYMENT'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('customer.history.payment', [
            'payments' => $paginatedPayments ?? collect([]),
            'tableName' => $tableName ?? null,
            'isTransactionData' => $tableName ? true : false,
            'paymentStats' => $paymentStats
        ]);
    }

    // Fungsi untuk mencari nama tabel transaksi
    private function findTransactionTable()
    {
        $possibleTables = ['transactions', 'orders', 'payments', 'rentals', 'sewas', 'invoices'];
        
        foreach ($possibleTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                // Cek apakah tabel memiliki kolom yang diperlukan
                $columns = DB::getSchemaBuilder()->getColumnListing($table);
                
                $hasUserId = in_array('user_id', $columns);
                $hasAmount = in_array('amount', $columns);
                $hasStatus = in_array('transaction_status', $columns) || in_array('status', $columns);
                
                if ($hasUserId && $hasAmount && $hasStatus) {
                    return $table;
                }
            }
        }
        
        return null;
    }

    // Fungsi untuk generate deskripsi pembayaran
    private function generatePaymentDescription($payment)
    {
        $description = 'Pembayaran';
        
        if (isset($payment->rak_id)) {
            $description .= ' untuk Rak #' . $payment->rak_id;
        }
        
        if (isset($payment->order_id)) {
            $description .= ' - ' . $payment->order_id;
        }
        
        if (isset($payment->transaction_status)) {
            $statusMap = [
                'settlement' => ' (Berhasil)',
                'pending' => ' (Menunggu Pembayaran)',
                'expire' => ' (Kedaluwarsa)',
                'deny' => ' (Ditolak)',
                'cancel' => ' (Dibatalkan)'
            ];
            
            $description .= $statusMap[$payment->transaction_status] ?? ' (' . $payment->transaction_status . ')';
        }
        
        return $description;
    }

    // API untuk mendapatkan history
    public function getHistoryJson()
    {
        $customer = Auth::user();
        
        $histories = CustomerHistory::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($history) {
                return [
                    'id' => $history->id,
                    'activity_type' => $history->activity_type,
                    'description' => $history->description,
                    'additional_data' => $history->additional_data,
                    'created_at' => $history->created_at->format('d M Y H:i'),
                    'created_at_full' => $history->created_at->toISOString()
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $histories
        ]);
    }
}
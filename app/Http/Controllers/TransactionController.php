<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Menampilkan riwayat transaksi (read-only)
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'rak.gudang'])
            ->latest('transaction_time');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('transaction_status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('transaction_time', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('transaction_time', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('rak', function ($q) use ($search) {
                        $q->where('nama_rak', 'like', "%{$search}%")
                            ->orWhere('kode_rak', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query->paginate(15);

        // Statistics
        $stats = [
            'total' => Transaction::count(),
            'success' => Transaction::success()->count(),
            'pending' => Transaction::pending()->count(),
            'failed' => Transaction::failed()->count(),
            'total_revenue' => Transaction::success()->sum('amount'),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    /**
     * Menampilkan detail transaksi
     */
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'rak.gudang'])
            ->findOrFail($id);

        return view('admin.transactions.show', compact('transaction'));
    }
}

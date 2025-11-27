<?php

namespace App\Http\Controllers;

use App\Models\CustomerHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    // Menampilkan semua history customer
    public function index()
    {
        $customer = Auth::user();
        
        $histories = CustomerHistory::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Diubah dari 20 menjadi 10

        return view('customer.history.index', compact('histories'));
    }

    // Menampilkan history pembayaran khusus
    public function paymentHistory()
    {
        $customer = Auth::user();
        
        $payments = CustomerHistory::where('customer_id', $customer->id)
            ->whereIn('activity_type', ['PAYMENT_SUCCESS', 'PAYMENT_FAILED', 'RENTAL_PAYMENT'])
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Diubah dari 15 menjadi 10

        return view('customer.history.payment', compact('payments'));
    }

    // API untuk mendapatkan history (jika diperlukan)
    public function getHistoryJson()
    {
        $customer = Auth::user();
        
        $histories = CustomerHistory::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get() // Tetap menggunakan get() untuk API agar tidak mengubah struktur response
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
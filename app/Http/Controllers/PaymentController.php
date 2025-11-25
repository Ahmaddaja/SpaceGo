<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Rak;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function bayar($id)
    {
        $rak = Rak::findOrFail($id);

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . time() . '-' . $rak->id,
                'gross_amount' => $rak->harga_sewa_perbulan,
            ],
            'item_details' => [
                [
                    'id' => $rak->id,
                    'price' => $rak->harga_sewa_perbulan,
                    'quantity' => 1,
                    'name' => $rak->nama_rak
                ]
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ]
        ];

        // Generate Snap token
        $snapToken = Snap::getSnapToken($params);

        return view('customer.payment.checkout', compact('snapToken', 'rak'));
    }

    public function callback(Request $request)
    {
        $order_id = $request->order_id;
        $transaction_status = $request->transaction_status;
        $fraud_status = $request->fraud_status;

        if ($transaction_status == 'capture' || $transaction_status == 'settlement') {

            // Ambil ID rak dari order_id (ORDER-xxxx-idRak)
            $explode = explode('-', $order_id);
            $rak_id = end($explode);

            $rak = Rak::find($rak_id);

            if ($rak) {
                $rak->status = 'terisi'; // ubah status otomatis
                $rak->save();
            }
        }

        return response()->json(['message' => 'Callback processed']);
    }

}

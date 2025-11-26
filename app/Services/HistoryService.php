<?php

namespace App\Services;

use App\Models\CustomerHistory;
use Illuminate\Support\Facades\Auth;

class HistoryService
{
    // Log pembayaran berhasil
    public static function logPaymentSuccess($customer_id, $amount, $rental_id, $payment_method, $created_by = null)
    {
        $description = "Pembayaran sewa rak berhasil - Rp " . number_format($amount, 0, ',', '.');
        $additional_data = [
            'amount' => $amount,
            'rental_id' => $rental_id,
            'payment_method' => $payment_method,
            'currency' => 'IDR',
            'status' => 'success'
        ];

        return CustomerHistory::logActivity(
            $customer_id,
            'PAYMENT_SUCCESS',
            $description,
            $additional_data,
            $created_by ?? (Auth::check() ? Auth::user()->name : 'system')
        );
    }

    // Log pembayaran gagal
    public static function logPaymentFailed($customer_id, $amount, $rental_id, $reason, $created_by = null)
    {
        $description = "Pembayaran sewa rak gagal - " . $reason;
        $additional_data = [
            'amount' => $amount,
            'rental_id' => $rental_id,
            'reason' => $reason,
            'status' => 'failed'
        ];

        return CustomerHistory::logActivity(
            $customer_id,
            'PAYMENT_FAILED',
            $description,
            $additional_data,
            $created_by ?? (Auth::check() ? Auth::user()->name : 'system')
        );
    }

    // Log sewa rak baru
    public static function logNewRental($customer_id, $rack_code, $duration, $total_amount, $created_by = null)
    {
        $description = "Menyewa rak {$rack_code} selama {$duration} hari";
        $additional_data = [
            'rack_code' => $rack_code,
            'duration' => $duration,
            'total_amount' => $total_amount,
            'type' => 'new_rental'
        ];

        return CustomerHistory::logActivity(
            $customer_id,
            'NEW_RENTAL',
            $description,
            $additional_data,
            $created_by ?? (Auth::check() ? Auth::user()->name : 'system')
        );
    }

    // Log perpanjangan sewa
    public static function logRentalExtension($customer_id, $rack_code, $extension_days, $additional_amount, $created_by = null)
    {
        $description = "Memperpanjang sewa rak {$rack_code} selama {$extension_days} hari";
        $additional_data = [
            'rack_code' => $rack_code,
            'extension_days' => $extension_days,
            'additional_amount' => $additional_amount,
            'type' => 'extension'
        ];

        return CustomerHistory::logActivity(
            $customer_id,
            'RENTAL_EXTENSION',
            $description,
            $additional_data,
            $created_by ?? (Auth::check() ? Auth::user()->name : 'system')
        );
    }
}
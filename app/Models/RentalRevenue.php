<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalRevenue extends Model
{
    protected $fillable = [
        'year',
        'month',
        'total_revenue',
        'total_transactions',
        'total_raks_rented',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'total_transactions' => 'integer',
        'total_raks_rented' => 'integer',
    ];

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $months[$this->month] ?? '';
    }

    public function getFormattedRevenueAttribute(): string
    {
        return 'Rp ' . number_format($this->total_revenue, 0, ',', '.');
    }
}
<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\RentalRevenue;
use Illuminate\Support\Facades\DB;

class RevenueService
{
    public static function generateMonthlyReport(int $year, int $month): RentalRevenue
    {
        $data = Transaction::whereYear('transaction_time', $year)
            ->whereMonth('transaction_time', $month)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->select(
                DB::raw('SUM(amount) as total_revenue'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('COUNT(DISTINCT rak_id) as total_raks_rented')
            )
            ->first();

        return RentalRevenue::updateOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'total_revenue' => $data->total_revenue ?? 0,
                'total_transactions' => $data->total_transactions ?? 0,
                'total_raks_rented' => $data->total_raks_rented ?? 0,
            ]
        );
    }

    public static function generateYearlyReport(int $year): array
    {
        $reports = [];
        for ($month = 1; $month <= 12; $month++) {
            $reports[] = self::generateMonthlyReport($year, $month);
        }
        return $reports;
    }

    public static function syncAllRevenues(): void
    {
        $transactions = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
            ->select(
                DB::raw('YEAR(transaction_time) as year'),
                DB::raw('MONTH(transaction_time) as month')
            )
            ->distinct()
            ->get();

        foreach ($transactions as $transaction) {
            self::generateMonthlyReport($transaction->year, $transaction->month);
        }
    }
}
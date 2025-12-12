<?php

namespace App\Http\Controllers;

use App\Models\RentalRevenue;
use App\Models\Transaction;
use App\Models\Rak;
use App\Models\Gudang;
use App\Services\RevenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month'); // Keep for backward compatibility
        $monthFrom = $request->input('month_from');
        $monthTo = $request->input('month_to');
        $gudangId = $request->input('gudang_id');
        $status = $request->input('status');

        // Determine month range
        $startMonth = $monthFrom ?: 1;
        $endMonth = $monthTo ?: 12;

        // Build query for transactions with filters
        $transactionQuery = Transaction::query();

        if ($month) {
            $transactionQuery->whereMonth('transaction_time', $month);
        }

        $transactionQuery->whereYear('transaction_time', $year);

        if ($gudangId) {
            $transactionQuery->whereHas('rak.gudang', function($q) use ($gudangId) {
                $q->where('id', $gudangId);
            });
        }

        if ($status === 'success') {
            $transactionQuery->whereIn('transaction_status', ['capture', 'settlement']);
        } elseif ($status === 'pending') {
            $transactionQuery->where('transaction_status', 'pending');
        } elseif ($status === 'failed') {
            $transactionQuery->whereIn('transaction_status', ['deny', 'expire', 'cancel']);
        }

        // Get revenues - either filtered or all
        if ($month && ($gudangId || $status)) {
            // For specific month with gudang/status filters, build custom revenue data
            $revenues = collect();
            $yearlyTotal = $transactionQuery->whereIn('transaction_status', ['capture', 'settlement'])->sum('amount');
            $yearlyTransactions = $transactionQuery->count();

            if ($yearlyTotal > 0 || $yearlyTransactions > 0) {
                $revenues->push((object)[
                    'month' => $month,
                    'month_name' => \Carbon\Carbon::create()->month($month)->translatedFormat('F'),
                    'year' => $year,
                    'total_transactions' => $yearlyTransactions,
                    'total_raks_rented' => $transactionQuery->whereIn('transaction_status', ['capture', 'settlement'])->distinct('rak_id')->count('rak_id'),
                    'total_revenue' => $yearlyTotal,
                    'formatted_revenue' => 'Rp ' . number_format($yearlyTotal, 0, ',', '.')
                ]);
            }
        } else {
            // Use RentalRevenue table for general queries
            $query = RentalRevenue::where('year', $year);

            if ($month) {
                $query->where('month', $month);
            }

            $revenues = $query->orderBy('month', 'desc')->get();

            // Filter for month range if selected
            if ($monthFrom && $monthTo && !$month) {
                $revenues = $revenues->where('month', '>=', $monthFrom)->where('month', '<=', $monthTo);
                $yearlyTotal = $revenues->sum('total_revenue');
                $yearlyTransactions = $revenues->sum('total_transactions');
            } else {
                $yearlyTotal = $revenues->sum('total_revenue');
                $yearlyTransactions = $revenues->sum('total_transactions');
            }
        }

        $availableYears = Transaction::selectRaw('DISTINCT YEAR(transaction_time) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Build query for chart data with filters
        $chartQuery = Transaction::query()->whereYear('transaction_time', $year);

        if ($month) {
            $chartQuery->whereMonth('transaction_time', $month);
        }

        if ($gudangId) {
            $chartQuery->whereHas('rak.gudang', function($q) use ($gudangId) {
                $q->where('id', $gudangId);
            });
        }

        if ($status === 'success') {
            $chartQuery->whereIn('transaction_status', ['capture', 'settlement']);
        } elseif ($status === 'pending') {
            $chartQuery->where('transaction_status', 'pending');
        } elseif ($status === 'failed') {
            $chartQuery->whereIn('transaction_status', ['deny', 'expire', 'cancel']);
        }

        // Chart data for reports
        $transaksiLabels = [];
        $transaksiData = [];
        $pendapatanLabels = [];
        $pendapatanData = [];
        $rakTerisi = Rak::where('status', 'terisi')->count();
        $rakMaintenance = Rak::where('status', 'maintenance')->count();
        $rakTersedia = Rak::where('status', 'tersedia')->count();
        $statusSuccess = Transaction::whereIn('transaction_status', ['capture', 'settlement'])->count();
        $statusPending = Transaction::where('transaction_status', 'pending')->count();
        $statusFailed = Transaction::whereIn('transaction_status', ['deny', 'expire', 'cancel'])->count();

        // If no rack data, set default
        if ($rakTerisi == 0 && $rakMaintenance == 0 && $rakTersedia == 0) {
            $rakTersedia = 1;
        }

        // Monthly transaction data for selected year (or single month if filtered)
        // If month range is specified, use it; otherwise use single month or all months
        if ($monthFrom && $monthTo && !$month) {
            $startMonth = $monthFrom;
            $endMonth = $monthTo;
        } else {
            $startMonth = $month ?: 1;
            $endMonth = $month ?: 12;
        }

        for ($i = $startMonth; $i <= $endMonth; $i++) {
            $transaksiLabels[] = $this->getMonthName($i);

            $countQuery = clone $chartQuery;
            $count = $countQuery->whereMonth('transaction_time', $i)->count();
            $transaksiData[] = $count;
        }

        // Monthly revenue data for selected year (or single month if filtered)
        for ($i = $startMonth; $i <= $endMonth; $i++) {
            $pendapatanLabels[] = $this->getMonthName($i);

            $revenueQuery = clone $chartQuery;
            $total = $revenueQuery->whereIn('transaction_status', ['capture', 'settlement'])
                ->whereMonth('transaction_time', $i)
                ->sum('amount');

            $pendapatanData[] = $total;
        }

        $chartData = $this->getChartData($year);

        return view('admin.laporan.pendapatan', compact(
            'revenues',
            'year',
            'month',
            'monthFrom',
            'monthTo',
            'yearlyTotal',
            'yearlyTransactions',
            'availableYears',
            'chartData',
            'transaksiLabels',
            'transaksiData',
            'pendapatanLabels',
            'pendapatanData',
            'rakTerisi',
            'rakMaintenance',
            'rakTersedia',
            'statusSuccess',
            'statusPending',
            'statusFailed'
        ));
    }

    public function detail(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $transactions = Transaction::with(['user', 'rak'])
            ->whereYear('transaction_time', $year)
            ->whereMonth('transaction_time', $month)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->orderBy('transaction_time', 'desc')
            ->paginate(20);

        $summary = Transaction::whereYear('transaction_time', $year)
            ->whereMonth('transaction_time', $month)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(amount) as average')
            )
            ->first();

        return view('admin.laporan.detail', compact(
            'transactions',
            'year',
            'month',
            'summary'
        ));
    }

    public function exportCsv(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');

        $filename = $month
            ? "laporan-pendapatan-{$year}-{$month}.csv"
            : "laporan-pendapatan-{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $revenues = collect();

        if ($month) {
            $total = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->sum('amount');

            $count = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->count();

            $rakCount = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->distinct('rak_id')
                ->count('rak_id');

            if ($total > 0 || $count > 0) {
                $revenues->push([
                    'bulan' => \Carbon\Carbon::create()->month($month)->translatedFormat('F'),
                    'tahun' => $year,
                    'total_transaksi' => $count,
                    'rak_disewa' => $rakCount,
                    'total_pendapatan' => $total
                ]);
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $total = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                    ->whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->sum('amount');

                $count = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                    ->whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->count();

                $rakCount = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                    ->whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->distinct('rak_id')
                    ->count('rak_id');

                if ($total > 0 || $count > 0) {
                    $revenues->push([
                        'bulan' => \Carbon\Carbon::create()->month($i)->translatedFormat('F'),
                        'tahun' => $year,
                        'total_transaksi' => $count,
                        'rak_disewa' => $rakCount,
                        'total_pendapatan' => $total
                    ]);
                }
            }
        }

        $callback = function() use ($revenues) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Bulan', 'Tahun', 'Total Transaksi', 'Rak Disewa', 'Total Pendapatan']);

            // Data
            foreach ($revenues as $revenue) {
                fputcsv($file, [
                    $revenue['bulan'],
                    $revenue['tahun'],
                    $revenue['total_transaksi'],
                    $revenue['rak_disewa'],
                    $revenue['total_pendapatan']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');

        // Build revenue summary from Transaction data instead of RentalRevenue
        $revenues = collect();
        $yearlyTotal = 0;

        if ($month) {
            // Single month data
            $total = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->sum('amount');

            $count = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->count();

            $rakCount = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->distinct('rak_id')
                ->count('rak_id');

            if ($total > 0 || $count > 0) {
                $revenues->push((object)[
                    'month' => $month,
                    'month_name' => $this->getMonthName($month),
                    'year' => $year,
                    'total_transactions' => $count,
                    'total_raks_rented' => $rakCount,
                    'total_revenue' => $total
                ]);
            }
            $yearlyTotal = $total;
        } else {
            // Year summary data
            for ($i = 1; $i <= 12; $i++) {
                $total = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                    ->whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->sum('amount');

                $count = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                    ->whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->count();

                $rakCount = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                    ->whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->distinct('rak_id')
                    ->count('rak_id');

                if ($total > 0 || $count > 0) {
                    $revenues->push((object)[
                        'month' => $i,
                        'month_name' => $this->getMonthName($i),
                        'year' => $year,
                        'total_transactions' => $count,
                        'total_raks_rented' => $rakCount,
                        'total_revenue' => $total
                    ]);
                    $yearlyTotal += $total;
                }
            }
        }

        // Chart data for PDF
        $chartData = $this->getChartDataForPdf($year);

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'revenues',
            'year',
            'month',
            'yearlyTotal',
            'chartData'
        ))->setPaper('a4', 'portrait');

        $filename = $month
            ? "laporan-pendapatan-{$year}-{$month}.pdf"
            : "laporan-pendapatan-{$year}.pdf";

        return $pdf->download($filename);
    }

    public function viewPdf(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');

        // Build revenue summary from Transaction data instead of RentalRevenue
        $revenues = collect();
        $yearlyTotal = 0;

        if ($month) {
            // Single month data
            $total = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->sum('amount');

            $count = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->count();

            $rakCount = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $month)
                ->distinct('rak_id')
                ->count('rak_id');

            if ($total > 0 || $count > 0) {
                $revenues->push((object)[
                    'month' => $month,
                    'month_name' => $this->getMonthName($month),
                    'year' => $year,
                    'total_transactions' => $count,
                    'total_raks_rented' => $rakCount,
                    'total_revenue' => $total
                ]);
            }
            $yearlyTotal = $total;
        } else {
            // Year summary data
            for ($i = 1; $i <= 12; $i++) {
                $total = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                    ->whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->sum('amount');

                $count = Transaction::whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->count();

                $rakCount = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                    ->whereYear('transaction_time', $year)
                    ->whereMonth('transaction_time', $i)
                    ->distinct('rak_id')
                    ->count('rak_id');

                if ($total > 0 || $count > 0) {
                    $revenues->push((object)[
                        'month' => $i,
                        'month_name' => $this->getMonthName($i),
                        'year' => $year,
                        'total_transactions' => $count,
                        'total_raks_rented' => $rakCount,
                        'total_revenue' => $total
                    ]);
                    $yearlyTotal += $total;
                }
            }
        }

        // Chart data for PDF
        $chartData = $this->getChartDataForPdf($year);

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'revenues',
            'year',
            'month',
            'yearlyTotal',
            'chartData'
        ));

        $filename = $month
            ? "laporan-pendapatan-{$year}-{$month}.pdf"
            : "laporan-pendapatan-{$year}.pdf";

        return $pdf->stream($filename);
    }

    public function performance(Request $request)
    {
        $year = $request->input('year', now()->year);
        $monthFrom = $request->input('month_from');
        $monthTo = $request->input('month_to');
        $gudangId = $request->input('gudang_id');
        $status = $request->input('status');

        // Determine month range for filtering
        $monthRange = [];
        if ($monthFrom && $monthTo) {
            for ($i = $monthFrom; $i <= $monthTo; $i++) {
                $monthRange[] = $i;
            }
        }

        // Base query for filtering performance data
        $baseQuery = Transaction::query()->whereYear('transaction_time', $year);

        if ($gudangId) {
            $baseQuery->whereHas('rak.gudang', function($q) use ($gudangId) {
                $q->where('id', $gudangId);
            });
        }

        if ($status === 'success') {
            $baseQuery->whereIn('transaction_status', ['capture', 'settlement']);
        } elseif ($status === 'pending') {
            $baseQuery->where('transaction_status', 'pending');
        } elseif ($status === 'failed') {
            $baseQuery->whereIn('transaction_status', ['deny', 'expire', 'cancel']);
        }

        if (!empty($monthRange)) {
            $baseQuery->whereIn(DB::raw('MONTH(transaction_time)'), $monthRange);
        }

        // KPI Metrics with filters applied
        $avgRevenueQuery = clone $baseQuery;
        $avgRevenuePerTransaction = $avgRevenueQuery->whereIn('transaction_status', ['capture', 'settlement'])
            ->avg('amount') ?? 0;

        $currentMonth = now()->month;
        $prevMonth = $currentMonth - 1 ?: 12;
        $prevYear = $prevMonth === 12 ? $year - 1 : $year;

        $currentRevenue = (clone $baseQuery)->whereIn('transaction_status', ['capture', 'settlement'])
            ->whereMonth('transaction_time', $currentMonth)
            ->sum('amount');

        $prevRevenue = (clone $baseQuery)->whereIn('transaction_status', ['capture', 'settlement'])
            ->whereMonth('transaction_time', $prevMonth)
            ->whereYear('transaction_time', $prevYear)
            ->sum('amount');

        $revenueGrowth = $prevRevenue > 0 ? (($currentRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        // Customer Retention with filters applied
        $retentionQuery = (clone $baseQuery)->whereIn('transaction_status', ['capture', 'settlement']);
        $totalUsersWithMultipleTransactions = DB::table(DB::raw("({$retentionQuery->toSql()}) as filtered_transactions"))
            ->mergeBindings($retentionQuery->getQuery())
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $totalUsers = (clone $retentionQuery)->distinct('user_id')->count('user_id');

        $customerRetentionRate = $totalUsers > 0 ? ($totalUsersWithMultipleTransactions / $totalUsers) * 100 : 0;

        // Occupancy Rate
        $totalRaks = Rak::count();
        $occupiedRaks = Rak::where('status', 'terisi')->count();
        $currentOccupancyRate = $totalRaks > 0 ? ($occupiedRaks / $totalRaks) * 100 : 0;

        // Transaction Success Rate with filters applied
        $successRateQuery = clone $baseQuery;
        $totalTransactions = $successRateQuery->count();
        $successfulTransactions = (clone $successRateQuery)->whereIn('transaction_status', ['capture', 'settlement'])->count();
        $transactionSuccessRate = $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0;

        // Charts Data with Filters Applied
        // Customer Growth - Simplified approach
        $growthLabels = [];
        $newCustomerData = [];
        $repeatCustomerData = [];

        // Determine month range for charts
        $chartStartMonth = $monthFrom ?: 1;
        $chartEndMonth = $monthTo ?: 12;

        for ($month = $chartStartMonth; $month <= $chartEndMonth; $month++) {
            $growthLabels[] = $this->getMonthName($month);

            // Get total unique customers this month (for "New Customer" line)
            $totalCustomersThisMonth = (clone $baseQuery)
                ->whereIn('transaction_status', ['capture', 'settlement'])
                ->whereMonth('transaction_time', $month)
                ->distinct('user_id')
                ->count('user_id');

            // Get customers with multiple transactions this month (for "Repeat Customer" line)
            $repeatCustomers = (clone $baseQuery)
                ->whereIn('transaction_status', ['capture', 'settlement'])
                ->whereMonth('transaction_time', $month)
                ->select('user_id', DB::raw('COUNT(*) as transaction_count'))
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 1')
                ->distinct('user_id')
                ->count();

            $newCustomerData[] = $totalCustomersThisMonth;
            $repeatCustomerData[] = $repeatCustomers;
        }

        // Monthly Revenue Data with filters applied
        $trendLabels = [];
        $monthlyRevenueData = [];
        for ($month = $chartStartMonth; $month <= $chartEndMonth; $month++) {
            $trendLabels[] = $this->getMonthName($month);
            $revenueTrendQuery = (clone $baseQuery)->whereIn('transaction_status', ['capture', 'settlement'])
                ->whereMonth('transaction_time', $month);
            $monthlyRevenueData[] = $revenueTrendQuery->sum('amount');
        }

        // Occupancy Trends with month range filtering
        $occupancyLabels = [];
        $occupancyData = [];
        for ($month = $chartStartMonth; $month <= $chartEndMonth; $month++) {
            $occupancyLabels[] = $this->getMonthName($month);

            // Calculate occupancy for each month (would need historical data)
            // For now, using current occupancy as static
            $occupancyData[] = $currentOccupancyRate;
        }

        // Gudang Performance with filters applied
        $gudangPerformance = Gudang::with(['raks'])->get()->map(function($gudang) use ($baseQuery) {
            $gudangQuery = clone $baseQuery;
            $gudangQuery->whereHas('rak.gudang', function($q) use ($gudang) {
                $q->where('id', $gudang->id);
            });

            $totalRevenue = $gudangQuery->whereIn('transaction_status', ['capture', 'settlement'])->sum('amount');

            $totalRaks = $gudang->raks->count();
            $occupiedRaks = $gudang->raks->where('status', 'terisi')->count();
            $occupancyRate = $totalRaks > 0 ? ($occupiedRaks / $totalRaks) * 100 : 0;

            return (object) [
                'nama_gudang' => $gudang->nama_gudang,
                'total_revenue' => $totalRevenue,
                'occupancy_rate' => $occupancyRate
            ];
        })->sortByDesc('total_revenue');

        $totalRepeatCustomers = $totalUsersWithMultipleTransactions;

        return view('admin.laporan.performance', compact(
            'year',
            'avgRevenuePerTransaction',
            'revenueGrowth',
            'customerRetentionRate',
            'currentOccupancyRate',
            'transactionSuccessRate',
            'totalRaks',
            'occupiedRaks',
            'totalTransactions',
            'successfulTransactions',
            'growthLabels',
            'newCustomerData',
            'repeatCustomerData',
            'trendLabels',
            'monthlyRevenueData',
            'occupancyLabels',
            'occupancyData',
            'gudangPerformance',
            'totalRepeatCustomers'
        ));
    }

    public function sync()
    {
        RevenueService::syncAllRevenues();

        return redirect()->route('admin.laporan.pendapatan')
            ->with('success', 'Data laporan berhasil disinkronkan');
    }

    private function getChartData(int $year): array
    {
        $revenues = RentalRevenue::where('year', $year)
            ->orderBy('month')
            ->get();

        $months = [];
        $amounts = [];

        for ($i = 1; $i <= 12; $i++) {
            $revenue = $revenues->firstWhere('month', $i);
            $months[] = $this->getMonthName($i);
            $amounts[] = $revenue ? $revenue->total_revenue : 0;
        }

        return compact('months', 'amounts');
    }

    private function getChartDataForPdf(int $year): array
    {
        // Monthly transaction data for chart
        $transaksiLabels = [];
        $transaksiData = [];

        // Monthly revenue data for chart
        $pendapatanLabels = [];
        $pendapatanData = [];

        // Rack status data
        $rakTerisi = Rak::where('status', 'terisi')->count();
        $rakMaintenance = Rak::where('status', 'maintenance')->count();
        $rakTersedia = Rak::where('status', 'tersedia')->count();

        // Transaction status data
        $statusSuccess = Transaction::whereIn('transaction_status', ['capture', 'settlement'])->count();
        $statusPending = Transaction::where('transaction_status', 'pending')->count();
        $statusFailed = Transaction::whereIn('transaction_status', ['deny', 'expire', 'cancel'])->count();

        // Monthly transaction data
        for ($i = 1; $i <= 12; $i++) {
            $transaksiLabels[] = $this->getMonthName($i);
            $count = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $i)
                ->count();
            $transaksiData[] = $count;
        }

        // Monthly revenue data
        for ($i = 1; $i <= 12; $i++) {
            $pendapatanLabels[] = $this->getMonthName($i);
            $total = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $year)
                ->whereMonth('transaction_time', $i)
                ->sum('amount');
            $pendapatanData[] = $total;
        }

        return compact(
            'transaksiLabels',
            'transaksiData',
            'pendapatanLabels',
            'pendapatanData',
            'rakTerisi',
            'rakMaintenance',
            'rakTersedia',
            'statusSuccess',
            'statusPending',
            'statusFailed'
        );
    }

    private function getMonthName(int $month): string
    {
        $names = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $names[$month] ?? '';
    }
}

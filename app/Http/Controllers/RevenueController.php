<?php

namespace App\Http\Controllers;

use App\Models\RentalRevenue;
use App\Models\Transaction;
use App\Services\RevenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');

        $query = RentalRevenue::where('year', $year);

        if ($month) {
            $query->where('month', $month);
        }

        $revenues = $query->orderBy('month', 'desc')->get();

        $yearlyTotal = $revenues->sum('total_revenue');
        $yearlyTransactions = $revenues->sum('total_transactions');

        $availableYears = Transaction::selectRaw('DISTINCT YEAR(transaction_time) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $chartData = $this->getChartData($year);

        return view('admin.laporan.pendapatan', compact(
            'revenues',
            'year',
            'month',
            'yearlyTotal',
            'yearlyTransactions',
            'availableYears',
            'chartData'
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

    public function exportPdf(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');

        $query = RentalRevenue::where('year', $year);

        if ($month) {
            $query->where('month', $month);
        }

        $revenues = $query->orderBy('month')->get();
        $yearlyTotal = $revenues->sum('total_revenue');

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'revenues',
            'year',
            'month',
            'yearlyTotal'
        ));

        $filename = $month 
            ? "laporan-pendapatan-{$year}-{$month}.pdf"
            : "laporan-pendapatan-{$year}.pdf";

        return $pdf->download($filename);
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

    private function getMonthName(int $month): string
    {
        $names = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        
        return $names[$month] ?? '';
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\User;
use App\Models\Rak;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Data untuk card statistik
        $totalGudang = Gudang::count();
        $totalPelanggan = User::where('role', 'customer')->count();
        $totalRak = Rak::count();
        $totalTransaksi = Transaction::count();
        
        // Total Pendapatan (hanya transaksi sukses)
        $totalPendapatan = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
            ->sum('amount');
        
        // Transaksi Bulan Ini
        $transaksiBulanIni = Transaction::whereMonth('transaction_time', now()->month)
            ->whereYear('transaction_time', now()->year)
            ->count();
        
        // Pendapatan Bulan Ini
        $pendapatanBulanIni = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
            ->whereMonth('transaction_time', now()->month)
            ->whereYear('transaction_time', now()->year)
            ->sum('amount');

        // ===== GRAFIK TRANSAKSI BULANAN (12 BULAN TERAKHIR) =====
        $transaksiLabels = [];
        $transaksiData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $transaksiLabels[] = $date->format('M Y');
            
            $count = Transaction::whereYear('transaction_time', $date->year)
                ->whereMonth('transaction_time', $date->month)
                ->count();
            
            $transaksiData[] = $count;
        }

        // ===== GRAFIK STATUS RAK =====
        $rakTerisi = Rak::where('status', 'terisi')->count();
        $rakMaintenance = Rak::where('status', 'maintenance')->count();
        $rakTersedia = Rak::where('status', 'tersedia')->count();

        // Jika tidak ada data rak, set default untuk menghindari chart kosong
        if ($rakTerisi == 0 && $rakMaintenance == 0 && $rakTersedia == 0) {
            $rakTersedia = 1;
        }

        // ===== GRAFIK PENDAPATAN (6 BULAN TERAKHIR) =====
        $pendapatanLabels = [];
        $pendapatanData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $pendapatanLabels[] = $date->format('M Y');
            
            $total = Transaction::whereIn('transaction_status', ['capture', 'settlement'])
                ->whereYear('transaction_time', $date->year)
                ->whereMonth('transaction_time', $date->month)
                ->sum('amount');
            
            $pendapatanData[] = $total;
        }

        // ===== GRAFIK STATUS TRANSAKSI =====
        $statusSuccess = Transaction::whereIn('transaction_status', ['capture', 'settlement'])->count();
        $statusPending = Transaction::where('transaction_status', 'pending')->count();
        $statusFailed = Transaction::whereIn('transaction_status', ['deny', 'expire', 'cancel'])->count();

        // ===== TRANSAKSI TERBARU =====
        $recentTransactions = Transaction::with(['user', 'rak'])
            ->latest('transaction_time')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalGudang',
            'totalPelanggan',
            'totalRak',
            'totalTransaksi',
            'totalPendapatan',
            'transaksiBulanIni',
            'pendapatanBulanIni',
            'transaksiLabels',
            'transaksiData',
            'rakTerisi',
            'rakMaintenance',
            'rakTersedia',
            'pendapatanLabels',
            'pendapatanData',
            'statusSuccess',
            'statusPending',
            'statusFailed',
            'recentTransactions'
        ));
    }
}
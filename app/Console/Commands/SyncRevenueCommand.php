<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevenueService;

class SyncRevenueCommand extends Command
{
    protected $signature = 'revenue:sync {year?} {month?}';
    
    protected $description = 'Sinkronisasi laporan pendapatan dari transaksi';

    public function handle()
    {
        $this->info('Memulai sinkronisasi laporan pendapatan...');

        $year = $this->argument('year');
        $month = $this->argument('month');

        try {
            if ($year && $month) {
                RevenueService::generateMonthlyReport($year, $month);
                $this->info("Laporan bulan {$month}/{$year} berhasil disinkronkan");
            } elseif ($year) {
                RevenueService::generateYearlyReport($year);
                $this->info("Laporan tahun {$year} berhasil disinkronkan");
            } else {
                RevenueService::syncAllRevenues();
                $this->info('Semua laporan berhasil disinkronkan');
            }

            $this->newLine();
            $this->info('✓ Sinkronisasi selesai!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Rak;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOverdueTransactions extends Command
{
    protected $signature = 'transactions:check-overdue';
    protected $description = 'Check and update overdue transactions automatically';

    public function handle()
    {
        $this->info('🔍 Checking for overdue transactions...');
        
        try {
            // Cari transaksi yang sudah settlement tapi masa sewa sudah berakhir
            $transactions = Transaction::with('rak')
                ->where('transaction_status', 'settlement')
                ->whereDate('sewa_berakhir', '<', Carbon::now())
                ->whereDoesntHave('renewals', function($query) {
                    $query->where('transaction_status', 'settlement');
                })
                ->get();
            
            $expiredCount = 0;
            $warningCount = 0;
            
            foreach ($transactions as $transaction) {
                $daysOverdue = Carbon::now()->diffInDays($transaction->sewa_berakhir);
                
                // Jika sudah lewat lebih dari 3 hari, tandai sebagai expired
                if ($daysOverdue > 3) {
                    $transaction->update(['transaction_status' => 'expired']);
                    
                    // Update status rak menjadi tersedia
                    if ($transaction->rak) {
                        $transaction->rak->update(['status' => 'tersedia']);
                    }
                    
                    $expiredCount++;
                    
                    Log::info('Transaction auto-expired', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->user_id,
                        'rak_id' => $transaction->rak_id,
                        'sewa_berakhir' => $transaction->sewa_berakhir,
                        'days_overdue' => $daysOverdue
                    ]);
                    
                    $this->info("✅ Transaction #{$transaction->id} marked as expired (overdue {$daysOverdue} days)");
                    
                } else if ($daysOverdue > 0) {
                    // Hanya log warning jika baru 1-3 hari terlambat
                    $warningCount++;
                    Log::warning('Transaction overdue but not yet expired', [
                        'transaction_id' => $transaction->id,
                        'days_overdue' => $daysOverdue
                    ]);
                    
                    $this->warn("⚠️ Transaction #{$transaction->id} is {$daysOverdue} day(s) overdue");
                }
            }
            
            $this->newLine();
            $this->info("📊 Summary:");
            $this->info("Total transactions checked: " . $transactions->count());
            $this->info("Expired transactions: {$expiredCount}");
            $this->info("Warning transactions: {$warningCount}");
            
            if ($expiredCount > 0) {
                $this->info("📧 Email notifications can be sent here if needed");
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error checking overdue transactions: ' . $e->getMessage());
            Log::error('CheckOverdueTransactions Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
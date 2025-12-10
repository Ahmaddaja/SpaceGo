<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Rak;
use App\Services\HistoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckExpiredRentals extends Command
{
    protected $signature = 'rentals:check-expired';
    protected $description = 'Check and update expired rentals (37+ days past due)';

    public function handle()
    {
        $this->info('Starting expired rentals check...');

        $currentDbTime = DB::selectOne('SELECT NOW() as db_time')->db_time;
        $now = Carbon::parse($currentDbTime);

        $expiredTransactions = Transaction::whereIn('transaction_status', ['settlement', 'capture'])
            ->where('is_dikosongkan', false)
            ->whereNotNull('sewa_berakhir')
            ->get();

        $this->info("Found {$expiredTransactions->count()} active transactions to check.");

        $updatedCount = 0;
        $progressBar = $this->output->createProgressBar($expiredTransactions->count());

        foreach ($expiredTransactions as $transaction) {
            $end = Carbon::parse($transaction->sewa_berakhir);
            $daysPassed = $now->diffInDays($end, false);

            if ($daysPassed < -37) {
                DB::beginTransaction();
                
                try {
                    $rak = Rak::find($transaction->rak_id);
                    
                    if ($rak && in_array($rak->status, ['terisi', 'pengosongan'])) {
                        $rak->update(['status' => 'tersedia']);
                        
                        $transaction->update([
                            'is_dikosongkan' => true,
                            'dikosongkan_at' => $now
                        ]);
                        
                        $updatedCount++;
                        
                        $this->line("\n✓ Rak {$rak->kode_rak} dikosongkan (Transaction ID: {$transaction->id})");
                        
                        Log::info('Command: Rak dikosongkan otomatis', [
                            'rak_id' => $rak->id,
                            'transaction_id' => $transaction->id,
                            'days_passed' => abs($daysPassed)
                        ]);
                    }
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("\n✗ Error processing transaction {$transaction->id}: " . $e->getMessage());
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->newLine(2);
        $this->info("✓ Check completed!");
        $this->info("  - Checked: {$expiredTransactions->count()} transactions");
        $this->info("  - Updated: {$updatedCount} rak(s) dikosongkan");

        return Command::SUCCESS;
    }
}
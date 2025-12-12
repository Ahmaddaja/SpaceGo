<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Log;

class UpdateRakStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rak:update-status {--user_id=}';

    /**
     * The console command description.
     */
    protected $description = 'Update status rak yang sedang disewa berdasarkan durasi sewa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai update status rak...');
        
        $query = Tagihan::where('status', 'settlement')
            ->whereNotNull('sewa_berakhir');

        // Filter by user if provided
        if ($this->option('user_id')) {
            $query->where('user_id', $this->option('user_id'));
        }

        $tagihans = $query->get();
        
        $this->info("Ditemukan {$tagihans->count()} tagihan untuk diupdate");

        $updated = 0;
        $bar = $this->output->createProgressBar($tagihans->count());
        $bar->start();

        foreach ($tagihans as $tagihan) {
            try {
                $oldStatus = $tagihan->status_rak;
                $tagihan->updateStatusRak();
                $newStatus = $tagihan->fresh()->status_rak;

                if ($oldStatus !== $newStatus) {
                    $updated++;
                    Log::info("Status rak updated", [
                        'tagihan_id' => $tagihan->id,
                        'rak_id' => $tagihan->rak_id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus
                    ]);
                }

                $bar->advance();
            } catch (\Exception $e) {
                Log::error("Error updating tagihan {$tagihan->id}: " . $e->getMessage());
                $this->error("\nError updating tagihan {$tagihan->id}");
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Update selesai! {$updated} status berubah dari {$tagihans->count()} tagihan.");
        
        return Command::SUCCESS;
    }
}
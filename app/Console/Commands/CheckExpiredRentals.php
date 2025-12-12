<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use App\Models\Rak;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckExpiredRentals extends Command
{
    protected $signature = 'rentals:check-expired';
    protected $description = 'Check and force-release rak that are over 37 days past the end of rental period';

    public function handle()
    {
        $this->info('🔍 Memulai pengecekan expired rentals (37+ hari)...');

        // Ambil waktu langsung dari database agar konsisten
        $currentDbTime = DB::selectOne('SELECT NOW() AS db_time')->db_time;
        $now = Carbon::parse($currentDbTime);

        // Tagihan yang valid untuk dicek
        $tagihans = Tagihan::where('status', 'settlement')
            ->where('is_dikosongkan', false)
            ->whereNotNull('sewa_berakhir')
            ->get();

        $this->info("Ditemukan {$tagihans->count()} tagihan untuk dicek.");
        $progress = $this->output->createProgressBar($tagihans->count());

        $updatedCount = 0;

        foreach ($tagihans as $tagihan) {

            $end = Carbon::parse($tagihan->sewa_berakhir);
            $daysPassed = $now->diffInDays($end, false);

            // Jika sudah lebih dari 37 hari lewat dari masa sewa berakhir
            if ($daysPassed < -37) {

                DB::beginTransaction();
                try {

                    $rak = Rak::find($tagihan->rak_id);

                    if ($rak && in_array($rak->status, ['terisi', 'pengosongan'])) {

                        // Kosongkan rak
                        $rak->update(['status' => 'tersedia']);

                        // Update tagihan
                        $tagihan->update([
                            'is_dikosongkan' => true,
                            'dikosongkan_at' => $now,
                        ]);

                        $updatedCount++;

                        $this->line("\n✓ Rak {$rak->kode_rak} dikosongkan paksa (Tagihan ID: {$tagihan->id})");

                        Log::info('Command: Rak dikosongkan otomatis (37+ hari)', [
                            'rak_id' => $rak->id,
                            'tagihan_id' => $tagihan->id,
                            'days_passed' => abs($daysPassed)
                        ]);
                    }

                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("\n✗ Error pada tagihan ID {$tagihan->id}: " . $e->getMessage());
                }
            }

            $progress->advance();
        }

        $progress->finish();
        $this->newLine(2);

        $this->info("=== RINGKASAN ===");
        $this->info("Total dicek   : {$tagihans->count()}");
        $this->info("Rak dikosongkan: {$updatedCount}");
        $this->info("Selesai!");

        return Command::SUCCESS;
    }
}

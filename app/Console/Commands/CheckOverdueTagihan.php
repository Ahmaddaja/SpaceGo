<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use App\Models\Rak;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOverdueTagihan extends Command
{
    protected $signature = 'tagihan:check-overdue';
    protected $description = 'Check overdue rental based on Tagihan (not Transaction)';

    public function handle()
    {
        $this->info("🔍 Checking overdue tagihan...");

        try {
            // Ambil semua tagihan aktif (settlement = sewa berjalan)
            $tagihans = Tagihan::with('rak')
                ->where('status', 'settlement')
                ->whereNotNull('sewa_berakhir')
                ->whereDate('sewa_berakhir', '<', now())
                ->get();

            $expiredCount = 0;
            $warningCount = 0;
            $pengosonganCount = 0;

            $gracePeriodDays = 3;
            $maxLateDays = 30;

            foreach ($tagihans as $tagihan) {

                $daysLate = now()->diffInDays($tagihan->sewa_berakhir);

                // ================================
                // 1. CEK PENGOSONGAN ( > 30 HARI )
                // ================================
                if ($daysLate > $maxLateDays && !$tagihan->is_pengosongan) {

                    $tagihan->update([
                        'is_pengosongan' => true,
                        'pengosongan_dimulai' => now()
                    ]);

                    $pengosonganCount++;

                    Log::warning('Tagihan masuk proses pengosongan', [
                        'tagihan_id' => $tagihan->id,
                        'user_id' => $tagihan->user_id,
                        'rak_id' => $tagihan->rak_id,
                        'days_late' => $daysLate,
                    ]);

                    $this->warn("🚨 Tagihan #{$tagihan->id} masuk PENGOSONGAN (terlambat {$daysLate} hari)");
                    continue;
                }

                // ======================================
                // 2. CEK EXPIRED ( > 3 HARI SETELAH TENGGANG )
                // ======================================
                if ($daysLate > $gracePeriodDays) {

                    $tagihan->update([
                        'status' => 'expired',
                        'expired_at' => now()
                    ]);

                    // Rak menjadi tersedia
                    if ($tagihan->rak) {
                        $tagihan->rak->update(['status' => 'tersedia']);
                    }

                    $expiredCount++;

                    Log::info('Tagihan expired otomatis', [
                        'tagihan_id' => $tagihan->id,
                        'user_id' => $tagihan->user_id,
                        'rak_id' => $tagihan->rak_id,
                        'days_late' => $daysLate
                    ]);

                    $this->info("❌ Tagihan #{$tagihan->id} EXPIRED (telat {$daysLate} hari)");
                    continue;
                }

                // ======================================
                // 3. MASIH DALAM MASA TENGGANG ( 1–3 HARI )
                // ======================================
                if ($daysLate > 0) {

                    $warningCount++;

                    Log::warning('Tagihan melewati jatuh tempo, masih masa tenggang', [
                        'tagihan_id' => $tagihan->id,
                        'days_late' => $daysLate,
                    ]);

                    $this->warn("⚠️ Tagihan #{$tagihan->id} telat {$daysLate} hari (MASA TENGGANG)");
                }
            }

            // ========================
            // SUMMARY
            // ========================
            $this->newLine();
            $this->info("📊 Summary:");
            $this->info("Total tagihan dicek: " . $tagihans->count());
            $this->info("Expired: {$expiredCount}");
            $this->info("Warning (tenggang): {$warningCount}");
            $this->info("Masuk pengosongan (>30 hari): {$pengosonganCount}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            Log::error("CheckOverdueTagihan Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

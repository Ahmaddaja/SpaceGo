<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Rak;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckPengosonganStatus extends Command
{
    protected $signature = 'rak:check-pengosongan';
    protected $description = 'Check dan update status rak untuk pengosongan dan kembali tersedia';

    public function handle()
    {
        $this->info('Memulai pengecekan status pengosongan...');

        DB::beginTransaction();
        try {
            // Konstanta
            $gracePeriodDays = 3;
            $maxLateDays = 30; // Setelah 30 hari lewat grace period = pengosongan
            $pengosonganDuration = 7; // Durasi pengosongan 7 hari

            // 1. CEK TRANSAKSI YANG HARUS MASUK PENGOSONGAN
            $transactions = Transaction::whereIn('transaction_status', ['settlement', 'capture'])
                ->where('is_pengosongan', false)
                ->whereNotNull('sewa_berakhir')
                ->get();

            $pengosonganCount = 0;

            foreach ($transactions as $transaction) {
                $now = Carbon::now()->startOfDay();
                $end = Carbon::parse($transaction->sewa_berakhir)->startOfDay();
                $daysDiff = $now->diffInDays($end, false);

                // Jika sudah lewat grace period + 30 hari
                $totalLateDays = abs($daysDiff) - $gracePeriodDays;

                if ($daysDiff < 0 && $totalLateDays >= $maxLateDays) {
                    // Masuk masa pengosongan
                    $rak = Rak::find($transaction->rak_id);
                    
                    if ($rak && $rak->status !== 'pengosongan') {
                        $pengosonganMulai = $now;
                        $pengosonganBerakhir = $now->copy()->addDays($pengosonganDuration);

                        $transaction->update([
                            'is_pengosongan' => true,
                            'pengosongan_dimulai' => $pengosonganMulai,
                            'pengosongan_berakhir' => $pengosonganBerakhir,
                        ]);

                        $rak->update(['status' => 'pengosongan']);

                        $pengosonganCount++;

                        Log::info('Rak masuk masa pengosongan', [
                            'rak_id' => $rak->id,
                            'kode_rak' => $rak->kode_rak,
                            'transaction_id' => $transaction->id,
                            'pengosongan_mulai' => $pengosonganMulai,
                            'pengosongan_berakhir' => $pengosonganBerakhir,
                            'days_late' => $totalLateDays
                        ]);

                        $this->info("✓ Rak {$rak->kode_rak} masuk masa pengosongan");
                    }
                }
            }

            // 2. CEK RAK YANG MASA PENGOSONGANNYA SUDAH BERAKHIR
            $pengosonganTransactions = Transaction::where('is_pengosongan', true)
                ->whereNotNull('pengosongan_berakhir')
                ->get();

            $tersediaCount = 0;

            foreach ($pengosonganTransactions as $transaction) {
                $now = Carbon::now();
                $pengosonganEnd = Carbon::parse($transaction->pengosongan_berakhir);

                // Jika masa pengosongan sudah lewat
                if ($now->greaterThanOrEqualTo($pengosonganEnd)) {
                    $rak = Rak::find($transaction->rak_id);
                    
                    if ($rak && $rak->status === 'pengosongan') {
                        $rak->update(['status' => 'tersedia']);

                        $tersediaCount++;

                        Log::info('Rak kembali tersedia setelah pengosongan', [
                            'rak_id' => $rak->id,
                            'kode_rak' => $rak->kode_rak,
                            'transaction_id' => $transaction->id,
                            'pengosongan_berakhir' => $pengosonganEnd
                        ]);

                        $this->info("✓ Rak {$rak->kode_rak} kembali tersedia");
                    }
                }
            }

            DB::commit();

            $this->info("\n=== RINGKASAN ===");
            $this->info("Rak masuk pengosongan: {$pengosonganCount}");
            $this->info("Rak kembali tersedia: {$tersediaCount}");
            $this->info("Pengecekan selesai!");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error checking pengosongan status: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            return 1;
        }
    }
}
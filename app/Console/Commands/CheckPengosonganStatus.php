<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use App\Models\Rak;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckPengosonganStatus extends Command
{
    protected $signature = 'rak:check-pengosongan';
    protected $description = 'Check dan update status rak untuk pengosongan berdasarkan tagihan settlement';

    public function handle()
    {
        $this->info('🔍 Memulai pengecekan status pengosongan berbasis Tagihan...');

        DB::beginTransaction();
        try {
            // Konfigurasi
            $gracePeriodDays = 3;
            $maxLateDays = 30;
            $pengosonganDuration = 7;

            /*
            |--------------------------------------------------------------------------
            | 1. CEK TAGIHAN YANG HARUS MASUK PENGOSONGAN
            |--------------------------------------------------------------------------
            */
            $tagihans = Tagihan::where('status', 'settlement')
                ->where('is_pengosongan', false)
                ->whereNotNull('sewa_berakhir')
                ->get();

            $masukPengosongan = 0;

            foreach ($tagihans as $tagihan) {

                $now = Carbon::now()->startOfDay();
                $end = Carbon::parse($tagihan->sewa_berakhir)->startOfDay();

                // Diff dalam hari (negatif = sudah lewat)
                $daysDiff = $now->diffInDays($end, false);

                // Lewat masa sewa
                if ($daysDiff < 0) {
                    $totalLate = abs($daysDiff) - $gracePeriodDays;

                    // Jika total keterlambatan melebihi 30 hari
                    if ($totalLate >= $maxLateDays) {
                        $rak = Rak::find($tagihan->rak_id);

                        if ($rak && $rak->status !== 'pengosongan') {

                            $mulai = $now;
                            $selesai = $now->copy()->addDays($pengosonganDuration);

                            $tagihan->update([
                                'is_pengosongan' => true,
                                'pengosongan_dimulai' => $mulai,
                                'pengosongan_berakhir' => $selesai,
                            ]);

                            $rak->update(['status' => 'pengosongan']);

                            $masukPengosongan++;

                            Log::info("Rak masuk pengosongan via Tagihan", [
                                'tagihan_id' => $tagihan->id,
                                'rak_id' => $rak->id,
                                'mulai' => $mulai,
                                'selesai' => $selesai,
                                'telat' => $totalLate
                            ]);

                            $this->info("✓ Rak {$rak->kode_rak} masuk masa pengosongan");
                        }
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 2. CEK TAGIHAN YANG MASA PENGOSONGANNYA SUDAH HABIS
            |--------------------------------------------------------------------------
            */
            $ongoing = Tagihan::where('is_pengosongan', true)
                ->whereNotNull('pengosongan_berakhir')
                ->get();

            $selesaiPengosongan = 0;

            foreach ($ongoing as $tagihan) {

                $now = Carbon::now();
                $pengosonganEnd = Carbon::parse($tagihan->pengosongan_berakhir);

                if ($now->greaterThanOrEqualTo($pengosonganEnd)) {

                    $rak = Rak::find($tagihan->rak_id);

                    if ($rak && $rak->status === 'pengosongan') {

                        $rak->update(['status' => 'tersedia']);

                        // Tandai finalisasi
                        $tagihan->update([
                            'is_dikosongkan' => true,
                            'dikosongkan_at' => now(),
                        ]);

                        $selesaiPengosongan++;

                        Log::info("Rak kembali tersedia setelah pengosongan", [
                            'tagihan_id' => $tagihan->id,
                            'rak_id' => $rak->id,
                            'pengosongan_berakhir' => $pengosonganEnd,
                        ]);

                        $this->info("✓ Rak {$rak->kode_rak} kembali tersedia");
                    }
                }
            }

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | RINGKASAN
            |--------------------------------------------------------------------------
            */
            $this->info("\n=== RINGKASAN ===");
            $this->info("Rak masuk masa pengosongan : {$masukPengosongan}");
            $this->info("Rak kembali tersedia       : {$selesaiPengosongan}");
            $this->info("Pengecekan selesai!");

            return 0;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error("CheckPengosonganStatus Error: {$e->getMessage()}");
            Log::error($e->getTraceAsString());

            $this->error("❌ Terjadi kesalahan: {$e->getMessage()}");

            return 1;
        }
    }
}

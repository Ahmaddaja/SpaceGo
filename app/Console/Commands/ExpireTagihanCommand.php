<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireTagihanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire tagihan yang sudah lewat 24 jam (berdasarkan database time)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired tagihan...');

        try {
            DB::beginTransaction();

            // Ambil current time dari DATABASE, bukan device
            $currentDbTime = DB::selectOne('SELECT NOW() as db_time')->db_time;
            $now = Carbon::parse($currentDbTime);

            $this->info("Current DB Time: {$now}");

            // Cari tagihan pending yang expired_at sudah lewat
            $expiredTagihan = Tagihan::where('status', 'pending')
                ->where('expired_at', '<=', $now)
                ->get();

            $count = $expiredTagihan->count();

            if ($count === 0) {
                $this->info('No expired tagihan found.');
                DB::commit();
                return Command::SUCCESS;
            }

            $this->info("Found {$count} expired tagihan. Processing...");

            foreach ($expiredTagihan as $tagihan) {
                // Update status tagihan
                $tagihan->update([
                    'status' => 'expired',
                    'cancelled_at' => $now,
                ]);

                // Sync ke transaction
                $transaction = $tagihan->transaction;
                if ($transaction && $transaction->transaction_status === 'pending') {
                    $transaction->update([
                        'transaction_status' => 'expired',
                        'updated_at' => $now,
                    ]);

                    // Update rak jadi tersedia jika perlu
                    if ($transaction->rak && $transaction->rak->status !== 'tersedia') {
                        $transaction->rak->update([
                            'status' => 'tersedia',
                            'updated_at' => $now,
                        ]);
                    }
                }

                $this->line("✓ Expired: Tagihan #{$tagihan->id} - {$tagihan->tagihan_code}");

                Log::info('Tagihan auto-expired', [
                    'tagihan_id' => $tagihan->id,
                    'tagihan_code' => $tagihan->tagihan_code,
                    'transaction_id' => $tagihan->transaction_id,
                    'expired_at' => $tagihan->expired_at,
                    'current_time' => $now,
                ]);
            }

            DB::commit();

            $this->info("✓ Successfully expired {$count} tagihan.");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('Error expiring tagihan: ' . $e->getMessage());

            Log::error('ExpireTagihanCommand failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
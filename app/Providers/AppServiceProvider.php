<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Midtrans\Config;
use Illuminate\Pagination\Paginator;
use App\Models\Transaction;
use App\Observers\TransactionObserver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Tagihan;
use App\Observers\TagihanObserver;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        //Observer Tagihan
         Tagihan::observe(TagihanObserver::class);

        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION') === 'true';
        Config::$isSanitized = true;
        Config::$is3ds = true;

        Paginator::useBootstrap();
        Transaction::observe(TransactionObserver::class);

        // Check expired tagihan
        $this->checkExpiredTagihan();
    }

    private function checkExpiredTagihan()
    {
        try {
            Log::info('🚀 Checking expired tagihan');
            
            $now = Carbon::now();
            
            // Cari tagihan pending yang expired
            $expiredTagihan = Tagihan::where('status', 'pending')
                ->where('expired_at', '<=', $now)
                ->get();

            if ($expiredTagihan->isEmpty()) {
                return;
            }

            DB::beginTransaction();

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

                    // Update rak jadi tersedia
                    if ($transaction->rak && $transaction->rak->status !== 'tersedia') {
                        $transaction->rak->update([
                            'status' => 'tersedia',
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            DB::commit();
            Log::info("✅ Successfully expired {$expiredTagihan->count()} tagihan");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ExpireTagihan failed: ' . $e->getMessage());
        }
    }
}

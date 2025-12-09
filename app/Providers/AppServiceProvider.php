<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Midtrans\Config;
use Illuminate\Pagination\Paginator;
use App\Models\Transaction;
use App\Observers\TransactionObserver;
use Illuminate\Support\Facades\Cache;      // ✅ TAMBAHIN INI
use Illuminate\Support\Facades\Artisan;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
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
        if (!Cache::has('tagihan_expire_last_check')) {
            dispatch(function() {
                Artisan::call('tagihan:expire');
            })->afterResponse();
            
            Cache::put('tagihan_expire_last_check', now(), now()->addMinute());
        }
    }
}

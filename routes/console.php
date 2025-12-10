<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Console Commands
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:check-overdue', function () {
    $this->call('transactions:check-overdue');
})->purpose('Test check overdue transactions manually');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks (Laravel 11 / 12)
|--------------------------------------------------------------------------
*/

// ✅ 1. Expire tagihan tiap 1 menit
Schedule::command('tagihan:expire')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        \Log::info('Tagihan expire command completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Tagihan expire command failed');
    });

// ✅ 2. Auto check expired rental harian jam 00:00
Schedule::call(function () {
    app(PaymentController::class)->autoCheckExpiredRentals();
})
->dailyAt('00:00')
->name('auto-check-expired-rentals');

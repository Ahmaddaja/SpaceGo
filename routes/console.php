<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:check-overdue', function () {
    $this->call('transactions:check-overdue');
})->purpose('Test check overdue transactions manually');

// Schedule command expire tagihan setiap 1 menit
Schedule::command('tagihan:expire')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Alternatif: Setiap 5 menit jika load server tinggi
// Schedule::command('tagihan:expire')->everyFiveMinutes();

// Log ketika command dijalankan
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
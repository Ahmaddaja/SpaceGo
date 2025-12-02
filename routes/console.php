<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:check-overdue', function () {
    $this->call('transactions:check-overdue');
})->purpose('Test check overdue transactions manually');
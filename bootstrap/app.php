<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Schedule untuk mengecek transaksi overdue setiap hari jam 00:00
        $schedule->command('transactions:check-overdue')->daily();
        
        // Atau jika mau lebih sering untuk testing di development
        if (app()->environment('local')) {
            $schedule->command('transactions:check-overdue')->everyMinute();
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
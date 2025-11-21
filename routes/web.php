<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Route untuk Customer (default user)
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('users.dashboard');
    })->name('dashboard');

    // Tambahkan route customer lainnya di sini
    // Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

// Route khusus Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Tambahkan route admin lainnya di sini
    // Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
});

// Route Profile (untuk semua user yang login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\ProfileAdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\DashboardController;


// Halaman awal
Route::get('/', function () {
    return view('welcome');
});

// Route untuk Customer (default user)
Route::middleware(['auth', 'role:customer'])->group(function () {

    // Dashboard Customer
    Route::get('/customer', [CustomerController::class, 'dashboard'])
        ->name('customer.index');

    Route::get('/dashboard', function () {
        return view('customer.index');
    })->name('dashboard');
    
    // Profile
    Route::get('/customer/profile', [ProfileController::class, 'index'])->name('customer.profile.index');
    Route::put('/customer/profile', [ProfileController::class, 'updateProfile'])->name('customer.profile.update');
    Route::post('/customer/profile/upload-foto', [ProfileController::class, 'uploadFoto'])->name('customer.profile.upload-foto');

    // List Rak
    Route::get('/customer/rak', [CustomerController::class, 'listRak'])
        ->name('customer.list-rak.list-rak');
        
    Route::get('/customer/rak/{id}', [CustomerController::class, 'showRak'])
        ->name('customer.list-rak.show');

    // Bayar rak
    Route::get('/customer/bayar/{id}', function ($id) {
        return "Halaman pembayaran rak ID: " . $id;
    })->name('customer.bayar');
});


// Route khusus Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
 Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->name('admin.dashboard');
    Route::resource('raks', RakController::class);
    Route::get('/admin/profile', [ProfileAdminController::class, 'index'])->name('admin.profile.index');
    Route::put('/admin/profile', [ProfileAdminController::class, 'update'])->name('admin.profile.update');
    Route::put('/admin/profile/password', [ProfileAdminController::class, 'updatePassword'])->name('admin.profile.updatePassword');
    Route::delete('/notif/{id}', [NotificationController::class, 'delete'])->name('notif.delete');
    Route::resource('gudangs', GudangController::class);
    Route::get('/customers', [CustomerController::class, 'index'])
    ->name('admin.pelanggan.pelanggan');
});

// Route Profile (untuk semua user yang login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================
// Route Login & Logout
// ========================

// Form Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Proses Login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// ========================
// Route Register
// ========================
// Form Register
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');

// Proses Register
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
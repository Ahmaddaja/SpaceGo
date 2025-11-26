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
use App\Http\Controllers\PaymentController;
// TAMBAHKAN INI - Import HistoryController
use App\Http\Controllers\HistoryController;

// Halaman awal
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-midtrans', function () {
    return config('midtrans');
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
    Route::get('/rak-dibeli', [RakController::class, 'rakDibeli'])->name('customer.list-rak.rak');
    Route::get('/rak-dibeli/{id}', [RakController::class, 'detailRak'])->name('customer.list-rak.detail');

    Route::get('/customer/rak/{id}', [CustomerController::class, 'showRak'])
        ->name('customer.list-rak.show');

    // Bayar rak - TAMBAHKAN ROUTE INI
    Route::get('/customer/bayar/{id}', [PaymentController::class, 'bayar'])
        ->name('customer.bayar'); // Tambahkan nama route ini

    // Route payment yang sudah ada
    Route::get('/customer/bayar/{id}', [PaymentController::class, 'bayar'])
        ->name('customer.payment.checkout');

    //callback
    Route::post('/midtrans/callback', [PaymentController::class, 'callback'])->name('midtrans.callback');

    Route::get('/customer/list-rak/rak', [RakController::class, 'rakDibeli'])
        ->name('customer.list-rak.rak');

    // Route untuk halaman checkout/pembayaran
    Route::get('/payment/checkout/{id}', [PaymentController::class, 'bayar'])
        ->name('customer.payment.checkout');

    // Route untuk update status dari frontend (setelah bayar di popup)
    Route::post('/payment/update-status', [PaymentController::class, 'updateStatus'])
        ->name('payment.update-status');

    // ===========================================
    // TAMBAHKAN ROUTE HISTORY CUSTOMER DI SINI
    // ===========================================
    Route::prefix('customer/history')->group(function () {
        // Semua history aktivitas
        Route::get('/', [HistoryController::class, 'index'])->name('customer.history');
        
        // History pembayaran khusus
        Route::get('/payments', [HistoryController::class, 'paymentHistory'])->name('customer.history.payments');
        
        // API untuk get history JSON (jika diperlukan)
        Route::get('/json', [HistoryController::class, 'getHistoryJson'])->name('customer.history.json');
    });
    // ===========================================
});

// Route untuk callback dari Midtrans (tidak perlu auth)
Route::post('/payment/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback');

// Route khusus Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');
    Route::resource('raks', RakController::class);

    // Profile Admin Routes - DIUPDATE DENGAN ROUTE UPLOAD PHOTO
    Route::prefix('admin/profile')->group(function () {
        Route::get('/', [ProfileAdminController::class, 'index'])->name('admin.profile.index');
        Route::put('/', [ProfileAdminController::class, 'update'])->name('admin.profile.update');
        Route::put('/password', [ProfileAdminController::class, 'updatePassword'])->name('admin.profile.updatePassword');

        // Tambahkan route untuk foto profil - TAMBAHKAN INI
        Route::post('/upload-photo', [ProfileAdminController::class, 'uploadPhoto'])->name('admin.profile.upload-photo');
    });

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
    Route::delete('/profile/hapus-foto', [ProfileController::class, 'hapusFoto'])->name('customer.profile.hapus-foto');
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
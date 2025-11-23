<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman awal
Route::get('/', function () {
    return view('welcome');
});

// Route untuk Customer (default user)
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('customer.index');
    })->name('dashboard');
    
    // Route Profile Customer
    Route::get('/customer/profile', [ProfileController::class, 'index'])->name('customer.profile.index');
    Route::put('/customer/profile', [ProfileController::class, 'updateProfile'])->name('customer.profile.update');
    Route::post('/customer/profile/upload-foto', [ProfileController::class, 'uploadFoto'])->name('customer.profile.upload-foto');
});

// Route khusus Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
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
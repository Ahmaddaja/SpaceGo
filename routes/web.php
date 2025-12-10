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
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\TagihanController;

// ========================
// HALAMAN AWAL
// ========================
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-midtrans', function () {
    return config('midtrans');
});

// ========================
// ROUTE CUSTOMER
// ========================
Route::middleware(['auth', 'role:customer'])->group(function () {

    // Dashboard Customer
    Route::get('/customer', [CustomerController::class, 'dashboard'])->name('customer.index');

    Route::get('/dashboard', function () {
        return view('customer.index');
    })->name('dashboard');

    // Profile Customer
    Route::get('/customer/profile', [ProfileController::class, 'index'])->name('customer.profile.index');
    Route::put('/customer/profile', [ProfileController::class, 'updateProfile'])->name('customer.profile.update');
    Route::post('/customer/profile/upload-foto', [ProfileController::class, 'uploadFoto'])->name('customer.profile.upload-foto');

    // List Rak
    Route::get('/customer/rak', [CustomerController::class, 'listRak'])->name('customer.list-rak.list-rak');
    Route::get('/customer/rak/{id}', [CustomerController::class, 'showRak'])->name('customer.list-rak.show');

    // Rak Dibeli
    Route::get('/rak-dibeli', [RakController::class, 'rakDibeli'])->name('customer.list-rak.rak');
    Route::get('/rak-dibeli/{id}', [RakController::class, 'detailRak'])->name('customer.list-rak.detail');
    Route::get('/customer/list-rak/rak', [RakController::class, 'rakDibeli'])->name('customer.list-rak.rak');

    // ========================
    // PAYMENT ROUTES (digabung dari HEAD + second branch)
    // ========================

    // Payment dasar
    Route::get('/customer/bayar/{id}', [PaymentController::class, 'bayar'])->name('customer.bayar');
    Route::get('/payment/checkout/{id}', [PaymentController::class, 'bayar'])->name('customer.payment.checkout');

    // Update status (jangan diduplikasi)
    Route::post('/payment/update-status', [PaymentController::class, 'updateStatus'])
        ->name('payment.update-status');

    // Fitur checkout baru
    Route::post('/payment/process-checkout', [PaymentController::class, 'processPayment'])
        ->name('payment.process-checkout');

    Route::get('/payment/cancel-checkout', [PaymentController::class, 'cancelCheckout'])
        ->name('payment.cancel-checkout');

    // Handle return (Midtrans redirect)
    Route::post('/payment/handle-return', [PaymentController::class, 'handlePaymentReturn'])
        ->name('payment.handle-return');

    // ========================
    // PAYMENT RENEWAL ROUTES (DIPINDAHKAN KE LUAR TAGIHAN)
    // ========================
    Route::get('/payment/renewal-checkout/{transaction_id}', [PaymentController::class, 'renewal'])
        ->name('customer.payment.renewal-checkout');

    Route::post('/payment/process-renewal', [PaymentController::class, 'processRenewal'])
        ->name('customer.payment.process-renewal');

    // ========================
    // HISTORY ROUTES (DIUBAH: payments menjadi payment)
    // ========================
    Route::prefix('customer/history')->group(function () {
        Route::get('/', [HistoryController::class, 'index'])->name('customer.history');
        Route::get('/payment', [HistoryController::class, 'paymentHistory'])->name('customer.history.payment'); // DIUBAH: /payments menjadi /payment
        Route::get('/json', [HistoryController::class, 'getHistoryJson'])->name('customer.history.json');
    });

    // ========================
    // TAGIHAN ROUTES (FIXED)
    // ========================
    Route::prefix('customer/tagihan')->group(function () {
        // Index
        Route::get('/', [TagihanController::class, 'index'])->name('customer.tagihan');

        // Detail & Actions
        Route::get('/{id}/detail', [TagihanController::class, 'getDetail'])->name('customer.tagihan.detail');
        Route::post('/{id}/cancel', [TagihanController::class, 'cancelTagihan'])->name('customer.tagihan.cancel');
        Route::post('/{id}/regenerate-token', [TagihanController::class, 'regenerateToken'])->name('customer.tagihan.regenerate-token');

        // Payment & Status
        Route::get('/check-status/{id}', [TagihanController::class, 'checkStatus'])->name('customer.tagihan.check-status');
        Route::post('/process-expired/{id}', [TagihanController::class, 'processExpired'])->name('customer.tagihan.process-expired');
        Route::get('/payment-details/{id}', [TagihanController::class, 'paymentDetails'])->name('customer.tagihan.payment-details');

        // Utils
        Route::get('/check-overdue', [TagihanController::class, 'checkOverdue'])->name('customer.tagihan.check-overdue');
        Route::get('/debug-tokens', [TagihanController::class, 'debugMissingTokens'])->name('customer.tagihan.debug-tokens');
    });
});

// ========================
// PAYMENT CALLBACK (NO AUTH)
// ========================
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])->name('midtrans.callback');


// ========================
// ROUTE ADMIN
// ========================
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('raks', RakController::class);
    Route::resource('gudangs', GudangController::class);

    // Route untuk delete foto rak (AJAX)
    Route::delete('/raks/photos/{id}', [RakController::class, 'deletePhoto'])->name('raks.photos.delete');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.pelanggan.pelanggan');

    // Profile Admin
    Route::prefix('admin/profile')->group(function () {
        Route::get('/', [ProfileAdminController::class, 'index'])->name('admin.profile.index');
        Route::put('/', [ProfileAdminController::class, 'update'])->name('admin.profile.update');
        Route::put('/password', [ProfileAdminController::class, 'updatePassword'])->name('admin.profile.updatePassword');
        Route::post('/upload-photo', [ProfileAdminController::class, 'uploadPhoto'])->name('admin.profile.upload-photo');
    });

    // Transactions
    Route::prefix('admin/transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('admin.transactions.index');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('admin.transactions.show');
    });

    // Laporan
    Route::prefix('admin/laporan')->group(function () {
        Route::get('/pendapatan', [RevenueController::class, 'index'])->name('admin.laporan.pendapatan');
        Route::get('/pendapatan/detail', [RevenueController::class, 'detail'])->name('admin.laporan.detail');
        Route::get('/pendapatan/export-pdf', [RevenueController::class, 'exportPdf'])->name('admin.laporan.export.pdf');
        Route::get('/pendapatan/export-csv', [RevenueController::class, 'exportCsv'])->name('admin.laporan.export.csv');
        Route::get('/pendapatan/view-pdf', [RevenueController::class, 'viewPdf'])->name('admin.laporan.view.pdf');
        Route::get('/pendapatan/sync', [RevenueController::class, 'sync'])->name('admin.laporan.sync');
        Route::get('/performance', [RevenueController::class, 'performance'])->name('admin.laporan.performance');
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');

        Route::get('/check-updates', function () {
            $newUsersCount = \App\Models\User::where('created_at', '>=', now()->subMinutes(5))->count();
            $newTransactionsCount = \App\Models\Transaction::where('created_at', '>=', now()->subMinutes(5))->count();
            $newNotificationsCount = \App\Models\UserNotification::where('created_at', '>=', now()->subMinutes(5))->count();

            return response()->json([
                'has_new' => ($newUsersCount + $newTransactionsCount + $newNotificationsCount) > 0,
                'unread_count' => \App\Models\UserNotification::where('is_read', false)->count(),
                'new_users' => $newUsersCount,
                'new_transactions' => $newTransactionsCount
            ]);
        })->name('notifications.check-updates');

        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.delete');
        Route::delete('/category/{category}', [NotificationController::class, 'clearByCategory'])->name('notifications.clear-category');
    });

    // API Notifications
    Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])
        ->name('api.notifications');
});

// ========================
// PROFILE ROUTES
// ========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/hapus-foto', [ProfileController::class, 'hapusFoto'])->name('customer.profile.hapus-foto');
});

// ========================
// AUTHENTICATION
// ========================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

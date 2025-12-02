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

// ========================
// HALAMAN AWAL & TEST
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
    Route::get('/customer', [CustomerController::class, 'dashboard'])
        ->name('customer.index');

    Route::get('/dashboard', function () {
        return view('customer.index');
    })->name('dashboard');

    // Profile Customer
    Route::get('/customer/profile', [ProfileController::class, 'index'])
        ->name('customer.profile.index');
    Route::put('/customer/profile', [ProfileController::class, 'updateProfile'])
        ->name('customer.profile.update');
    Route::post('/customer/profile/upload-foto', [ProfileController::class, 'uploadFoto'])
        ->name('customer.profile.upload-foto');

    // List Rak
    Route::get('/customer/rak', [CustomerController::class, 'listRak'])
        ->name('customer.list-rak.list-rak');
    Route::get('/customer/rak/{id}', [CustomerController::class, 'showRak'])
        ->name('customer.list-rak.show');

    // Rak Dibeli
    Route::get('/rak-dibeli', [RakController::class, 'rakDibeli'])
        ->name('customer.list-rak.rak');
    Route::get('/rak-dibeli/{id}', [RakController::class, 'detailRak'])
        ->name('customer.list-rak.detail');
    Route::get('/customer/list-rak/rak', [RakController::class, 'rakDibeli'])
        ->name('customer.list-rak.rak');

    // Payment Routes
    Route::get('/customer/bayar/{id}', [PaymentController::class, 'bayar'])
        ->name('customer.bayar');
    Route::get('/payment/checkout/{id}', [PaymentController::class, 'bayar'])
        ->name('customer.payment.checkout');
    Route::post('/payment/update-status', [PaymentController::class, 'updateStatus'])
        ->name('payment.update-status');
    Route::get('/payment/renewal/{transaction_id}', [PaymentController::class, 'renewal'])
    ->name('customer.payment.renewal');

Route::post('/payment/update-status', [PaymentController::class, 'updateStatus'])
    ->name('payment.update-status');

    // History Routes
    Route::prefix('customer/history')->group(function () {
        Route::get('/', [HistoryController::class, 'index'])
            ->name('customer.history');
        Route::get('/payments', [HistoryController::class, 'paymentHistory'])
            ->name('customer.history.payments');
        Route::get('/json', [HistoryController::class, 'getHistoryJson'])
            ->name('customer.history.json');
    });
});

// ========================
// PAYMENT CALLBACK (NO AUTH)
// ========================
Route::post('/payment/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback');
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])
    ->name('midtrans.callback');

// ========================
// ROUTE ADMIN
// ========================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard Admin
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Rak Management
    Route::resource('raks', RakController::class);

    // Gudang Management
    Route::resource('gudangs', GudangController::class);

    // Customers/Pelanggan
    Route::get('/customers', [CustomerController::class, 'index'])
        ->name('admin.pelanggan.pelanggan');

    // Profile Admin
    Route::prefix('admin/profile')->group(function () {
        Route::get('/', [ProfileAdminController::class, 'index'])
            ->name('admin.profile.index');
        Route::put('/', [ProfileAdminController::class, 'update'])
            ->name('admin.profile.update');
        Route::put('/password', [ProfileAdminController::class, 'updatePassword'])
            ->name('admin.profile.updatePassword');
        Route::post('/upload-photo', [ProfileAdminController::class, 'uploadPhoto'])
            ->name('admin.profile.upload-photo');
    });

    // Transaction Management
    Route::prefix('admin/transactions')->group(function () {
        // Transaction Management (READ ONLY)
        Route::prefix('admin/transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])
                ->name('admin.transactions.index');
            Route::get('/{id}', [TransactionController::class, 'show'])
                ->name('admin.transactions.show');
        });

        // Laporan Routes
        Route::prefix('admin/laporan')->group(function () {
            Route::get('/pendapatan', [RevenueController::class, 'index'])
                ->name('admin.laporan.pendapatan');

            Route::get('/pendapatan/detail', [RevenueController::class, 'detail'])
                ->name('admin.laporan.detail');

            Route::get('/pendapatan/export-pdf', [RevenueController::class, 'exportPdf'])
                ->name('admin.laporan.export.pdf');

            Route::get('/pendapatan/sync', [RevenueController::class, 'sync'])
                ->name('admin.laporan.sync');
        });
    });

    // Notification Management
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])
            ->name('notifications.index');
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

        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])
            ->name('notifications.read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('notifications.read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])
            ->name('notifications.delete');
        Route::delete('/category/{category}', [NotificationController::class, 'clearByCategory'])
            ->name('notifications.clear-category');
    });

    // Transaction Check API
    Route::get('/transactions/check-new', function () {
        $newTransactionsCount = \App\Models\Transaction::where('created_at', '>=', now()->subMinutes(5))->count();
        $latestTransaction = \App\Models\Transaction::with('user')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->latest()
            ->first();

        return response()->json([
            'new_transactions_count' => $newTransactionsCount,
            'latest_transaction' => $latestTransaction ? [
                'order_id' => $latestTransaction->order_id,
                'amount' => $latestTransaction->amount,
                'user_name' => $latestTransaction->user->name ?? 'Unknown'
            ] : null
        ]);
    })->name('transactions.check-new');

    // API Notifications
    Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])
        ->name('api.notifications');
});

// ========================
// PROFILE ROUTES (ALL AUTHENTICATED USERS)
// ========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    Route::delete('/profile/hapus-foto', [ProfileController::class, 'hapusFoto'])
        ->name('customer.profile.hapus-foto');
});

// ========================
// AUTHENTICATION ROUTES
// ========================

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Register
Route::get('/register', [AuthController::class, 'showRegistrationForm'])
    ->name('register');
Route::post('/register', [AuthController::class, 'register'])
    ->name('register.post');

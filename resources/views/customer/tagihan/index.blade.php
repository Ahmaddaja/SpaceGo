@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 pt-20"> <!-- Tambahkan pt-20 untuk offset navbar -->
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tagihan & Perpanjangan</h1>
                <p class="text-gray-600 mt-2">Kelola pembayaran dan perpanjangan sewa rak Anda</p>
            </div>
            <a href="{{ route('customer.tagihan.check-overdue') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-sync-alt mr-2"></i>Refresh Status
            </a>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Menunggu Pembayaran</p>
                        <p class="text-2xl font-bold">{{ $pendingTransactions->count() }}</p>
                    </div>
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Kadaluarsa</p>
                        <p class="text-2xl font-bold">{{ $expiredTransactions->count() }}</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Perlu Perpanjangan</p>
                        <p class="text-2xl font-bold">{{ $overdueTransactions->count() }}</p>
                    </div>
                    <i class="fas fa-calendar-times text-orange-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Menunggu Pembayaran -->
    @if($pendingTransactions->count() > 0)
    <div class="mb-10">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clock text-yellow-500 mr-2"></i>
            Menunggu Pembayaran
        </h2>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batas Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pendingTransactions as $transaction)
                        @php
                            $isRenewal = $transaction->is_renewal ?? false;
                            $batasWaktu = $transaction->created_at->addHours(24);
                            $isExpired = now()->gt($batasWaktu);
                        @endphp
                        
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $transaction->rak->nama_rak ?? 'Rak' }}</div>
                                <div class="text-sm text-gray-500">Order: {{ $transaction->order_id }}</div>
                                @if($isRenewal)
                                <div class="text-xs text-purple-600 font-medium mt-1">
                                    <i class="fas fa-redo mr-1"></i> Perpanjangan
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Pembayaran
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($isExpired)
                                <span class="text-red-600 text-sm font-medium">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Telah Kadaluarsa
                                </span>
                                @else
                                <!-- Tombol untuk melanjutkan pembayaran ke Midtrans -->
                                <button onclick="continuePayment('{{ $transaction->snap_token }}', {{ $transaction->id }})" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Bayar Sekarang
                                </button>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($isExpired)
                                <span class="text-red-600 text-sm">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Telah lewat
                                </span>
                                @else
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $batasWaktu->format('d M Y H:i') }}
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Perlu Perpanjangan -->
    @if($overdueTransactions->count() > 0)
    <div class="mb-10">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-calendar-times text-orange-500 mr-2"></i>
            Rak Perlu Perpanjangan
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($overdueTransactions as $transaction)
            @php
                $sewaBerahir = \Carbon\Carbon::parse($transaction->sewa_berakhir);
                $now = now();
                $isOverdue = $now->gt($sewaBerahir);
                $totalHours = abs(floor($now->diffInHours($sewaBerahir)));
                
                if ($totalHours < 24) {
                    if ($isOverdue) {
                        $overdueText = $totalHours . ' jam terlambat';
                        $isCritical = false;
                        $statusColor = 'orange';
                    } else {
                        $overdueText = $totalHours . ' jam tersisa';
                        $isCritical = false;
                        $statusColor = 'blue';
                    }
                } else {
                    $totalDays = floor($totalHours / 24);
                    $remainingHours = $totalHours % 24;
                    
                    if ($remainingHours > 0) {
                        $overdueText = $totalDays . ' hari ' . $remainingHours . ' jam ' . ($isOverdue ? 'terlambat' : 'tersisa');
                    } else {
                        $overdueText = $totalDays . ' hari ' . ($isOverdue ? 'terlambat' : 'tersisa');
                    }
                    
                    $isCritical = $isOverdue && $totalDays > 7;
                    $statusColor = $isOverdue ? ($isCritical ? 'red' : 'orange') : 'blue';
                }
            @endphp
            
            <div class="bg-white rounded-lg shadow p-6 border {{ $isCritical ? 'border-red-200' : 'border-orange-200' }}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $transaction->rak->nama_rak ?? 'Rak' }}</h3>
                        <p class="text-sm text-gray-500">Kode: {{ $transaction->order_id }}</p>
                    </div>
                    <span class="px-3 py-1 {{ $isCritical ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }} text-xs font-medium rounded-full">
                        {{ $overdueText }}
                    </span>
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-sm">Masa Sewa Berakhir:</span>
                        <span class="font-medium">{{ $sewaBerahir->format('d M Y') }}</span>
                    </div>
                    @if($transaction->rak)
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-sm">Biaya Perpanjangan:</span>
                        <span class="font-bold text-blue-600">
                            Rp {{ number_format($transaction->rak->harga_sewa_perbulan, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    
                    @if($isCritical)
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center text-red-700">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span class="text-sm font-medium">Segera perpanjang untuk menghindari sanksi</span>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="space-y-3">
                    <!-- Tombol Perpanjang -->
                    <a href="{{ route('customer.payment.renewal-checkout', $transaction->id) }}"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>
                        Buat Permintaan Perpanjangan
                    </a>
                    
                    <!-- Tombol Lepas -->
                    <form action="{{ route('customer.tagihan.process-expired', $transaction->id) }}" method="POST" id="form-lepas-{{ $transaction->id }}">
                        @csrf
                        <button type="button" 
                                onclick="confirmLepasRak({{ $transaction->id }})"
                                class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 rounded-lg transition">
                            <i class="fas fa-times mr-2"></i>
                            Lepas Rak
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="space-y-3">
        <!-- Tombol Perpanjang -->
        <a href="{{ route('customer.payment.renewal-checkout', $transaction->id) }}"
   class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition flex items-center justify-center">
    <i class="fas fa-redo mr-2"></i>
    Buat Permintaan Perpanjangan
</a>

        
        <!-- Tombol Lepas -->
        <form action="{{ route('customer.tagihan.process-expired', $transaction->id) }}" method="POST">
            @csrf
            <button type="submit" 
                    onclick="return confirm('Apakah Anda yakin ingin membatalkan pembayaran ini? Status akan berubah menjadi kadaluarsa.')"
                    class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </button>
        </form>
    </div>
</div>
@endforeach
        </div>
    </div>
    @endif

    <!-- Kadaluarsa (Read-only) -->
    @if($expiredTransactions->count() > 0)
    <div class="mb-10">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            Riwayat Kadaluarsa
        </h2>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($expiredTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $transaction->rak->nama_rak ?? 'Rak' }}</div>
                                <div class="text-sm text-gray-500">{{ $transaction->order_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Kadaluarsa
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($transaction->is_renewal)
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-redo mr-1"></i> Pembayaran perpanjangan
                                </span>
                                @else
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-clock mr-1"></i> Pembayaran awal
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">
                                    {{ $transaction->created_at->format('d M Y') }}
                                </div>
                                @if($transaction->sewa_berakhir)
                                <div class="text-xs text-gray-500">
                                    Berakhir: {{ \Carbon\Carbon::parse($transaction->sewa_berakhir)->format('d M Y') }}
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Empty State -->
    @if($pendingTransactions->count() == 0 && $expiredTransactions->count() == 0 && $overdueTransactions->count() == 0)
    <div class="text-center py-16">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
            <i class="fas fa-check-circle text-green-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Tagihan</h3>
        <p class="text-gray-500 mb-8">Semua pembayaran Anda sudah berhasil diproses.</p>
        <a href="{{ route('customer.list-rak.list-rak') }}" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-pallet mr-2"></i>
            Sewa Rak Baru
        </a>
    </div>
    @endif
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-8 rounded-2xl shadow-2xl text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-4"></div>
        <p class="text-lg font-semibold text-gray-800">Memproses...</p>
        <p class="text-gray-600 mt-2">Mohon tunggu sebentar</p>
    </div>
</div>

<!-- Alert Container -->
<div id="alert-container" class="z-[9999]"></div>

<!-- Midtrans Snap Script -->
@if($pendingTransactions->count() > 0)
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="{{ config('midtrans.client_key') }}"
            async defer></script>
@endif

@push('styles')
<style>
    /* Fix untuk navbar */
    nav.navbar {
        z-index: 1000 !important;
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
    }

    /* Loading Overlay */
    #loading-overlay {
        transition: opacity 0.3s ease;
        z-index: 9998;
    }

    #loading-overlay.hidden {
        display: none;
    }

    /* Alert Modal Styles - Perbaikan z-index */
    .alert-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
        padding-top: 60px; /* Offset untuk navbar */
    }
    
    .alert-modal.show {
        opacity: 1;
        visibility: visible;
    }
    
    .alert-modal-content {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 450px;
        overflow: hidden;
        transform: translateY(20px);
        transition: transform 0.4s ease;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .alert-modal.show .alert-modal-content {
        transform: translateY(0);
    }
    
    .alert-modal-header {
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .alert-modal-success .alert-modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .alert-modal-error .alert-modal-header {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .alert-modal-warning .alert-modal-header {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }
    
    .alert-modal-info .alert-modal-header {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    
    .alert-modal-icon {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }
    
    .alert-modal-title {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .alert-modal-body {
        padding: 1.5rem;
        text-align: center;
    }
    
    .alert-modal-message {
        font-size: 1rem;
        color: #4b5563;
        line-height: 1.5;
        margin-bottom: 1rem;
    }
    
    .alert-countdown {
        background: #f3f4f6;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 1rem;
    }
    
    .countdown-number {
        font-weight: bold;
        color: #3b82f6;
    }
    
    .alert-modal-footer {
        padding: 1.5rem;
        display: flex;
        gap: 0.75rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    
    .alert-modal-button {
        flex: 1;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .alert-modal-button-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    
    .alert-modal-button-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        transform: translateY(-1px);
    }
    
    .alert-modal-button-secondary {
        background: white;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }
    
    .alert-modal-button-secondary:hover {
        background: #f3f4f6;
        color: #374151;
    }

    /* Fix for Midtrans iframe overlay */
    .snap-midtrans-overlay {
        z-index: 9997 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // ===== ALERT FUNCTIONS =====
    function showAlert(message, type = 'info', autoRedirect = false, redirectUrl = null) {
        const alertTypes = {
            success: { 
                icon: 'fas fa-check-circle', 
                class: 'alert-modal-success', 
                title: 'Sukses!', 
                buttonText: 'Lanjutkan' 
            },
            error: { 
                icon: 'fas fa-exclamation-triangle', 
                class: 'alert-modal-error', 
                title: 'Error!', 
                buttonText: 'Coba Lagi' 
            },
            warning: { 
                icon: 'fas fa-bolt', 
                class: 'alert-modal-warning', 
                title: 'Peringatan!', 
                buttonText: 'Mengerti' 
            },
            info: { 
                icon: 'fas fa-info-circle', 
                class: 'alert-modal-info', 
                title: 'Informasi', 
                buttonText: 'OK' 
            }
        };

        const alertConfig = alertTypes[type] || alertTypes.info;
        const alertId = 'alert-' + Date.now();
        
        const alertHTML = `
            <div id="${alertId}" class="alert-modal ${alertConfig.class}">
                <div class="alert-modal-content">
                    <div class="alert-modal-header">
                        <div class="alert-modal-icon">
                            <i class="${alertConfig.icon}"></i>
                        </div>
                        <div class="alert-modal-title">${alertConfig.title}</div>
                    </div>
                    <div class="alert-modal-body">
                        <div class="alert-modal-message">${message}</div>
                        ${autoRedirect ? `
                            <div class="alert-countdown">
                                <i class="fas fa-clock"></i>
                                Akan dialihkan dalam 
                                <span class="countdown-number" id="countdown-${alertId}">5</span> 
                                detik
                            </div>
                        ` : ''}
                    </div>
                    <div class="alert-modal-footer">
                        <button onclick="closeAlert('${alertId}')" class="alert-modal-button alert-modal-button-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Tutup
                        </button>
                        <button onclick="handleAlertAction('${alertId}', ${autoRedirect}, '${redirectUrl}')" 
                                class="alert-modal-button alert-modal-button-primary">
                            <i class="fas fa-check mr-2"></i>
                            ${alertConfig.buttonText}
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        const alertContainer = document.getElementById('alert-container');
        alertContainer.insertAdjacentHTML('beforeend', alertHTML);
        
        const alertElement = document.getElementById(alertId);
        
        setTimeout(() => {
            alertElement.classList.add('show');
        }, 100);

        if (autoRedirect) {
            startCountdown(alertId, redirectUrl);
        }

        return alertId;
    }

    function startCountdown(alertId, redirectUrl) {
        let countdown = 5;
        const countdownElement = document.getElementById(`countdown-${alertId}`);
        const countdownInterval = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                handleAlertAction(alertId, true, redirectUrl);
            }
        }, 1000);

        const alertElement = document.getElementById(alertId);
        alertElement.dataset.countdownInterval = countdownInterval;
    }

    function handleAlertAction(alertId, autoRedirect = false, redirectUrl = null) {
        closeAlert(alertId);
        
        if (autoRedirect && redirectUrl) {
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 300);
        }
    }

    function closeAlert(alertId) {
        const alertElement = document.getElementById(alertId);
        if (!alertElement) return;

        if (alertElement.dataset.countdownInterval) {
            clearInterval(alertElement.dataset.countdownInterval);
        }

        alertElement.classList.remove('show');
        
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.parentNode.removeChild(alertElement);
            }
        }, 400);
    }

    // ===== PAYMENT FUNCTIONS =====
    let isPaymentProcessing = false;

    function continuePayment(snapToken, transactionId) {
        if (isPaymentProcessing) {
            showAlert('Sedang memproses pembayaran sebelumnya. Tunggu sebentar.', 'warning');
            return;
        }

        if (!snapToken) {
            showAlert('Token pembayaran tidak tersedia.', 'error');
            return;
        }

        isPaymentProcessing = true;
        const loadingOverlay = document.getElementById('loading-overlay');
        loadingOverlay.classList.remove('hidden');

        // Tunggu Snap.js siap jika belum terload
        if (typeof snap === 'undefined') {
            showAlert('Sistem pembayaran sedang loading. Coba beberapa saat lagi.', 'warning');
            loadingOverlay.classList.add('hidden');
            isPaymentProcessing = false;
            return;
        }

        console.log('Opening Midtrans payment with token:', snapToken);

        // Buka popup Midtrans
        snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('Payment Success:', result);
                loadingOverlay.classList.add('hidden');
                updatePaymentStatus(result, transactionId);
            },
            onPending: function(result) {
                console.log('Payment Pending:', result);
                loadingOverlay.classList.add('hidden');
                showAlert(
                    'Pembayaran Anda dalam proses pending. Mohon selesaikan pembayaran Anda dalam waktu 24 jam.', 
                    'warning',
                    true,
                    "{{ route('customer.tagihan') }}"
                );
                isPaymentProcessing = false;
            },
            onError: function(result) {
                console.error('Payment Error:', result);
                loadingOverlay.classList.add('hidden');
                showAlert(
                    'Pembayaran gagal! Silakan coba lagi atau hubungi customer service jika masalah berlanjut.', 
                    'error'
                );
                isPaymentProcessing = false;
            },
            onClose: function() {
                console.log('Payment popup closed by user');
                loadingOverlay.classList.add('hidden');
                showAlert(
                    'Anda menutup popup pembayaran tanpa menyelesaikan transaksi. Silakan klik "Bayar Sekarang" lagi untuk melanjutkan.', 
                    'info'
                );
                isPaymentProcessing = false;
            }
        });
    }

    function updatePaymentStatus(result, transactionId = null) {
        const loadingOverlay = document.getElementById('loading-overlay');
        loadingOverlay.classList.remove('hidden');

        fetch("{{ route('payment.update-status') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                order_id: result.order_id,
                transaction_status: result.transaction_status,
                payment_type: result.payment_type || null,
                transaction_id: transactionId
            })
        })
        .then(async response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            }
            return { success: false, message: 'Invalid response from server' };
        })
        .then(data => {
            loadingOverlay.classList.add('hidden');
            console.log('Update status response:', data);
            
            if (data.success) {
                showAlert(
                    'Pembayaran berhasil! Terima kasih telah melakukan pembayaran. Anda akan dialihkan ke halaman rak.', 
                    'success',
                    true,
                    "{{ route('customer.list-rak.rak') }}"
                );
            } else {
                showAlert(
                    'Pembayaran berhasil tapi gagal update status. Silakan hubungi admin untuk konfirmasi.', 
                    'warning',
                    false,
                    null
                );
                console.error('Update status failed:', data.message);
            }
            isPaymentProcessing = false;
        })
        .catch(error => {
            loadingOverlay.classList.add('hidden');
            console.error('Error updating status:', error);
            showAlert(
                'Pembayaran berhasil! Terima kasih telah melakukan pembayaran.', 
                'success',
                true,
                "{{ route('customer.list-rak.rak') }}"
            );
            isPaymentProcessing = false;
        });
    }

// ===== FUNCTION UNTUK KONFIRMASI LEPAS RAK =====
function confirmLepasRak(transactionId) {
    const formId = 'form-lepas-' + transactionId;
    
    // Gunakan showAlert() biasa, tapi custom handler-nya
    const alertId = showAlert(
        'Apakah Anda yakin ingin melepas rak ini? Status akan berubah menjadi kadaluarsa.', 
        'warning',
        false,
        null
    );
    
    // Custom handler untuk alert ini
    const alertElement = document.getElementById(alertId);
    const primaryButton = alertElement.querySelector('.alert-modal-button-primary');
    const secondaryButton = alertElement.querySelector('.alert-modal-button-secondary');
    
    if (primaryButton) {
        // Ganti dengan handler baru untuk submit
        primaryButton.onclick = function() {
            closeAlert(alertId);
            submitLepasRak(formId);
        };
        
        // Ubah teks tombol
        primaryButton.innerHTML = '<i class="fas fa-check mr-2"></i>Ya, Lepas Rak';
    }
    
    if (secondaryButton) {
        // Ubah teks tombol secondary
        secondaryButton.innerHTML = '<i class="fas fa-times mr-2"></i>Batal';
    }
    
    // Hapus countdown jika ada
    const countdownElement = alertElement.querySelector('.alert-countdown');
    if (countdownElement) {
        countdownElement.remove();
    }
    
    return false;
}

// Function untuk submit form lepas rak
function submitLepasRak(formId, retryCount = 0) {
    const form = document.getElementById(formId);
    if (!form) {
        showAlert(
            'Form tidak ditemukan. Silakan refresh halaman.', 
            'error'
        );
        return;
    }
    
    // Tampilkan loading
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.classList.remove('hidden');
    }
    
    // Kirim form
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(Object.fromEntries(new FormData(form)))
    })
    .then(async response => {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return response.json();
        }
        throw new Error('Response bukan JSON');
    })
    .then(data => {
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
        
        if (data.success) {
            showAlert(
                'Rak berhasil dilepas dan status diubah menjadi kadaluarsa.', 
                'success',
                true,
                "{{ route('customer.tagihan') }}"
            );
        } else {
            // Jika gagal, tampilkan alert dengan tombol "Coba Lagi"
            const retryAlertId = showAlert(
                'Gagal melepas rak: ' + (data.message || 'Terjadi kesalahan'), 
                'error',
                false,
                null
            );
            
            // Custom handler untuk retry
            const retryAlertElement = document.getElementById(retryAlertId);
            const retryButton = retryAlertElement.querySelector('.alert-modal-button-primary');
            const closeButton = retryAlertElement.querySelector('.alert-modal-button-secondary');
            
            if (retryButton) {
                // Ubah teks dan handler untuk tombol "Coba Lagi"
                retryButton.innerHTML = '<i class="fas fa-redo mr-2"></i>Coba Lagi';
                retryButton.onclick = function() {
                    closeAlert(retryAlertId);
                    // Coba lagi dengan increment retry count
                    submitLepasRak(formId, retryCount + 1);
                };
            }
            
            if (closeButton) {
                closeButton.innerHTML = '<i class="fas fa-times mr-2"></i>Tutup';
            }
        }
    })
    .catch(error => {
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
        
        console.error('Error:', error);
        
        // Jika sudah mencoba 3 kali, beri pesan berbeda
        if (retryCount >= 3) {
            showAlert(
                'Sudah 3 kali mencoba tetapi masih gagal. Silakan hubungi customer service.', 
                'error'
            );
            return;
        }
        
        // Tampilkan alert error dengan tombol "Coba Lagi"
        const errorAlertId = showAlert(
            'Terjadi kesalahan: ' + error.message + '. Silakan coba lagi.', 
            'error',
            false,
            null
        );
        
        // Custom handler untuk retry
        const errorAlertElement = document.getElementById(errorAlertId);
        const retryButton = errorAlertElement.querySelector('.alert-modal-button-primary');
        const closeButton = errorAlertElement.querySelector('.alert-modal-button-secondary');
        
        if (retryButton) {
            // Ubah teks dan handler untuk tombol "Coba Lagi"
            retryButton.innerHTML = '<i class="fas fa-redo mr-2"></i>Coba Lagi';
            retryButton.onclick = function() {
                closeAlert(errorAlertId);
                // Coba lagi dengan increment retry count
                submitLepasRak(formId, retryCount + 1);
            };
        }
        
        if (closeButton) {
            closeButton.innerHTML = '<i class="fas fa-times mr-2"></i>Tutup';
        }
    });
}

// Function khusus untuk retry action (bisa digunakan umum)
function setupRetryButton(alertId, retryFunction, retryParams = {}) {
    const alertElement = document.getElementById(alertId);
    const retryButton = alertElement.querySelector('.alert-modal-button-primary');
    const closeButton = alertElement.querySelector('.alert-modal-button-secondary');
    
    if (retryButton) {
        retryButton.innerHTML = '<i class="fas fa-redo mr-2"></i>Coba Lagi';
        retryButton.onclick = function() {
            closeAlert(alertId);
            retryFunction(retryParams);
        };
    }
    
    if (closeButton) {
        closeButton.innerHTML = '<i class="fas fa-times mr-2"></i>Tutup';
    }
}

    // ===== EVENT LISTENERS =====
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('alert-modal')) {
            closeAlert(e.target.id);
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.alert-modal.show');
            if (openModal) {
                closeAlert(openModal.id);
            }
        }
    });

    // ===== AUTO CLOSE SESSION MESSAGES =====
    @if(session('success'))
        showAlert('{{ session('success') }}', 'success', true, "{{ route('customer.list-rak.rak') }}");
    @endif
    
    @if(session('error'))
        showAlert('{{ session('error') }}', 'error', false, null);
    @endif
    
    @if(session('warning'))
        showAlert('{{ session('warning') }}', 'warning', false, null);
    @endif
    
    @if(session('info'))
        showAlert('{{ session('info') }}', 'info', false, null);
    @endif
</script>
@endpush
@endsection
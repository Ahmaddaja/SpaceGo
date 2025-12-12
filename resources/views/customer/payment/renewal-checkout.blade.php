    @extends('layouts.app')

    @section('title', 'Perpanjangan Sewa - SPACEGO')

    @push('styles')
    <style>
        .checkout-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .payment-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .payment-card:hover {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .header-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.2s ease;
        }
        
        .detail-row:hover {
            background: #f9fafb;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .total-row {
            background: #fef3c7;
            border-radius: 0.75rem;
            padding: 1.5rem !important;
            margin-top: 1rem;
            border: 2px solid #fbbf24;
        }
        
        .penalty-badge {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }

        .grace-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .info-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin: 1.5rem 0;
        }
        
        .pay-button {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1.125rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .pay-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }
        
        .back-button {
            background: #e5e7eb;
            color: #374151;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        
        .back-button:hover {
            background: #d1d5db;
            transform: translateY(-2px);
        }
    </style>
    @endpush

    @section('content')
    <div class="py-12">
        <div class="checkout-container px-4">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Perpanjangan Sewa</h1>
                <p class="text-gray-600">Lanjutkan masa sewa rak Anda</p>
            </div>

    @php
        // ✅ FIX: Ambil sewa_berakhir dari tagihan atau transaction
        $sewaBerahir = isset($tagihan->sewa_berakhir) 
            ? $tagihan->sewa_berakhir 
            : $transaction->sewa_berakhir;

        // Hitung selisih hari secara akurat (integer)
        $daysDiff = isset($daysDiff) 
            ? $daysDiff 
            : (int) (
                \Carbon\Carbon::parse($sewaBerahir)
                    ->startOfDay()
                    ->diffInDays(now()->startOfDay(), false)
            );

        // LOGIKA MASA TENGGANG DAN DENDA
        $gracePeriodDays = $gracePeriodDays ?? 3; 
        $dendaPerHari = 50000;

        $isOverdue = false;
        $isInGracePeriod = false;
        $latenessDays = 0;
        $calculatedDenda = 0;

        // Jika daysDiff negatif, berarti sudah lewat tanggal berakhir
        if ($daysDiff < 0) {
            $daysLate = abs($daysDiff);

            if ($daysLate <= $gracePeriodDays) {
                // Masih dalam masa tenggang, tidak ada denda
                $isInGracePeriod = true;
                $latenessDays = 0;
                $calculatedDenda = 0;
            } else {
                // Sudah melewati masa tenggang, ada denda
                $isOverdue = true;
                $latenessDays = $daysLate - $gracePeriodDays;
                $calculatedDenda = $latenessDays * $dendaPerHari;
            }
        }

        // Total bayar
        $totalPembayaran = $hargaSewa + $calculatedDenda;

        // ✅ FIX: Format waktu yang lebih user-friendly
        $timeBeforeExpiry = '';
        if ($daysDiff > 0) {
            // Masih ada waktu sebelum berakhir
            if ($daysDiff >= 1) {
                $timeBeforeExpiry = abs($daysDiff) . ' hari';
            } else {
                // Jika kurang dari 1 hari, hitung dalam jam
                $hoursDiff = \Carbon\Carbon::parse($sewaBerahir)->diffInHours(now());
                if ($hoursDiff >= 1) {
                    $timeBeforeExpiry = abs($hoursDiff) . ' jam';
                } else {
                    $timeBeforeExpiry = 'beberapa saat';
                }
            }
        }
    @endphp

            <!-- Payment Card -->
            <div class="payment-card mb-6">
                
                <!-- Header Section -->
                <div class="header-gradient">
                    <div class="relative z-10">
                        <h2 class="text-2xl font-bold mb-2">{{ $rak->nama_rak }}</h2>
                        <p class="opacity-90">{{ $rak->kode_rak }}</p>
                        
                        @if($isOverdue)
                        <div class="mt-4">
                            <span class="penalty-badge">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Terdapat Denda Keterlambatan ({{ $latenessDays }} Hari)
                            </span>
                        </div>
                        @elseif($isInGracePeriod)
                        <div class="mt-4">
                            <span class="grace-badge">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Masa Tenggang Hari Ke-{{ abs($daysDiff) }} (Tanpa Denda)
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Detail Section -->
                <div class="p-8">
                    
                    <!-- Informasi Sewa Sebelumnya -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Sewa Sebelumnya</h3>
                        
                        <div class="detail-row">
                            <span class="text-gray-600">Tanggal Mulai</span>
                            <span class="font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($transaction->sewa_mulai)->format('d M Y') }}
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="text-gray-600">Tanggal Berakhir</span>
                            <span class="font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($sewaBerahir)->format('d M Y') }}
                            </span>
                        </div>
                        
                        @if($daysDiff < 0)
                        <div class="detail-row">
                            <span class="@if($isOverdue) text-red-600 @else text-yellow-600 @endif font-semibold">
                                Status
                            </span>
                            <span class="font-bold @if($isOverdue) text-red-600 @else text-yellow-600 @endif">
                                @if($isInGracePeriod)
                                    Masa Tenggang Hari Ke-{{ abs($daysDiff) }} dari {{ $gracePeriodDays }}
                                @else
                                    Terlambat {{ $latenessDays }} Hari (Setelah Masa Tenggang)
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Warning/Info Box -->
                    @if($isOverdue)
                    <div class="info-box" style="background: #fee2e2; border-left-color: #ef4444;">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">⚠️ Denda Keterlambatan Dikenakan!</p>
                                <p class="text-sm text-gray-700">
                                    Masa sewa Anda berakhir <strong>{{ abs($daysDiff) }} hari</strong> yang lalu.
                                    Anda telah melewati masa tenggang {{ $gracePeriodDays }} hari.
                                    <br><br>
                                    <strong>Keterlambatan: {{ $latenessDays }} hari setelah masa tenggang</strong><br>
                                    Denda: Rp {{ number_format($dendaPerHari, 0, ',', '.') }}/hari × {{ $latenessDays }} hari = <strong class="text-red-700">Rp {{ number_format($calculatedDenda, 0, ',', '.') }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    @elseif($isInGracePeriod)
                    <div class="info-box" style="background: #fef3c7; border-left-color: #f59e0b;">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-orange-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">🛡️ Anda Dalam Masa Tenggang</p>
                                <p class="text-sm text-gray-700">
                                    Masa sewa berakhir <strong>{{ abs($daysDiff) }} hari</strong> yang lalu, tetapi Anda masih dalam masa tenggang <strong>{{ $gracePeriodDays }} hari</strong>.
                                    <br><br>
                                    ✅ <strong>Tidak ada denda yang dikenakan</strong> selama masa tenggang.<br>
                                    ⏰ Sisa waktu masa tenggang: <strong>{{ $gracePeriodDays - abs($daysDiff) }} hari lagi</strong><br>
                                    💡 Perpanjang sekarang untuk menghindari denda Rp {{ number_format($dendaPerHari, 0, ',', '.') }}/hari setelah masa tenggang berakhir.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
    <div class="info-box" style="background: #d1fae5; border-left-color: #10b981;">
        <div class="flex items-start">
            <i class="fas fa-check-circle text-green-600 text-xl mr-3 mt-1"></i>
            <div>
                <p class="font-semibold text-gray-800 mb-1">✅ Perpanjangan Tepat Waktu</p>
                <p class="text-sm text-gray-700">
                    @if($daysDiff == 0)
                        Anda melakukan perpanjangan pada hari terakhir masa sewa.
                    @elseif($daysDiff > 0)
                        Anda melakukan perpanjangan <strong>{{ $timeBeforeExpiry }} sebelum</strong> masa sewa berakhir.
                    @endif
                    <br><br>
                    ✅ <strong>Tidak ada denda</strong> yang dikenakan.
                </p>
            </div>
        </div>
    </div>
    @endif

                    <!-- Rincian Pembayaran -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Rincian Pembayaran</h3>
                        
                        <div class="detail-row">
                            <span class="text-gray-600">
                                Harga Sewa Perpanjangan ({{ $rak->durasi_sewa_hari ?? 30 }} hari)
                            </span>
                            <span class="font-semibold text-gray-800">
                                Rp {{ number_format($hargaSewa, 0, ',', '.') }}
                            </span>
                        </div>
                        
                        @if($isOverdue && $calculatedDenda > 0)
                        <div class="detail-row">
                            <span class="text-red-600 font-semibold">
                                Denda Keterlambatan
                                <div class="text-xs text-gray-600 font-normal mt-1">
                                    {{ $latenessDays }} hari × Rp {{ number_format($dendaPerHari, 0, ',', '.') }}/hari<br>
                                    <em>(Setelah {{ $gracePeriodDays }} hari masa tenggang)</em>
                                </div>
                            </span>
                            <span class="font-bold text-red-600 text-lg">
                                Rp {{ number_format($calculatedDenda, 0, ',', '.') }}
                            </span>
                        </div>
                        @elseif($isInGracePeriod || $daysDiff >= 0)
                        <div class="detail-row">
                            <span class="text-green-600 font-semibold">
                                Denda Keterlambatan
                                <div class="text-xs text-gray-600 font-normal mt-1">
                                    @if($isInGracePeriod)
                                        <em>Dalam masa tenggang (hari ke-{{ abs($daysDiff) }})</em>
                                    @else
                                        <em>Tidak ada keterlambatan</em>
                                    @endif
                                </div>
                            </span>
                            <span class="font-bold text-green-600">
                                Rp 0
                            </span>
                        </div>
                        @endif
                        
                        <div class="detail-row total-row">
                            <span class="text-lg font-bold text-gray-800">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-orange-600">
                                Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Informasi Periode Baru -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-blue-900 mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Periode Sewa Baru
                        </h4>
                        <p class="text-sm text-blue-800">
                            Setelah pembayaran berhasil, masa sewa Anda akan diperpanjang selama 
                            <strong>{{ $rak->durasi_sewa_hari ?? 30 }} hari</strong> mulai dari 
                            <strong>{{ now()->format('d M Y') }}</strong> hingga 
                            <strong>{{ now()->addDays($rak->durasi_sewa_hari ?? 30)->format('d M Y') }}</strong>.
                        </p>
                    </div>

                    @if($isInGracePeriod)
                    <!-- Info Tambahan untuk Masa Tenggang -->
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <h4 class="font-semibold text-yellow-900 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Informasi Masa Tenggang
                        </h4>
                        <p class="text-sm text-yellow-800">
                            Masa tenggang adalah periode {{ $gracePeriodDays }} hari setelah masa sewa berakhir dimana Anda tidak dikenakan denda.
                            Saat ini Anda berada di hari ke-{{ abs($daysDiff) }} dari {{ $gracePeriodDays }} hari masa tenggang.
                            Jika melewati masa tenggang, denda Rp {{ number_format($dendaPerHari, 0, ',', '.') }}/hari akan mulai dikenakan.
                        </p>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('customer.tagihan') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali ke Tagihan</span>
                        </a>
                        
                        <button id="pay-button" class="pay-button">
                            <i class="fas fa-credit-card"></i>
                            <span>Bayar Sekarang</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Security Notice -->
            <div class="text-center text-sm text-gray-500">
                <i class="fas fa-lock mr-2"></i>
                Pembayaran Anda aman dan terenkripsi
            </div>

        </div>
    </div>
    @include('customer.payment.partials.scripts')
    <!-- Alert Container -->
<div id="alert-container" class="z-[9999]"></div>

<style>
    /* Copy semua style alert modal dari file tagihan */
    nav.navbar { z-index: 1000 !important; position: fixed !important; top: 0; left: 0; right: 0; }
    #loading-overlay { transition: opacity 0.3s ease; z-index: 9998; }
    #loading-overlay.hidden { display: none; }
    .alert-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.7); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; visibility: hidden; transition: all 0.3s ease; backdrop-filter: blur(4px); padding-top: 60px; }
    .alert-modal.show { opacity: 1; visibility: visible; }
    .alert-modal-content { background: white; border-radius: 16px; width: 90%; max-width: 450px; overflow: hidden; transform: translateY(20px); transition: transform 0.4s ease; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-height: 80vh; overflow-y: auto; }
    .alert-modal.show .alert-modal-content { transform: translateY(0); }
    .alert-modal-header { padding: 1.5rem; text-align: center; border-bottom: 1px solid #e5e7eb; }
    .alert-modal-success .alert-modal-header { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .alert-modal-error .alert-modal-header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .alert-modal-warning .alert-modal-header { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .alert-modal-info .alert-modal-header { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
    .alert-modal-icon { font-size: 3rem; margin-bottom: 0.5rem; }
    .alert-modal-title { font-size: 1.5rem; font-weight: bold; }
    .alert-modal-body { padding: 1.5rem; text-align: center; }
    .alert-modal-message { font-size: 1rem; color: #4b5563; line-height: 1.5; margin-bottom: 1rem; }
    .alert-countdown { background: #f3f4f6; padding: 0.75rem; border-radius: 8px; font-size: 0.875rem; color: #6b7280; margin-top: 1rem; }
    .countdown-number { font-weight: bold; color: #3b82f6; }
    .alert-modal-footer { padding: 1.5rem; justify-content: center; display: flex; gap: 0.75rem; border-top: 1px solid #e5e7eb; background: #f9fafb; }
    .alert-modal-button { flex: 1; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; border: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
    .alert-modal-button-primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
    .alert-modal-button-primary:hover { background: linear-gradient(135deg, #2563eb, #1e40af); transform: translateY(-1px); }
    .alert-modal-button-secondary { background: white; color: #6b7280; border: 1px solid #d1d5db; }
    .alert-modal-button-secondary:hover { background: #f3f4f6; color: #374151; }
    .snap-midtrans-overlay { z-index: 9997 !important; }
</style>

<script>
    // Copy fungsi showAlert dan fungsi pendukungnya dari file tagihan
    function showAlert(message, type = 'info', autoRedirect = false, redirectUrl = null) {
        const alertTypes = {
            success: { icon: 'fas fa-check-circle', class: 'alert-modal-success', title: 'Sukses!', buttonText: 'Lanjutkan' },
            error: { icon: 'fas fa-exclamation-triangle', class: 'alert-modal-error', title: 'Error!', buttonText: 'Coba Lagi' },
            warning: { icon: 'fas fa-bolt', class: 'alert-modal-warning', title: 'Peringatan!', buttonText: 'Mengerti' },
            info: { icon: 'fas fa-info-circle', class: 'alert-modal-info', title: 'Informasi', buttonText: 'OK' }
        };
        const alertConfig = alertTypes[type] || alertTypes.info;
        const alertId = 'alert-' + Date.now();
        const alertHTML = `
            <div id="${alertId}" class="alert-modal ${alertConfig.class}">
                <div class="alert-modal-content">
                    <div class="alert-modal-header">
                        <div class="alert-modal-icon"><i class="${alertConfig.icon}"></i></div>
                        <div class="alert-modal-title">${alertConfig.title}</div>
                    </div>
                    <div class="alert-modal-body">
                        <div class="alert-modal-message">${message}</div>
                        ${autoRedirect ? `
                            <div class="alert-countdown">
                                <i class="fas fa-clock"></i>
                                Akan dialihkan dalam 
                                <span class="countdown-number" id="countdown-${alertId}">3</span> 
                                detik
                            </div>
                        ` : ''}
                    </div>
                    <div class="alert-modal-footer">
                        <button onclick="handleAlertAction('${alertId}', ${autoRedirect}, '${redirectUrl}')" 
                                class="alert-modal-button alert-modal-button-primary" style="flex: none; width: auto; min-width: 140px;">
                            <i class="fas fa-check mr-2"></i>${alertConfig.buttonText}
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('alert-container').insertAdjacentHTML('beforeend', alertHTML);
        const alertElement = document.getElementById(alertId);
        setTimeout(() => alertElement.classList.add('show'), 100);
        if (autoRedirect) startCountdown(alertId, redirectUrl);
        return alertId;
    }

    function startCountdown(alertId, redirectUrl) {
        let countdown = 3;
        const countdownElement = document.getElementById(`countdown-${alertId}`);
        const countdownInterval = setInterval(() => {
            countdown--;
            if (countdownElement) countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                handleAlertAction(alertId, true, redirectUrl);
            }
        }, 1000);
        document.getElementById(alertId).dataset.countdownInterval = countdownInterval;
    }

    function handleAlertAction(alertId, autoRedirect = false, redirectUrl = null) {
        closeAlert(alertId);
        if (autoRedirect && redirectUrl && redirectUrl !== 'null') {
            setTimeout(() => window.location.href = redirectUrl, 300);
        }
    }

    function closeAlert(alertId) {
        const alertElement = document.getElementById(alertId);
        if (!alertElement) return;
        if (alertElement.dataset.countdownInterval) clearInterval(alertElement.dataset.countdownInterval);
        alertElement.classList.remove('show');
        setTimeout(() => alertElement.parentNode?.removeChild(alertElement), 400);
    }

    // Event listener untuk modal
    document.addEventListener('click', e => {
        if (e.target.classList.contains('alert-modal')) closeAlert(e.target.id);
    });
    
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.alert-modal.show');
            if (openModal) closeAlert(openModal.id);
        }
    });

    // Session alerts
    @if (session('success'))
        showAlert('{{ session("success") }}', 'success');
    @endif
    
    @if (session('error'))
        showAlert('{{ session("error") }}', 'error');
    @endif
    
    @if (session('warning'))
        showAlert('{{ session("warning") }}', 'warning');
    @endif
    
    @if (session('info'))
        showAlert('{{ session("info") }}', 'info');
    @endif
</script>
@endsection

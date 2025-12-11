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

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.getElementById('pay-button').addEventListener('click', function () {
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            console.log('Payment Success:', result);
            
            fetch('{{ route("payment.update-status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_id: result.order_id,
                    transaction_status: 'settlement',
                    payment_type: result.payment_type
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Status Update Response:', data);
                window.location.href = '{{ route("customer.list-rak.rak") }}';
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.href = '{{ route("customer.list-rak.rak") }}';
            });
        },
        onPending: function(result) {
            console.log('Payment Pending:', result);
            
            fetch('{{ route("payment.update-status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_id: result.order_id,
                    transaction_status: 'pending',
                    payment_type: result.payment_type
                })
            })
            .then(response => response.json())
            .then(data => {
                alert('Pembayaran sedang diproses. Silakan cek status pembayaran Anda.');
                window.location.href = '{{ route("customer.tagihan") }}';
            });
        },
        onError: function(result) {
            console.log('Payment Error:', result);
            alert('Pembayaran gagal! Silakan coba lagi.');
        },
        onClose: function() {
            console.log('Payment popup closed');
            alert('Anda menutup halaman pembayaran. Silakan selesaikan pembayaran Anda.');
        }
    });
});
</script>
@endpush
@endsection
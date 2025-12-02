@extends('layouts.app')

@section('title', 'Detail Rak - SPACEGO')

@push('styles')
<style>
    .detail-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .detail-card:hover {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .spec-card {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    
    .spec-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        backdrop-filter: blur(8px);
    }
    
    .status-available {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .status-occupied {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .status-maintenance {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }
    
    .type-badge {
        background: rgba(255, 255, 255, 0.95);
        color: #374151;
        border: 1px solid #e5e7eb;
    }
    
    .image-hover {
        transition: transform 0.5s ease;
    }
    
    .image-hover:hover {
        transform: scale(1.05);
    }
    
    .action-button {
        transition: all 0.3s ease;
    }
    
    .action-button:hover {
        transform: translateY(-2px);
    }
    
    .rental-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        color: white;
        box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        animation: slideDown 0.5s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .rental-date-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 0.75rem;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .rental-icon {
        background: rgba(255, 255, 255, 0.2);
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .renewal-card {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        color: white;
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);
        margin-top: 1rem;
    }

    .penalty-badge {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: inline-block;
    }

    .price-breakdown {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- TITLE -->
        <div class="mb-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Detail Rak</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Informasi lengkap mengenai Rak yang Anda pilih</p>
        </div>

        <!-- RENTAL INFO (Jika user sudah menyewa) -->
        @php
            $activeRental = null;
            $hasPendingRenewal = false;
            
            if (Auth::check()) {
                // Ambil transaksi aktif terbaru
                $activeRental = \App\Models\Transaction::where('user_id', Auth::id())
                    ->where('rak_id', $rak->id)
                    ->whereIn('transaction_status', ['settlement', 'capture'])
                    ->orderBy('sewa_berakhir', 'desc')
                    ->first();
                
                // Cek apakah ada transaksi perpanjangan yang pending
                if ($activeRental) {
                    $hasPendingRenewal = \App\Models\Transaction::where('user_id', Auth::id())
                        ->where('rak_id', $rak->id)
                        ->where('parent_transaction_id', $activeRental->id) // Asumsi ada kolom parent_transaction_id
                        ->whereIn('transaction_status', ['pending', 'settlement', 'capture'])
                        ->where('created_at', '>', $activeRental->updated_at) // Transaksi renewal lebih baru
                        ->exists();
                    
                    // Alternatif jika tidak ada parent_transaction_id:
                    // Cek apakah ada transaksi dengan order_id yang mengandung "RENEWAL"
                    if (!$hasPendingRenewal) {
                        $hasPendingRenewal = \App\Models\Transaction::where('user_id', Auth::id())
                            ->where('rak_id', $rak->id)
                            ->where('order_id', 'LIKE', '%RENEWAL%')
                            ->whereIn('transaction_status', ['pending', 'settlement', 'capture'])
                            ->where('created_at', '>', $activeRental->created_at)
                            ->exists();
                    }
                }
            }
        @endphp

        @if($activeRental)
        <div class="mb-8 rental-info-card">
            <div class="flex items-center mb-4">
                <div class="rental-icon mr-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Anda Sedang Menyewa Rak Ini</h3>
                    <p class="text-sm opacity-90">Order ID: {{ $activeRental->order_id }}</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4 mt-6">
                <div class="rental-date-box">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-calendar-check mr-2 text-lg"></i>
                        <span class="font-semibold">Tanggal Mulai Sewa</span>
                    </div>
                    <p class="text-2xl font-bold">
                        {{ \Carbon\Carbon::parse($activeRental->sewa_mulai)->format('d M Y') }}
                    </p>
                    <p class="text-sm opacity-80 mt-1">
                        {{ \Carbon\Carbon::parse($activeRental->sewa_mulai)->diffForHumans() }}
                    </p>
                </div>
                
                <div class="rental-date-box">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-calendar-times mr-2 text-lg"></i>
                        <span class="font-semibold">Tanggal Berakhir Sewa</span>
                    </div>
                    <p class="text-2xl font-bold">
                        {{ \Carbon\Carbon::parse($activeRental->sewa_berakhir)->format('d M Y') }}
                    </p>
                    <p class="text-sm opacity-80 mt-1">
                        {{ \Carbon\Carbon::parse($activeRental->sewa_berakhir)->diffForHumans() }}
                    </p>
                </div>
            </div>
            
@php
    $now = now()->startOfDay();
    $end = \Carbon\Carbon::parse($activeRental->sewa_berakhir)->startOfDay();

    // Selisih hari (+ = masih ada sisa, 0 = hari terakhir, - = sudah lewat)
    $daysDiff = $now->diffInDays($end, false);

    // Tentukan warna + status text
    if ($daysDiff > 0) {
        $statusColor = 'bg-green-600';
        $statusText = $daysDiff . ' Hari Tersisa';
    } elseif ($daysDiff === 0) {
        $statusColor = 'bg-yellow-500';
        $statusText = 'Berakhir Hari Ini';
    } else {
        $statusColor = 'bg-red-600';
        $statusText = 'Masa Tenggang - Lewat ' . abs($daysDiff) . ' Hari';
    }

    // Harga & denda
    $dendaPerHari = 50000;
    $totalDenda = $daysDiff < 0 ? abs($daysDiff) * $dendaPerHari : 0;
    $hargaSewa = $rak->harga_sewa_perbulan ?? 0;
    $totalBayar = $hargaSewa + $totalDenda;
@endphp


            <div class="mt-4 p-3 rounded-lg text-white {{ $statusColor }}">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-white">Sisa Waktu Sewa:</span>
                    <span class="text-xl font-bold text-white">
                        {{ $statusText }}
                    </span>
                </div>
            </div>

            <div class="mt-4 p-4 rounded-lg 
                @if($daysDiff >= 0) bg-black bg-opacity-30 @else bg-red-600 bg-opacity-90 @endif 
                text-white">
                
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-white">
                        @if($daysDiff >= 0)
                            Countdown Waktu Sewa:
                        @else
                            Waktu Lewat Dari Batas Sewa:
                        @endif
                    </span>

                    <span id="countdownTimer" class="text-xl font-bold">00:00:00</span>
                </div>
            </div>

            <!-- RENEWAL SECTION - Tampil jika masa sewa akan habis atau sudah lewat DAN belum ada pembayaran renewal pending -->
            @if($daysDiff <= 1 && !$hasPendingRenewal)
            <div class="renewal-card">
                <div class="flex items-center mb-4">
                    <div class="rental-icon mr-4">
                        <i class="fas fa-redo-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">
                            @if($daysDiff < 0)
                                Perpanjang Sewa & Bayar Denda
                            @else
                                Perpanjang Masa Sewa
                            @endif
                        </h3>
                        <p class="text-sm opacity-90">
                            @if($daysDiff < 0)
                                Segera perpanjang untuk menghindari denda lebih lanjut
                            @else
                                Perpanjang sekarang untuk melanjutkan penyewaan
                            @endif
                        </p>
                    </div>
                </div>

                <div class="price-breakdown">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm">Harga Sewa ({{ $rak->durasi_sewa_hari ?? 30 }} hari)</span>
                        <span class="font-bold">Rp {{ number_format($hargaSewa, 0, ',', '.') }}</span>
                    </div>

                    @if($daysDiff < 0)
                    <div class="flex justify-between items-center mb-2 text-red-200">
                        <span class="text-sm">
                            Denda Keterlambatan ({{ abs($daysDiff) }} hari × Rp {{ number_format($dendaPerHari, 0, ',', '.') }})
                        </span>
                        <span class="font-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-white border-opacity-30 pt-2 mt-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg">Total Pembayaran</span>
                            <span class="font-bold text-2xl">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @else
                    <div class="border-t border-white border-opacity-30 pt-2 mt-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg">Total Pembayaran</span>
                            <span class="font-bold text-2xl">Rp {{ number_format($hargaSewa, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                @if($daysDiff < 0)
                <div class="mt-4 p-3 bg-red-700 bg-opacity-50 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                        <div class="text-sm">
                            <p class="font-semibold mb-1">Perhatian!</p>
                            <p class="opacity-90">
                                Anda berada dalam masa tenggang. Denda akan bertambah Rp {{ number_format($dendaPerHari, 0, ',', '.') }} per hari. 
                                Segera perpanjang untuk menghindari biaya tambahan.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <a href="{{ route('customer.payment.renewal', ['transaction_id' => $activeRental->id]) }}"
                   class="mt-4 w-full flex items-center justify-center space-x-3 px-6 py-4 bg-white text-orange-600 rounded-xl hover:bg-orange-50 transition-all duration-300 font-bold shadow-lg hover:shadow-xl">
                    <i class="fas fa-credit-card"></i>
                    <span>
                        @if($daysDiff < 0)
                            Bayar Sekarang (Sewa + Denda)
                        @else
                            Perpanjang Sewa Sekarang
                        @endif
                    </span>
                </a>
            </div>
            @elseif($hasPendingRenewal)
            <!-- Tampilkan notifikasi jika sudah ada pembayaran renewal pending -->
            <div class="mt-4 p-4 bg-blue-500 bg-opacity-90 rounded-lg text-white">
                <div class="flex items-start">
                    <i class="fas fa-info-circle mr-3 mt-1 text-xl"></i>
                    <div>
                        <p class="font-semibold mb-1">Pembayaran Perpanjangan Berhasil !</p>
                        <p class="text-sm opacity-90">
                            Anda sudah melakukan pembayaran perpanjangan. Silakan cek status pembayaran Anda di halaman riwayat transaksi.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Kirim waktu ke JS -->
            <input type="hidden" id="rentalEndTime" value="{{ \Carbon\Carbon::parse($activeRental->sewa_berakhir)->format('Y-m-d H:i:s') }}">
            <input type="hidden" id="daysDiff" value="{{ $daysDiff }}">
        </div>
        @endif

        <!-- MAIN CARD -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 detail-card">
            
            <div class="grid lg:grid-cols-2 gap-8 p-8">
                <!-- FOTO SECTION -->
                @include('customer.list-rak.partials.photo-section')
                
                <!-- INFO DETAIL SECTION -->
                @include('customer.list-rak.partials.info-section')
            </div>
        </div>

        <!-- SPESIFIKASI SECTION -->
        @include('customer.list-rak.partials.specifications-section')

        <!-- ACTION BUTTONS -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <a href="{{ route('customer.list-rak.list-rak') }}"
               class="flex items-center justify-center space-x-3 px-8 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-300 font-semibold shadow-md hover:shadow-lg group action-button">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                <span>Kembali ke Daftar Rak</span>
            </a>

            @if($activeRental)
                <button class="flex-1 flex items-center justify-center space-x-3 px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl cursor-default font-semibold shadow-lg">
                    <i class="fas fa-check-circle"></i>
                    <span>Rak Sedang Anda Sewa</span>
                </button>
            @elseif ($rak->status === 'tersedia')
                <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                   class="flex-1 flex items-center justify-center space-x-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all duration-300 font-semibold shadow-lg action-button">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Sewa Sekarang</span>
                </a>
            @else
                <button class="flex-1 flex items-center justify-center space-x-3 px-8 py-4 bg-gray-300 text-gray-500 rounded-xl cursor-not-allowed font-semibold">
                    <i class="fas fa-ban"></i>
                    <span>Tidak Tersedia</span>
                </button>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    let endTime = document.getElementById("rentalEndTime");
    let daysDiff = parseInt(document.getElementById("daysDiff")?.value ?? 0);

    if (!endTime) return;

    let end = new Date(endTime.value).getTime();

    function updateCountdown() {
        let now = new Date().getTime();

        let distance = end - now;

        let isGrace = distance < 0;

        let absDistance = Math.abs(distance);

        let hours = Math.floor(absDistance / (1000 * 60 * 60));
        let minutes = Math.floor((absDistance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((absDistance % (1000 * 60)) / 1000);

        let formatted =
            (hours < 10 ? "0" + hours : hours) + ":" +
            (minutes < 10 ? "0" + minutes : minutes) + ":" +
            (seconds < 10 ? "0" + seconds : seconds);

        let display = document.getElementById("countdownTimer");

        if (isGrace) {
            display.innerHTML = "Lewat " + formatted;
            display.style.color = "#ffdddd";
        } else {
            display.innerHTML = formatted;
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>
@endpush

<!-- WhatsApp Button -->
@include('customer.payment.partials.whatsapp-button')
@endsection
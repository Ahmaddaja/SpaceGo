{{-- resources/views/customer/list-rak/partials/rak-card.blade.php --}}

@php
    // Cek apakah rak terkunci
    $hasPendingTransaction = \App\Models\Transaction::where('rak_id', $rak->id)
        ->where('transaction_status', 'pending')
        ->exists();

    // Cek apakah user ini yang sedang memproses
    $isMyPendingTransaction = false;
    if ($hasPendingTransaction) {
        $isMyPendingTransaction = \App\Models\Transaction::where('rak_id', $rak->id)
            ->where('transaction_status', 'pending')
            ->where('user_id', Auth::id())
            ->exists();
    }

    $isLocked = $rak->status !== 'tersedia' || $hasPendingTransaction;

    // Tentukan status display dan ribbon class
    if ($hasPendingTransaction) {
        $statusLabel = $isMyPendingTransaction ? 'Menunggu Pembayaran' : 'Sedang Direservasi';
        $statusIcon = $isMyPendingTransaction ? 'fa-clock' : 'fa-user-lock';
        $ribbonClass = 'ribbon-locked';
    } elseif ($rak->status === 'tersedia') {
        $statusLabel = 'Tersedia';
        $statusIcon = 'fa-check-circle';
        $ribbonClass = 'ribbon-tersedia';
    } elseif ($rak->status === 'terisi') {
        $statusLabel = 'Terisi';
        $statusIcon = 'fa-box';
        $ribbonClass = 'ribbon-terisi';
    } elseif ($rak->status === 'maintenance') {
        $statusLabel = 'Maintenance';
        $statusIcon = 'fa-tools';
        $ribbonClass = 'ribbon-maintenance';
    } elseif ($rak->status === 'pengosongan') {
        $statusLabel = 'Pengosongan';
        $statusIcon = 'fa-box-open';
        $ribbonClass = 'ribbon-pengosongan';
    } else {
        $statusLabel = ucfirst($rak->status);
        $statusIcon = 'fa-cube';
        $ribbonClass = 'ribbon-default';
    }

    // Text untuk tombol
    $buttonText = $hasPendingTransaction
        ? ($isMyPendingTransaction
            ? 'Menunggu Pembayaran'
            : 'Menunggu')
        : 'Tidak Tersedia';

    $buttonTooltip = $hasPendingTransaction
        ? ($isMyPendingTransaction
            ? 'Selesaikan pembayaran Anda di halaman Tagihan'
            : 'Rak ini sedang dalam proses pemesanan oleh customer lain')
        : 'Rak tidak tersedia untuk disewa';
@endphp

<div class="rak-card bg-white rounded-2xl shadow-lg overflow-hidden group border border-gray-100 relative">

    <!-- RIBBON - KIRI ATAS -->
    <div class="status-ribbon {{ $ribbonClass }}">
        <i class="fas {{ $statusIcon }}"></i>
        <span>{{ $statusLabel }}</span>
    </div>

    <!-- FOTO CAROUSEL -->
    @php
        $hasMultiplePhotos = $rak->fotos && $rak->fotos->count() > 0;
        $photos = [];

        if ($hasMultiplePhotos) {
            $photos = $rak->fotos->pluck('path')->toArray();
        } elseif ($rak->foto) {
            $photos = [$rak->foto];
        }
    @endphp

    <div
        class="relative w-full h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 photo-carousel-container">
        @if (count($photos) > 0)
            @foreach ($photos as $index => $photo)
                <div class="carousel-slide {{ $index === 0 ? 'active' : '' }}" data-slide-index="{{ $index }}">
                    <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover image-hover"
                        alt="Foto Rak {{ $index + 1 }}">
                </div>
            @endforeach

            @if (count($photos) > 1)
                <button class="carousel-btn carousel-prev" onclick="changeSlide(this, -1, '{{ $rak->id }}')">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn carousel-next" onclick="changeSlide(this, 1, '{{ $rak->id }}')">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="carousel-indicators">
                    @foreach ($photos as $index => $photo)
                        <button class="indicator {{ $index === 0 ? 'active' : '' }}"
                            onclick="goToSlide(this, {{ $index }}, '{{ $rak->id }}')"></button>
                    @endforeach
                </div>

                <div class="photo-counter-badge">
                    <i class="fas fa-images"></i>
                    <span class="current-photo">1</span>/<span class="total-photos">{{ count($photos) }}</span>
                </div>
            @endif
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100">
                <i class="fas fa-pallet text-4xl text-blue-500 opacity-50"></i>
            </div>
        @endif
    </div>

    <!-- CONTENT -->
    <div class="p-6">
        <div class="mb-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nama Rak</p>
            <h3 class="text-2xl font-bold text-blue-600">{{ $rak->nama_rak }}</h3>
        </div>

        <div class="space-y-4 text-sm mb-4">
            <div class="flex items-start">
                <div class="bg-green-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-layer-group text-green-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Jenis Rak</p>
                    <p class="text-gray-800 font-semibold">{{ $rak->jenis_rak }}</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-map-marker-alt text-purple-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Lokasi Gudang</p>
                    <p class="text-gray-800 font-semibold">{{ $rak->gudang->alamat ?? 'Tidak ada data gudang' }}</p>
                </div>
            </div>
        </div>

        <!-- DURASI -->
        <div class="mt-5 pt-5 border-t border-gray-100">
            <div class="flex items-center mb-3">
                <div class="bg-amber-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-calendar-alt text-amber-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Durasi Sewa</p>
                    <p class="text-lg font-bold text-amber-600">
                        {{ $rak->durasi_sewa_hari }} Hari
                        <span class="text-xs text-gray-500 font-normal">
                            ({{ round($rak->durasi_sewa_hari / 30, 1) }} bulan)
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- HARGA -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 mb-1">Harga Sewa</p>
            <p class="text-2xl font-bold text-green-600">
                Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                <span class="text-sm text-gray-500 font-normal">/{{ $rak->durasi_sewa_hari }} hari</span>
            </p>
        </div>

        <!-- BUTTONS -->
        <div class="mt-6 flex gap-3">
            <a href="{{ route('customer.list-rak.show', $rak->id) }}"
                class="flex-1 bg-gray-100 text-center text-gray-700 py-3 rounded-xl hover:bg-gray-200 transition-all duration-300 font-medium flex items-center justify-center space-x-2 action-button">
                <i class="fas fa-eye text-sm"></i>
                <span>Detail</span>
            </a>

            @if (!$isLocked)
                <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-center text-white py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-md hover:shadow-lg font-medium flex items-center justify-center space-x-2 action-button">
                    <i class="fas fa-shopping-cart text-sm"></i>
                    <span>Sewa Sekarang</span>
                </a>
            @else
                @if ($isMyPendingTransaction)
                    {{-- Jika user ini yang punya pending transaction --}}
                    <a href="{{ route('customer.tagihan') }}"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-amber-600 text-white py-3 rounded-xl hover:from-amber-600 hover:to-amber-700 transition shadow-md hover:shadow-lg font-medium flex items-center justify-center space-x-2 action-button"
                        title="Klik untuk melanjutkan pembayaran">
                        <i class="fas fa-clock text-sm"></i>
                        <span>Lanjutkan Bayar</span>
                    </a>
                @else
                    {{-- Jika user lain yang punya pending transaction --}}
                    <div class="flex-1 bg-gradient-to-r from-orange-400 to-orange-500 text-white py-3 rounded-xl cursor-not-allowed font-medium flex items-center justify-center space-x-2 shadow-md"
                        title="{{ $buttonTooltip }}">
                        <i class="fas fa-user-lock text-sm"></i>
                        <span>{{ $buttonText }}</span>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .rak-card {
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            /* RIBBON STYLE */
            .status-ribbon {
                position: absolute;
                top: 12px;
                left: -32px;
                transform: rotate(-45deg);
                padding: 8px 40px;
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                display: flex;
                align-items: center;
                gap: 6px;
                color: white;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                z-index: 30;
                white-space: nowrap;
            }

            .ribbon-tersedia {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .ribbon-terisi {
                background: linear-gradient(135deg, #ef4444, #dc2626);
            }

            .ribbon-maintenance {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .ribbon-pengosongan {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            }

            .ribbon-locked {
                background: linear-gradient(135deg, #fb923c, #f97316);
                animation: pulse-ribbon 2s ease-in-out infinite;
            }

            .ribbon-default {
                background: linear-gradient(135deg, #6b7280, #4b5563);
            }

            @keyframes pulse-ribbon {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.85;
                }
            }

            .status-ribbon i {
                font-size: 0.875rem;
            }

            /* CAROUSEL STYLES */
            .photo-carousel-container {
                position: relative;
            }

            .carousel-slide {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0;
                transition: opacity 0.5s ease-in-out;
                pointer-events: none;
            }

            .carousel-slide.active {
                opacity: 1;
                pointer-events: auto;
            }

            .carousel-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(0, 0, 0, 0.5);
                color: white;
                border: none;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 20;
                transition: all 0.3s ease;
                opacity: 0;
            }

            .photo-carousel-container:hover .carousel-btn {
                opacity: 1;
            }

            .carousel-btn:hover {
                background: rgba(0, 0, 0, 0.8);
                transform: translateY(-50%) scale(1.1);
            }

            .carousel-prev {
                left: 12px;
            }

            .carousel-next {
                right: 12px;
            }

            .carousel-indicators {
                position: absolute;
                bottom: 12px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 6px;
                z-index: 20;
            }

            .indicator {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.5);
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .indicator.active {
                background: white;
                width: 24px;
                border-radius: 4px;
            }

            .photo-counter-badge {
                position: absolute;
                top: 12px;
                right: 12px;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(10px);
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 6px;
                z-index: 20;
            }

            .rak-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            .action-button {
                transition: all 0.3s ease;
            }

            .action-button:hover:not([disabled]) {
                transform: translateY(-2px);
            }

            .image-hover {
                transition: transform 0.5s ease;
            }

            .rak-card:hover .image-hover {
                transform: scale(1.05);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function changeSlide(button, direction, rakId) {
                const container = button.closest('.photo-carousel-container');
                const slides = container.querySelectorAll('.carousel-slide');
                const indicators = container.querySelectorAll('.indicator');
                const counterCurrent = container.querySelector('.current-photo');

                let currentIndex = 0;
                slides.forEach((slide, index) => {
                    if (slide.classList.contains('active')) {
                        currentIndex = index;
                    }
                });

                let newIndex = currentIndex + direction;
                if (newIndex >= slides.length) newIndex = 0;
                if (newIndex < 0) newIndex = slides.length - 1;

                slides[currentIndex].classList.remove('active');
                slides[newIndex].classList.add('active');
                indicators[currentIndex].classList.remove('active');
                indicators[newIndex].classList.add('active');

                if (counterCurrent) {
                    counterCurrent.textContent = newIndex + 1;
                }
            }

            function goToSlide(button, index, rakId) {
                const container = button.closest('.photo-carousel-container');
                const slides = container.querySelectorAll('.carousel-slide');
                const indicators = container.querySelectorAll('.indicator');
                const counterCurrent = container.querySelector('.current-photo');

                slides.forEach(slide => slide.classList.remove('active'));
                indicators.forEach(ind => ind.classList.remove('active'));

                slides[index].classList.add('active');
                indicators[index].classList.add('active');

                if (counterCurrent) {
                    counterCurrent.textContent = index + 1;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const carousels = document.querySelectorAll('.photo-carousel-container');

                carousels.forEach(carousel => {
                    const slides = carousel.querySelectorAll('.carousel-slide');
                    if (slides.length > 1) {
                        let autoPlayInterval;

                        const startAutoPlay = () => {
                            autoPlayInterval = setInterval(() => {
                                const nextBtn = carousel.querySelector('.carousel-next');
                                if (nextBtn) nextBtn.click();
                            }, 5000);
                        };

                        const stopAutoPlay = () => {
                            clearInterval(autoPlayInterval);
                        };

                        startAutoPlay();
                        carousel.addEventListener('mouseenter', stopAutoPlay);
                        carousel.addEventListener('mouseleave', startAutoPlay);
                    }
                });
            });
        </script>
    @endpush
@endonce

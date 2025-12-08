<div class="rak-card bg-white rounded-2xl shadow-lg overflow-hidden group border border-gray-100 relative">

    <!-- RIBBON - DIPINDAHKAN KE KIRI ATAS -->
    <div class="status-ribbon ribbon-{{ $rak->status }}">
        <i
            class="fas 
            @if ($rak->status == 'tersedia') fa-check
            @elseif($rak->status == 'terisi') fa-box
            @else fa-tools @endif
        "></i>
        <span>
            @if ($rak->status == 'tersedia')
                Tersedia
            @elseif($rak->status == 'terisi')
                Terisi
            @else
                Maintenance
            @endif
        </span>
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
                <!-- Navigation Arrows -->
                <button class="carousel-btn carousel-prev" onclick="changeSlide(this, -1, '{{ $rak->id }}')">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn carousel-next" onclick="changeSlide(this, 1, '{{ $rak->id }}')">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <!-- Indicators -->
                <div class="carousel-indicators">
                    @foreach ($photos as $index => $photo)
                        <button class="indicator {{ $index === 0 ? 'active' : '' }}"
                            onclick="goToSlide(this, {{ $index }}, '{{ $rak->id }}')"></button>
                    @endforeach
                </div>

                <!-- Photo Counter Badge -->
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
                    <p class="text-gray-800 font-semibold"> {{ $rak->gudang->alamat ?? 'Tidak ada data gudang' }}</p>
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

            @if ($rak->status === 'tersedia')
                <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-center text-white py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-md hover:shadow-lg font-medium action-button">
                    Sewa Sekarang
                </a>
            @else
                <button
                    class="flex-1 bg-gray-300 text-gray-500 py-3 rounded-xl cursor-not-allowed font-medium flex items-center justify-center space-x-2">
                    <i class="fas fa-ban text-sm"></i>
                    <span>Tidak Tersedia</span>
                </button>
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

            /* RIBBON STYLE - DIUBAH KE KIRI ATAS */
            .status-ribbon {
                position: absolute;
                top: 18px;
                left: -60px;
                transform: rotate(-45deg);
                padding: 14px 75px;
                font-size: 0.85rem;
                font-weight: 700;
                letter-spacing: 0.5px;
                display: flex;
                align-items: center;
                gap: 8px;
                color: white;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                z-index: 30;
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

            .status-ribbon i {
                font-size: 1rem;
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

            /* INDICATORS */
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

            /* PHOTO COUNTER BADGE */
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

            /* Type Badge */
            .type-badge {
                background: rgba(255, 255, 255, 0.95);
                color: #374151;
                border: 1px solid #e5e7eb;
                font-size: 0.75rem;
                font-weight: 600;
                padding: 0.5rem 0.75rem;
                border-radius: 9999px;
                backdrop-filter: blur(8px);
                display: flex;
                align-items: center;
                gap: 4px;
            }

            /* Card hover effects */
            .rak-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            .action-button {
                transition: all 0.3s ease;
            }

            .action-button:hover {
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

                // Update slides
                slides[currentIndex].classList.remove('active');
                slides[newIndex].classList.add('active');

                // Update indicators
                indicators[currentIndex].classList.remove('active');
                indicators[newIndex].classList.add('active');

                // Update counter
                if (counterCurrent) {
                    counterCurrent.textContent = newIndex + 1;
                }
            }

            function goToSlide(button, index, rakId) {
                const container = button.closest('.photo-carousel-container');
                const slides = container.querySelectorAll('.carousel-slide');
                const indicators = container.querySelectorAll('.indicator');
                const counterCurrent = container.querySelector('.current-photo');

                // Remove active from all
                slides.forEach(slide => slide.classList.remove('active'));
                indicators.forEach(ind => ind.classList.remove('active'));

                // Add active to target
                slides[index].classList.add('active');
                indicators[index].classList.add('active');

                // Update counter
                if (counterCurrent) {
                    counterCurrent.textContent = index + 1;
                }
            }

            // Auto-play carousel (optional)
            document.addEventListener('DOMContentLoaded', function() {
                const carousels = document.querySelectorAll('.photo-carousel-container');

                carousels.forEach(carousel => {
                    const slides = carousel.querySelectorAll('.carousel-slide');
                    if (slides.length > 1) {
                        let autoPlayInterval;

                        // Auto play every 5 seconds
                        const startAutoPlay = () => {
                            autoPlayInterval = setInterval(() => {
                                const nextBtn = carousel.querySelector('.carousel-next');
                                if (nextBtn) {
                                    nextBtn.click();
                                }
                            }, 5000);
                        };

                        const stopAutoPlay = () => {
                            clearInterval(autoPlayInterval);
                        };

                        // Start auto play
                        startAutoPlay();

                        // Stop on hover, resume on leave
                        carousel.addEventListener('mouseenter', stopAutoPlay);
                        carousel.addEventListener('mouseleave', startAutoPlay);
                    }
                });

                // Card animation on load
                const cards = document.querySelectorAll('.rak-card');
                cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';

                    setTimeout(() => {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            });
        </script>
    @endpush
@endonce
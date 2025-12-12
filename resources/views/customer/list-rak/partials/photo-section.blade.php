<div class="relative">
    <!-- Status Ribbon - DIPINDAHKAN KE KIRI ATAS -->
    <div class="status-ribbon ribbon-{{ $rak->status }}">
        <span>
          @if ($rak->status === 'tersedia')
        <span class="status-badge status-available flex items-center space-x-1">
            <i class="fas fa-check-circle"></i>
            <span>Tersedia</span>
        </span>
    @elseif($rak->status === 'terisi')
        <span class="status-badge status-occupied flex items-center space-x-1">
            <i class="fas fa-lock"></i>
            <span>Terisi</span>
        </span>
    @elseif($rak->status === 'maintenance')
        <span class="status-badge status-maintenance flex items-center space-x-1">
            <i class="fas fa-tools"></i>
            <span>Maintenance</span>
        </span>
    @elseif($rak->status === 'pengosongan')
        <span class="status-badge status-pengosongan flex items-center space-x-1">
            <i class="fas fa-box-open"></i>
            <span>Pengosongan</span>
        </span>
    @endif
        </span>
    </div>

    @php
        $hasMultiplePhotos = $rak->fotos && $rak->fotos->count() > 0;
        $photos = [];

        if ($hasMultiplePhotos) {
            $photos = $rak->fotos->pluck('path')->toArray();
        } elseif ($rak->foto) {
            $photos = [$rak->foto];
        }
    @endphp

    <!-- Main Carousel -->
    <div
        class="rounded-2xl overflow-hidden shadow-lg h-96 bg-gradient-to-br from-gray-100 to-gray-200 detail-photo-carousel">
        @if (count($photos) > 0)
            @foreach ($photos as $index => $photo)
                <div class="detail-carousel-slide {{ $index === 0 ? 'active' : '' }}"
                    data-slide-index="{{ $index }}">
                    <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover image-hover"
                        alt="Foto Rak {{ $index + 1 }}">
                </div>
            @endforeach

            @if (count($photos) > 1)
                <!-- Navigation Arrows -->
                <button class="detail-carousel-btn detail-carousel-prev" onclick="changeDetailSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="detail-carousel-btn detail-carousel-next" onclick="changeDetailSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <!-- Photo Counter -->
                <div class="detail-photo-counter">
                    <i class="fas fa-images"></i>
                    <span class="detail-current-photo">1</span>/<span>{{ count($photos) }}</span>
                </div>
            @endif
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-pallet text-8xl text-blue-500 opacity-30"></i>
            </div>
        @endif
    </div>

    <!-- Thumbnail Gallery -->
    @if (count($photos) > 1)
        <div class="grid grid-cols-5 gap-3 mt-4">
            @foreach ($photos as $index => $photo)
                <div class="detail-thumbnail {{ $index === 0 ? 'active' : '' }}"
                    onclick="goToDetailSlide({{ $index }})">
                    <img src="{{ asset('storage/' . $photo) }}"
                        class="w-full h-20 object-cover rounded-lg cursor-pointer transition-all duration-300"
                        alt="Thumbnail {{ $index + 1 }}">
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('styles')
    <style>
        .relative {
            position: relative;
            overflow: hidden;
        }

        /* RIBBON STYLE - DIUBAH KE KIRI ATAS */
        .status-ribbon {
            position: absolute;
            top: 0;
            left: 0;
            width: 70%;            
            padding: 10px 16px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
            color: white;
            background: rgba(0,0,0,0.4); /* nanti ditimpa sama class status */
            z-index: 30;
            white-space: nowrap;
            border-bottom-right-radius: 8px; /* biar cantik */
            border-bottom-left-radius: 8px;
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

        /* DETAIL CAROUSEL STYLES */
        .detail-photo-carousel {
            position: relative;
        }

        .detail-carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            pointer-events: none;
        }

        .detail-carousel-slide.active {
            opacity: 1;
            pointer-events: auto;
        }

        .detail-carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 20;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .detail-carousel-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: translateY(-50%) scale(1.1);
        }

        .detail-carousel-prev {
            left: 20px;
        }

        .detail-carousel-next {
            right: 20px;
        }

        .detail-photo-counter {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            color: white;
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 20;
        }

        /* THUMBNAIL STYLES */
        .detail-thumbnail {
            position: relative;
            border: 3px solid transparent;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .detail-thumbnail:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .detail-thumbnail.active {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }

        .detail-thumbnail img {
            display: block;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Image hover effect */
        .image-hover {
            transition: transform 0.5s ease;
        }

        .image-hover:hover {
            transform: scale(1.05);
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentDetailSlide = 0;

        function changeDetailSlide(direction) {
            const carousel = document.querySelector('.detail-photo-carousel');
            const slides = carousel.querySelectorAll('.detail-carousel-slide');
            const thumbnails = document.querySelectorAll('.detail-thumbnail');
            const counter = document.querySelector('.detail-current-photo');

            currentDetailSlide += direction;

            if (currentDetailSlide >= slides.length) currentDetailSlide = 0;
            if (currentDetailSlide < 0) currentDetailSlide = slides.length - 1;

            updateDetailCarousel(slides, thumbnails, counter);
        }

        function goToDetailSlide(index) {
            const carousel = document.querySelector('.detail-photo-carousel');
            const slides = carousel.querySelectorAll('.detail-carousel-slide');
            const thumbnails = document.querySelectorAll('.detail-thumbnail');
            const counter = document.querySelector('.detail-current-photo');

            currentDetailSlide = index;
            updateDetailCarousel(slides, thumbnails, counter);
        }

        function updateDetailCarousel(slides, thumbnails, counter) {
            // Update slides
            slides.forEach((slide, index) => {
                slide.classList.toggle('active', index === currentDetailSlide);
            });

            // Update thumbnails
            thumbnails.forEach((thumb, index) => {
                thumb.classList.toggle('active', index === currentDetailSlide);
            });

            // Update counter
            if (counter) {
                counter.textContent = currentDetailSlide + 1;
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                changeDetailSlide(-1);
            } else if (e.key === 'ArrowRight') {
                changeDetailSlide(1);
            }
        });

        // Auto-play (optional - comment out if not needed)
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('.detail-photo-carousel');
            const slides = carousel?.querySelectorAll('.detail-carousel-slide');

            if (slides && slides.length > 1) {
                let autoPlayInterval;

                const startAutoPlay = () => {
                    autoPlayInterval = setInterval(() => {
                        changeDetailSlide(1);
                    }, 5000);
                };

                const stopAutoPlay = () => {
                    clearInterval(autoPlayInterval);
                };

                // Start auto play
                startAutoPlay();

                // Stop on hover
                carousel.addEventListener('mouseenter', stopAutoPlay);
                carousel.addEventListener('mouseleave', startAutoPlay);
            }
        });
    </script>
@endpush
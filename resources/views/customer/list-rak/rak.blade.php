@extends('layouts.app', ['title' => 'Rak Yang Sudah Dibeli'])

@section('title', 'Rak Yang Sudah Dibeli')

@push('styles')
<style>
    .rak-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        position: relative;
        overflow: hidden;
    }
    
    .rak-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .type-badge {
        background: rgba(255, 255, 255, 0.95);
        color: #374151;
        border: 1px solid #e5e7eb;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        backdrop-filter: blur(8px);
    }
    
    .price-gradient {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-left: 4px solid #10b981;
    }
    
    .duration-gradient {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-left: 4px solid #f59e0b;
    }
    
    .code-gradient {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-left: 4px solid #3b82f6;
    }
    
    .empty-state {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid #fbbf24;
    }
    
    .info-item {
        transition: all 0.2s ease;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-item:hover {
        background: #f9fafb;
        border-radius: 0.5rem;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .pagination-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
    }
    
    .pagination .page-link {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        margin: 0 0.25rem;
        padding: 0.5rem 1rem;
        color: #374151;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    
    .pagination .page-item.active .page-link {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    
    .action-btn {
        transition: all 0.2s ease;
        font-weight: 600;
        border-radius: 0.75rem;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
    }
    
    .btn-detail {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    
    .btn-detail:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .btn-history {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }
    
    .btn-history:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    }
    
    /* Animation for empty state */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    
    /* Gradient text for title */
    .gradient-text {
        background: linear-gradient(135deg, #1e40af, #7c3aed);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
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

    .image-hover {
        transition: transform 0.5s ease;
    }

    .rak-card:hover .image-hover {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- TITLE SECTION -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg mb-6">
                <i class="fas fa-boxes text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 gradient-text">
                Rak Yang Sudah Disewa
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Kelola dan pantau semua rak penyimpanan yang telah Anda sewa dalam satu tempat
            </p>
            
            <!-- Stats Summary -->
            <div class="flex justify-center items-center space-x-6 mt-6">
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                     <span>Total: <strong class="text-gray-700">{{ $raks->where('status', 'terisi')->count() }} rak</strong></span>
                </div>
                {{-- <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <span>Aktif: <strong class="text-gray-700">{{ $raks->where('status', 'terisi')->count() }} rak</strong></span>
                </div> --}}
            </div>
        </div>

        @if($raks->count() == 0)
            <!-- EMPTY STATE -->
            <div class="empty-state rounded-2xl p-8 md:p-12 text-center max-w-2xl mx-auto">
                <div class="flex justify-center mb-6">
                    <div class="bg-yellow-100 p-6 rounded-full floating-icon">
                        <i class="fas fa-shopping-cart text-yellow-600 text-4xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-yellow-800 mb-3">Belum Ada Rak yang Dibeli</h3>
                <p class="text-yellow-700 mb-2 text-lg">Anda belum melakukan penyewaan rak apapun.</p>
                <p class="text-yellow-600 mb-8 text-sm">Mulai sewa rak pertama Anda dan nikmati kemudahan penyimpanan!</p>
                <a href="{{ route('customer.list-rak.list-rak') }}" 
                   class="inline-flex items-center space-x-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all duration-300 font-semibold text-lg action-btn">
                    <i class="fas fa-pallet"></i>
                    <span>Sewa Rak Sekarang</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        @else

        <!-- RAK LIST -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">

            @foreach($raks as $rak)
            <div class="rak-card bg-white rounded-2xl shadow-lg overflow-hidden">
                
                <!-- Image Section with Carousel -->
                <div class="relative">
                    @php
                        $hasMultiplePhotos = $rak->fotos && $rak->fotos->count() > 0;
                        $photos = [];

                        if ($hasMultiplePhotos) {
                            $photos = $rak->fotos->pluck('path')->toArray();
                        } elseif ($rak->foto) {
                            $photos = [$rak->foto];
                        }
                    @endphp

                    <div class="relative w-full h-48 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 photo-carousel-container">
                        @if (count($photos) > 0)
                            @foreach ($photos as $index => $photo)
                                <div class="carousel-slide {{ $index === 0 ? 'active' : '' }}" data-slide-index="{{ $index }}">
                                    <img src="{{ asset('storage/' . $photo) }}" 
                                         class="w-full h-full object-cover image-hover"
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
                                    @foreach ($photos as $idx => $photo)
                                        <button class="indicator {{ $idx === 0 ? 'active' : '' }}"
                                            onclick="goToSlide(this, {{ $idx }}, '{{ $rak->id }}')"></button>
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

                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6 space-y-5">
                    
                    <!-- Header -->
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $rak->nama_rak }}</h3>
                        <div class="code-gradient px-4 py-3 rounded-xl">
                            <p class="text-blue-700 text-sm font-semibold flex items-center">
                                <i class="fas fa-barcode mr-2"></i>
                                Kode: {{ $rak->kode_rak }}
                            </p>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="space-y-3">
                        <div class="info-item flex items-center justify-between">
                            <span class="text-gray-600 text-sm flex items-center">
                                <i class="fas fa-warehouse text-blue-500 mr-3 w-5 text-center"></i>
                                Gudang
                            </span>
                            <span class="text-gray-900 font-semibold text-sm">
                                {{ $rak->gudang->nama_gudang ?? $rak->lokasi_gudang }}
                            </span>
                        </div>

                        <div class="info-item flex items-center justify-between">
                            <span class="text-gray-600 text-sm flex items-center">
                                <i class="fas fa-layer-group text-green-500 mr-3 w-5 text-center"></i>
                                Jenis
                            </span>
                            <span class="text-gray-900 font-semibold text-sm">{{ $rak->jenis_rak }}</span>
                        </div>

                        <div class="info-item flex items-center justify-between">
                            <span class="text-gray-600 text-sm flex items-center">
                                <i class="fas fa-ruler-combined text-purple-500 mr-3 w-5 text-center"></i>
                                Dimensi
                            </span>
                            <span class="text-gray-900 font-semibold text-sm">
                                {{ $rak->panjang }}×{{ $rak->lebar }}×{{ $rak->tinggi }}m
                            </span>
                        </div>

                        <div class="info-item flex items-center justify-between">
                            <span class="text-gray-600 text-sm flex items-center">
                                <i class="fas fa-weight text-orange-500 mr-3 w-5 text-center"></i>
                                Kapasitas
                            </span>
                            <span class="text-gray-900 font-semibold text-sm">{{ number_format($rak->kapasitas_berat, 0, ',', '.') }} kg</span>
                        </div>

                        <!-- Status Info -->
                        <div class="info-item flex items-center justify-between">
                            <span class="text-gray-600 text-sm flex items-center">
                                <i class="fas fa-info-circle text-purple-500 mr-3 w-5 text-center"></i>
                                Status
                            </span>
                            @if ($rak->status === 'tersedia')
                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">
                                    <i class="fas fa-check mr-1"></i>
                                    Tersedia
                                </span>
                            @elseif($rak->status === 'terisi')
                                <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-semibold">
                                    <i class="fas fa-box mr-1"></i>
                                    Terisi
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-semibold">
                                    <i class="fas fa-tools mr-1"></i>
                                    Maintenance
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Duration Section -->
                    <div class="duration-gradient p-4 rounded-xl">
                        <p class="text-amber-700 text-sm font-medium mb-1 flex items-center">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Durasi Sewa
                        </p>
                        <p class="text-amber-600 text-2xl font-bold">
                            {{ $rak->durasi_sewa_hari }} Hari
                            <span class="text-sm font-normal text-amber-500">
                                ({{ round($rak->durasi_sewa_hari / 30, 1) }} bulan)
                            </span>
                        </p>
                    </div>

                    <!-- Price Section -->
                    <div class="price-gradient p-4 rounded-xl">
                        <p class="text-green-700 text-sm font-medium mb-1 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Harga Sewa
                        </p>
                        <p class="text-green-600 text-2xl font-bold">
                            Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                            <span class="text-sm font-normal text-green-500">/{{ $rak->durasi_sewa_hari }} hari</span>
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <a href="{{ route('customer.list-rak.detail', $rak->id) }}" 
                           class="flex-1 action-btn btn-detail text-center py-3 px-4 rounded-lg font-semibold text-sm">
                            <i class="fas fa-eye mr-2"></i> Detail Rak
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <!-- PAGINATION -->
        @if($raks->hasPages())
        <div class="pagination-container mt-12 p-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan 
                    <span class="font-medium">{{ $raks->firstItem() }}</span> 
                    sampai 
                    <span class="font-medium">{{ $raks->lastItem() }}</span> 
                    dari 
                    <span class="font-medium">{{ $raks->total() }}</span> 
                    rak
                </div>
                <div class="flex space-x-2">
                    <!-- Previous Page -->
                    @if ($raks->onFirstPage())
                        <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $raks->previousPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    <!-- Page Numbers -->
                    @php
                        $current = $raks->currentPage();
                        $last = $raks->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $raks->url(1) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">1</a>
                        @if($start > 2)
                            <span class="px-3 py-2 text-gray-500">...</span>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $current)
                            <span class="px-3 py-2 bg-blue-600 text-white rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $raks->url($page) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="px-3 py-2 text-gray-500">...</span>
                        @endif
                        <a href="{{ $raks->url($last) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">{{ $last }}</a>
                    @endif

                    <!-- Next Page -->
                    @if ($raks->hasMorePages())
                        <a href="{{ $raks->nextPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @endif

    </div>
</div>
@endsection

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

    document.addEventListener('DOMContentLoaded', function() {
        // Add loading animation to cards
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

        // Auto-play carousel
        const carousels = document.querySelectorAll('.photo-carousel-container');
        carousels.forEach(carousel => {
            const slides = carousel.querySelectorAll('.carousel-slide');
            if (slides.length > 1) {
                let autoPlayInterval;

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

                startAutoPlay();
                carousel.addEventListener('mouseenter', stopAutoPlay);
                carousel.addEventListener('mouseleave', startAutoPlay);
            }
        });
    });
</script>
@endpush
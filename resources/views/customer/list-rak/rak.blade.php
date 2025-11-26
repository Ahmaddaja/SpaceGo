@extends('layouts.app', ['title' => 'Rak Yang Sudah Dibeli'])

@section('title', 'Rak Yang Sudah Dibeli')

@push('styles')
<style>
    .rak-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .rak-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
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
    
    .price-gradient {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-left: 4px solid #10b981;
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
                Rak Yang Sudah Dibeli
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Kelola dan pantau semua rak penyimpanan yang telah Anda sewa dalam satu tempat
            </p>
            
            <!-- Stats Summary -->
            <div class="flex justify-center items-center space-x-6 mt-6">
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span>Total: <strong class="text-gray-700">0 rak</strong></span>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <span>Aktif: <strong class="text-gray-700">{{ $raks->where('status', 'terisi')->count() }} rak</strong></span>
                </div>
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
                
                <!-- Image Section -->
                <div class="relative">
                    <img src="{{ $rak->foto ? asset('storage/' . $rak->foto) : asset('images/default-rak.jpg') }}"
                         class="w-full h-48 object-cover"
                         alt="{{ $rak->nama_rak }}"
                         onerror="this.src='https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'">
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        @if($rak->status == 'terisi')
                        <span class="status-badge status-occupied flex items-center space-x-1">
                            <i class="fas fa-box text-xs"></i>
                            <span>Terisi</span>
                        </span>
                        @elseif($rak->status == 'tersedia')
                        <span class="status-badge status-available flex items-center space-x-1">
                            <i class="fas fa-check text-xs"></i>
                            <span>Tersedia</span>
                        </span>
                        @else
                        <span class="status-badge status-maintenance flex items-center space-x-1">
                            <i class="fas fa-tools text-xs"></i>
                            <span>Maintenance</span>
                        </span>
                        @endif
                    </div>

                    <!-- Type Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="status-badge type-badge flex items-center space-x-1">
                            <i class="fas fa-layer-group text-blue-500"></i>
                            <span>{{ $rak->jenis_rak }}</span>
                        </span>
                    </div>

                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
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
                    </div>

                    <!-- Price Section -->
                    <div class="price-gradient p-4 rounded-xl">
                        <p class="text-green-700 text-sm font-medium mb-1 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Harga Sewa Bulanan
                        </p>
                        <p class="text-green-600 text-2xl font-bold">
                            Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                            <span class="text-sm font-normal text-green-500">/bulan</span>
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
            <div class="flex justify-center">
                {{ $raks->links() }}
            </div>
        </div>
        @endif

        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
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

        // Add hover effect to action buttons
        const actionBtns = document.querySelectorAll('.action-btn');
        actionBtns.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endpush
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
</style>
@endpush

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- TITLE -->
        <div class="mb-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Detail Rak</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Informasi lengkap mengenai rak yang Anda pilih</p>
        </div>

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

            @if ($rak->status === 'tersedia')
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

<!-- WhatsApp Button -->
@include('customer.payment.partials.whatsapp-button')
@endsection
@extends('layouts.app')

@section('title', 'Detail Rak - SPACEGO')

<<<<<<< HEAD
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md shadow-lg sticky top-0 z-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-3 rounded-2xl shadow-lg">
                        <i class="fas fa-warehouse text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">SPACEGO</span>
                        <p class="text-xs text-gray-500 font-medium">Storage Solution</p>
                    </div>
                </div>
                

                <div class="flex items-center space-x-8">
                    <a href="{{ route('customer.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <i class="fas fa-home"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">Home</span>
                    </a>
                    
                    <a href="{{ route('customer.list-rak.list-rak') }}" class="flex flex-col items-center text-blue-600 group transition-all duration-300">
                        <div class="bg-blue-100 p-3 rounded-xl shadow-sm">
                            <i class="fas fa-pallet"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">Rak</span>
                    </a>

                    <a href="{{ route('customer.list-rak.rak') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-xs mt-2 font-medium">Rak Dibeli</span>
                    </a>
                    
                    <a href="{{ route('customer.profile.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">Profile</span>
                    </a>
                    
                    <a href="#" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <i class="fas fa-history"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">History</span>
                    </a>
                    
                    <!-- Dropdown Profile -->
                    <div class="relative group">
                        <button class="flex items-center space-x-3 bg-gray-100 hover:bg-gray-200 rounded-xl px-4 py-2 transition-all duration-300 shadow-sm">
                            <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=32&background=4A90E2&color=fff' }}" 
                                 alt="Profile" 
                                 class="w-8 h-8 rounded-lg object-cover border-2 border-white shadow-sm">
                            <span class="text-sm font-medium text-gray-700 hidden md:block">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=40&background=4A90E2&color=fff' }}" 
                                         alt="Profile" 
                                         class="w-10 h-10 rounded-lg object-cover border-2 border-blue-500 shadow-sm">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <a href="{{ route('customer.profile.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-all duration-300 text-sm font-medium mb-1">
                                    <i class="fas fa-user-edit text-blue-500"></i>
                                    <span>Edit Profile</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-300 text-sm font-medium">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <!-- CONTAINER -->
    <section class="max-w-7xl mx-auto px-6 mt-10 mb-20">
=======
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
</style>
@endpush

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
>>>>>>> f64eca3d0a395b4f2c3d7d83757c577153b9f3c3

        <!-- TITLE -->
        <div class="mb-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Detail Rak</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Informasi lengkap mengenai rak yang Anda pilih</p>
        </div>

        <!-- RENTAL INFO (Jika user sudah menyewa) -->
        @php
            $activeRental = null;
            if (Auth::check()) {
                $activeRental = \App\Models\Transaction::where('user_id', Auth::id())
                    ->where('rak_id', $rak->id)
                    ->whereIn('transaction_status', ['settlement', 'capture'])
                    ->where('sewa_berakhir', '>=', now())
                    ->orderBy('sewa_berakhir', 'desc')
                    ->first();
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
                $daysRemaining = \Carbon\Carbon::parse($activeRental->sewa_berakhir)
                                    ->startOfDay()
                                    ->diffInDays(now()->startOfDay(), true);
            @endphp
            
            <div class="mt-4 p-3 bg-white bg-opacity-20 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="font-semibold">Sisa Waktu Sewa:</span>
                    <span class="text-xl font-bold">
                        @if($daysRemaining > 0)
                            {{ $daysRemaining }} Hari
                        @elseif($daysRemaining == 0)
                            Berakhir Hari Ini
                        @else
                            Sudah Berakhir
                        @endif
                    </span>
                </div>
            </div>
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

<<<<<<< HEAD

            @if ($rak->status === 'tersedia')
=======
            @if($activeRental)
                <button class="flex-1 flex items-center justify-center space-x-3 px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl cursor-default font-semibold shadow-lg">
                    <i class="fas fa-check-circle"></i>
                    <span>Rak Sedang Anda Sewa</span>
                </button>
            @elseif ($rak->status === 'tersedia')
>>>>>>> f64eca3d0a395b4f2c3d7d83757c577153b9f3c3
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
<<<<<<< HEAD

           @if($rak->status != 'terisi')
            <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                class="block bg-blue-600 hover:bg-blue-700 text-white font-semibold text-center py-3 rounded-lg transition">
                Bayar Sekarang
            </a>
        @else
            <div class="bg-green-100 text-green-700 text-center py-3 rounded-lg font-semibold">
                Sudah Dibeli
            </div>
        @endif


=======
>>>>>>> f64eca3d0a395b4f2c3d7d83757c577153b9f3c3
        </div>

    </div>
</div>

<!-- WhatsApp Button -->
@include('customer.payment.partials.whatsapp-button')
@endsection
@extends('layouts.app')

@section('title', 'SPACEGO - Daftar Rak')

@push('styles')
<style>
    .rak-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .rak-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
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
    
    .filter-card {
        background: white;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .search-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .filter-button {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        transition: all 0.3s ease;
    }
    
    .filter-button:hover {
        background: linear-gradient(135deg, #1d4ed8, #7c3aed);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
    }
    
    .image-hover {
        transition: transform 0.5s ease;
    }
    
    .rak-card:hover .image-hover {
        transform: scale(1.1);
    }
    
    .action-button {
        transition: all 0.2s ease;
    }
    
    .action-button:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- HEADER SECTION -->
        <section class="mb-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Daftar Rak Tersedia</h1>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Pilih rak penyimpanan yang sesuai dengan kebutuhan bisnis Anda</p>
            </div>
        </section>

        <!-- FILTER DAN SEARCH -->
        <section class="mb-8">
            @include('customer.list-rak.partials.filter-section')
        </section>

        <!-- LIST RAK -->
        <section class="mb-16">
            @if($raks->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($raks as $rak)
                        @include('customer.list-rak.partials.rak-card')
                    @endforeach
                </div>
                
                <!-- PAGINATION - FIXED VERSION -->
                @if($raks->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        {{ $raks->links() }}
                    </div>
                </div>
<<<<<<< HEAD
            </div>
        </div>
    </nav>

    <!-- HEADER SECTION -->
    <section class="max-w-7xl mx-auto px-6 mt-12">
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold text-gray-800 mb-4">Daftar Rak Tersedia</h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Pilih rak penyimpanan yang sesuai dengan kebutuhan bisnis Anda</p>
        </div>
    </section>

    <!-- FILTER DAN SEARCH -->
    <section class="max-w-7xl mx-auto px-6 mt-8">
        <form method="GET" class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
            <div class="flex flex-col md:flex-row gap-6">
                
                <!-- SEARCH -->
                <div class="relative flex-1">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama rak atau kode rak..."
                           class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 text-lg">
                </div>

                <!-- FILTER JENIS -->
                <div class="relative md:w-64">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                        <i class="fas fa-filter text-gray-400"></i>
                    </div>
                    <select name="jenis" 
                            class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 appearance-none bg-white text-lg">
                        <option value="">Semua Jenis Rak</option>
                        @foreach ($jenisList as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis') == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>

                <!-- BUTTON SUBMIT -->
                <button class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 shadow-md font-medium flex items-center justify-center space-x-3 text-lg">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filter</span>
                </button>
            </div>
        </form>
    </section>

    <!-- LIST RAK -->
    <section class="max-w-7xl mx-auto px-6 mt-10 mb-16">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            @foreach ($raks as $rak)
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group border border-gray-100 transform hover:-translate-y-2">

                <!-- FOTO -->
                <div class="relative w-full h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                    @if ($rak->foto)
                        <img src="{{ asset('storage/' . $rak->foto) }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                             alt="Foto Rak">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100">
                            <i class="fas fa-pallet text-4xl text-blue-500 opacity-50"></i>
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">

                        @if ($rak->status === 'tersedia')
                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                ✓ Tersedia
                            </span>
                        @elseif($rak->status === 'terisi')
                            <span class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                ✕ Terisi
                            </span>
                        @else

                            <span class="px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-full shadow-lg flex items-center space-x-1">
                                <i class="fas fa-tools text-xs"></i>
                                <span>Maintenance</span>

                      @if($rak->status != 'terisi')
                            <a href="{{ route('customer.list-rak.show', $rak->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Lihat Detail
                            </a>
                        @else
                            <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg">
                            Sudah Dibeli


                            <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full shadow-lg">
                              
                        @endif


                    </div>

                    <!-- Jenis Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-semibold rounded-full shadow">
                            {{ $rak->jenis_rak }}
                        </span>
                    </div>
                </div>

                <!-- CONTENT -->
                <div class="p-6">
                    <!-- KODE -->
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Kode Rak</p>
                        <h3 class="text-2xl font-bold text-blue-600">{{ $rak->kode_rak }}</h3>
                    </div>

                    <!-- INFO -->
                    <div class="space-y-4 text-sm mb-4">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-tag text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Nama Rak</p>
                                <p class="text-gray-800 font-semibold">{{ $rak->nama_rak }}</p>
=======
                @endif
            @else
                <!-- EMPTY STATE -->
                <div class="text-center py-16">
                    <div class="bg-white rounded-2xl shadow-lg p-12 max-w-2xl mx-auto border border-gray-100">
                        <div class="flex justify-center mb-6">
                            <div class="bg-blue-100 p-6 rounded-full">
                                <i class="fas fa-pallet text-blue-600 text-4xl"></i>
>>>>>>> f64eca3d0a395b4f2c3d7d83757c577153b9f3c3
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Tidak Ada Rak Ditemukan</h3>
                        <p class="text-gray-600 mb-6">Coba ubah filter pencarian Anda atau hubungi kami untuk informasi lebih lanjut.</p>
                        <a href="{{ route('customer.list-rak.list-rak') }}" 
                        class="inline-flex items-center space-x-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                            <i class="fas fa-refresh"></i>
                            <span>Reset Pencarian</span>
                        </a>
<<<<<<< HEAD

                        @if ($rak->status === 'tersedia')

                            <a href="{{ route('customer.bayar', $rak->id) }}"
                               class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-center text-white py-3 rounded-xl hover:shadow-lg transition-all duration-300 shadow-md font-medium flex items-center justify-center space-x-2">
                                <i class="fas fa-shopping-cart text-sm"></i>
                                <span>Sewa</span>
                            </a>

                           <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                                class="block mt-5 bg-blue-600 text-center text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                    Bayar / Sewa
                                </a>

                                

                            <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                               class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-center text-white py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-md hover:shadow-lg font-medium">
                                Sewa Sekarang
                            </a>
                        @else
                            <button class="flex-1 bg-gray-300 text-gray-500 py-3 rounded-xl cursor-not-allowed font-medium flex items-center justify-center space-x-2">
                                <i class="fas fa-ban text-sm"></i>
                                <span>Tidak Tersedia</span>
                            </button>
                        @endif
=======
>>>>>>> f64eca3d0a395b4f2c3d7d83757c577153b9f3c3
                    </div>
                </div>
            @endif
</section>
    </div>
</div>

<!-- WhatsApp Button -->
@include('customer.payment.partials.whatsapp-button')
@endsection
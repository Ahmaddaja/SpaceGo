<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rak - SPACEGO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-50 min-h-screen">

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
                
                <div class="hidden md:block text-gray-600 text-sm border-l pl-4">
                    Selamat datang, <span class="font-semibold text-blue-600">{{ Auth::user()->name }}</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-6">
                <a href="{{ route('customer.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-xs mt-1 hidden md:block">Home</span>
                </a>
                
                <a href="{{ route('customer.list-rak.list-rak') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-xs mt-1 hidden md:block">Rak</span>
                </a>
                 <a href="{{ route('customer.list-rak.rak') }}" 
                    class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-xs mt-1 hidden md:block">Rak Dibeli</span>
                </a>
                
                <a href="{{ route('customer.profile.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-xs mt-1 hidden md:block">Profile</span>
                </a>
                
                <a href="#" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-xs mt-1 hidden md:block">History</span>
                </a>
                
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- CONTAINER -->
    <section class="max-w-7xl mx-auto px-6 mt-10 mb-20">

        <!-- TITLE -->
        <div class="mb-8 text-center">
            <h2 class="text-5xl font-bold text-gray-800 mb-4">Detail Rak</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Informasi lengkap mengenai rak yang Anda pilih</p>
        </div>

        <!-- MAIN CARD -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <div class="grid lg:grid-cols-2 gap-8 p-8">

                <!-- FOTO -->
                <div class="relative">
                    <div class="rounded-2xl overflow-hidden shadow-lg h-96 bg-gradient-to-br from-gray-100 to-gray-200">
                        @if ($rak->foto)
                            <img src="{{ asset('storage/' . $rak->foto) }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                                 alt="Foto Rak">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-pallet text-8xl text-blue-500 opacity-30"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Status Badge on Image -->
                    <div class="absolute top-4 right-4">
                        @if ($rak->status === 'tersedia')
                            <span class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-full shadow-lg flex items-center space-x-2">
                                <i class="fas fa-check text-xs"></i>
                                <span>Tersedia</span>
                            </span>
                        @elseif($rak->status === 'terisi')
                            <span class="px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-full shadow-lg flex items-center space-x-2">
                                <i class="fas fa-times text-xs"></i>
                                <span>Terisi</span>
                            </span>
                        @else
                            <span class="px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-full shadow-lg flex items-center space-x-2">
                                <i class="fas fa-tools text-xs"></i>
                                <span>Maintenance</span>
                            </span>
                        @endif
                    </div>

                    <!-- Jenis Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="px-4 py-2 bg-white/90 backdrop-blur-sm text-gray-700 text-sm font-semibold rounded-full shadow flex items-center space-x-2">
                            <i class="fas fa-layer-group text-blue-500"></i>
                            <span>{{ $rak->jenis_rak }}</span>
                        </span>
                    </div>
                </div>

                <!-- INFO DETAIL -->
                <div class="space-y-6">

                    <!-- Kode -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-2xl border-l-4 border-blue-600">
                        <p class="text-blue-700 text-sm font-medium mb-2 uppercase tracking-wide">Kode Rak</p>
                        <h3 class="text-4xl font-bold text-blue-600">{{ $rak->kode_rak }}</h3>
                    </div>

                    <!-- Nama -->
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-gray-500 text-sm font-medium mb-2 flex items-center">
                            <i class="fas fa-tag text-blue-500 mr-2"></i>
                            Nama Rak
                        </p>
                        <p class="text-gray-800 text-xl font-semibold">{{ $rak->nama_rak }}</p>
                    </div>

                    <!-- Jenis -->
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-gray-500 text-sm font-medium mb-2 flex items-center">
                            <i class="fas fa-layer-group text-green-500 mr-2"></i>
                            Jenis Rak
                        </p>
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold">
                            <i class="fas fa-pallet mr-2"></i>
                            {{ $rak->jenis_rak }}
                        </span>
                    </div>

                    <!-- Status -->
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-gray-500 text-sm font-medium mb-2 flex items-center">
                            <i class="fas fa-info-circle text-purple-500 mr-2"></i>
                            Status Ketersediaan
                        </p>
                        @if ($rak->status === 'tersedia')
                            <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                <i class="fas fa-check mr-2"></i>
                                Tersedia
                            </span>
                        @elseif($rak->status === 'terisi')
                            <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-xl font-semibold">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                <i class="fas fa-times mr-2"></i>
                                Terisi
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl font-semibold">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                                <i class="fas fa-tools mr-2"></i>
                                Maintenance
                            </span>
                        @endif
                    </div>

                    <!-- Harga -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-2xl border-l-4 border-green-500">
                        <p class="text-green-700 text-sm font-medium mb-2 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Harga Sewa Per Bulan
                        </p>
                        <p class="text-green-600 text-3xl font-bold">
                            Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                        </p>
                        <p class="text-green-600 text-sm mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            * Harga sudah termasuk perawatan dasar
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SPESIFIKASI -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mt-8 border border-gray-100">

            <div class="flex items-center space-x-4 mb-6 pb-4 border-b border-gray-200">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl">
                    <i class="fas fa-clipboard-list text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Spesifikasi Teknis</h3>
                    <p class="text-gray-600">Detail lengkap spesifikasi rak</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-2xl border border-blue-200 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-blue-500 p-2 rounded-lg">
                            <i class="fas fa-file-alt text-white text-sm"></i>
                        </div>
                        <p class="text-gray-700 text-sm font-medium">Deskripsi</p>
                    </div>
                    <p class="text-gray-800 font-semibold">{{ $rak->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-2xl border border-green-200 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-green-500 p-2 rounded-lg">
                            <i class="fas fa-weight text-white text-sm"></i>
                        </div>
                        <p class="text-gray-700 text-sm font-medium">Kapasitas Berat</p>
                    </div>
                    <p class="text-gray-800 text-xl font-bold">{{ $rak->kapasitas_berat }} <span class="text-sm font-normal text-gray-600">kg</span></p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-2xl border border-purple-200 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-purple-500 p-2 rounded-lg">
                            <i class="fas fa-ruler-combined text-white text-sm"></i>
                        </div>
                        <p class="text-gray-700 text-sm font-medium">Dimensi (P × L × T)</p>
                    </div>
                    <p class="text-gray-800 font-bold text-lg">
                        {{ $rak->panjang }} × {{ $rak->lebar }} × {{ $rak->tinggi }} <span class="text-sm font-normal text-gray-600">cm</span>
                    </p>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-5 rounded-2xl border border-orange-200 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-orange-500 p-2 rounded-lg">
                            <i class="fas fa-layer-group text-white text-sm"></i>
                        </div>
                        <p class="text-gray-700 text-sm font-medium">Jumlah Tingkat</p>
                    </div>
                    <p class="text-gray-800 text-xl font-bold">{{ $rak->jumlah_tingkat }} <span class="text-sm font-normal text-gray-600">tingkat</span></p>
                </div>

                <div class="bg-gradient-to-br from-red-50 to-red-100 p-5 rounded-2xl border border-red-200 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-red-500 p-2 rounded-lg">
                            <i class="fas fa-map-marker-alt text-white text-sm"></i>
                        </div>
                        <p class="text-gray-700 text-sm font-medium">Lokasi Gudang</p>
                    </div>
                    <p class="text-gray-800 font-semibold">{{ $rak->lokasi_gudang }}</p>
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 rounded-2xl border border-indigo-200 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-indigo-500 p-2 rounded-lg">
                            <i class="fas fa-list text-white text-sm"></i>
                        </div>
                        <p class="text-gray-700 text-sm font-medium">Spesifikasi Tambahan</p>
                    </div>
                    <p class="text-gray-800 font-semibold">{{ $rak->spesifikasi_tambahan ?? 'Tidak ada' }}</p>
                </div>

                <div class="bg-gradient-to-br from-teal-50 to-teal-100 p-5 rounded-2xl border border-teal-200 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-teal-500 p-2 rounded-lg">
                            <i class="fas fa-check-circle text-white text-sm"></i>
                        </div>
                        <p class="text-gray-700 text-sm font-medium">Status Aktif</p>
                    </div>
                    <p class="text-gray-800 font-bold text-lg">
                        @if($rak->is_active)
                            <span class="text-green-600 flex items-center">
                                <i class="fas fa-check mr-2"></i>Aktif
                            </span>
                        @else
                            <span class="text-red-600 flex items-center">
                                <i class="fas fa-times mr-2"></i>Tidak Aktif
                            </span>
                        @endif
                    </p>
                </div>

            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4">

            <a href="{{ route('customer.list-rak.list-rak') }}"
               class="flex items-center justify-center space-x-3 px-8 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-300 font-semibold shadow-md hover:shadow-lg group">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                <span>Kembali ke Daftar Rak</span>
            </a>

            @if ($rak->status === 'tersedia')
                <a href="{{ route('customer.bayar', $rak->id) }}"
                   class="flex-1 flex items-center justify-center space-x-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-semibold shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Sewa Sekarang</span>
                </a>
            @else
                <button class="flex-1 flex items-center justify-center space-x-2 px-8 py-4 bg-gray-300 text-gray-500 rounded-xl cursor-not-allowed font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                    <span>Tidak Tersedia</span>
                </button>
            @endif

        </div>

    </section>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/6281234567890" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition-all duration-300 hover:scale-110 z-50">
        <i class="fab fa-whatsapp text-xl"></i>
    </a>

</body>
</html>
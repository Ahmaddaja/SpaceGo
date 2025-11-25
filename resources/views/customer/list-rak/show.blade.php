<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rak - SPACEGO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-2xl font-bold text-gray-800">SPACEGO</span>
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
        <div class="mb-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Detail Rak</h2>
            <p class="text-gray-600">Informasi lengkap mengenai rak yang Anda pilih</p>
        </div>

        <!-- MAIN CARD -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            
            <div class="grid md:grid-cols-2 gap-8 p-8">

                <!-- FOTO -->
                <div class="relative">
                    <div class="rounded-2xl overflow-hidden shadow-lg h-96">
                        @if ($rak->foto)
                            <img src="{{ asset('storage/' . $rak->foto) }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                                 alt="Foto Rak">
                        @else
                            <img src="https://via.placeholder.com/600x500?text=Foto+Rak" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        @endif
                    </div>
                    
                    <!-- Status Badge on Image -->
                    <div class="absolute top-4 right-4">
                        @if ($rak->status === 'tersedia')
                            <span class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-full shadow-lg flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Tersedia</span>
                            </span>
                        @elseif($rak->status === 'terisi')
                            <span class="px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-full shadow-lg flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Terisi</span>
                            </span>
                        @else
                            <span class="px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-full shadow-lg flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Maintenance</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- INFO DETAIL -->
                <div class="space-y-6">

                    <!-- Kode -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-5 rounded-xl border-l-4 border-blue-600">
                        <p class="text-blue-700 text-sm font-medium mb-1 uppercase tracking-wide">Kode Rak</p>
                        <h3 class="text-4xl font-bold text-blue-600">{{ $rak->kode_rak }}</h3>
                    </div>

                    <!-- Nama -->
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-gray-500 text-sm font-medium mb-2">Nama Rak</p>
                        <p class="text-gray-800 text-xl font-semibold">{{ $rak->nama_rak }}</p>
                    </div>

                    <!-- Jenis -->
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-gray-500 text-sm font-medium mb-2">Jenis Rak</p>
                        <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            {{ $rak->jenis_rak }}
                        </span>
                    </div>

                    <!-- Status -->
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-gray-500 text-sm font-medium mb-2">Status Ketersediaan</p>
                        @if ($rak->status === 'tersedia')
                            <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg font-semibold">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Tersedia
                            </span>
                        @elseif($rak->status === 'terisi')
                            <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg font-semibold">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                Terisi
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg font-semibold">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                                Maintenance
                            </span>
                        @endif
                    </div>

                    <!-- Harga -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-5 rounded-xl border-l-4 border-green-500">
                        <p class="text-green-700 text-sm font-medium mb-1">Harga Sewa Per Bulan</p>
                        <p class="text-green-600 text-3xl font-bold">
                            Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                        </p>
                        <p class="text-green-600 text-sm mt-1">* Harga sudah termasuk perawatan dasar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SPESIFIKASI -->
        <div class="bg-white shadow-xl rounded-2xl p-8 mt-8 border border-gray-100">

            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-gray-200">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Spesifikasi Teknis</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-6">

                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Deskripsi</p>
                    </div>
                    <p class="text-gray-800 font-semibold">{{ $rak->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                </div>

                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Kapasitas Berat</p>
                    </div>
                    <p class="text-gray-800 text-xl font-bold">{{ $rak->kapasitas_berat }} <span class="text-sm font-normal text-gray-600">kg</span></p>
                </div>

                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Dimensi (P × L × T)</p>
                    </div>
                    <p class="text-gray-800 font-bold">
                        {{ $rak->panjang }} × {{ $rak->lebar }} × {{ $rak->tinggi }} <span class="text-sm font-normal text-gray-600">cm</span>
                    </p>
                </div>

                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Jumlah Tingkat</p>
                    </div>
                    <p class="text-gray-800 text-xl font-bold">{{ $rak->jumlah_tingkat }} <span class="text-sm font-normal text-gray-600">tingkat</span></p>
                </div>

                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Lokasi Gudang</p>
                    </div>
                    <p class="text-gray-800 font-semibold">{{ $rak->lokasi_gudang }}</p>
                </div>

                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Spesifikasi Tambahan</p>
                    </div>
                    <p class="text-gray-800 font-semibold">{{ $rak->spesifikasi_tambahan ?? 'Tidak ada' }}</p>
                </div>

                <div class="bg-gray-50 p-5 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Status Aktif</p>
                    </div>
                    <p class="text-gray-800 font-bold">
                        @if($rak->is_active)
                            <span class="text-green-600">✓ Aktif</span>
                        @else
                            <span class="text-red-600">✕ Tidak Aktif</span>
                        @endif
                    </p>
                </div>

            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4">

            <a href="{{ route('customer.list-rak.list-rak') }}"
               class="flex items-center justify-center space-x-2 px-8 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-semibold shadow-md hover:shadow-lg group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali</span>
            </a>

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

        </div>

    </section>

</body>
</html>
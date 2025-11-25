<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPACEGO - Daftar Rak</title>
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

    <!-- HEADER SECTION -->
    <section class="max-w-7xl mx-auto px-6 mt-12">
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold text-gray-800 mb-3">Daftar Rak Tersedia</h1>
            <p class="text-gray-600 text-lg">Pilih rak penyimpanan yang sesuai dengan kebutuhan Anda</p>
        </div>
    </section>

    <!-- FILTER DAN SEARCH -->
    <section class="max-w-7xl mx-auto px-6 mt-8">
        <form method="GET" class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
            <div class="flex flex-col md:flex-row gap-4">
                
                <!-- SEARCH -->
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama rak atau kode rak..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <!-- FILTER JENIS -->
                <div class="relative md:w-64">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <select name="jenis" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none bg-white">
                        <option value="">Semua Jenis Rak</option>
                        @foreach ($jenisList as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis') == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- BUTTON SUBMIT -->
                <button class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-md hover:shadow-lg font-medium flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span>Filter</span>
                </button>
            </div>
        </form>
    </section>

    <!-- LIST RAK -->
    <section class="max-w-7xl mx-auto px-6 mt-10 mb-16">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            @foreach ($raks as $rak)
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group border border-gray-100">

                <!-- FOTO -->
                <div class="relative w-full h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                    @if ($rak->foto)
                        <img src="{{ asset('storage/' . $rak->foto) }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                             alt="Foto Rak">
                    @else
                        <img src="https://via.placeholder.com/400x300?text=Foto+Rak" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                             alt="Foto Default">
                    @endif
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                      @if($rak->status != 'terisi')
                            <a href="{{ route('customer.list-rak.show', $rak->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Lihat Detail
                            </a>
                        @else
                            <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg">
                            Sudah Dibeli
                            </span>
                        @endif


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
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <div>
                                <p class="text-gray-500 text-xs">Nama Rak</p>
                                <p class="text-gray-800 font-semibold">{{ $rak->nama_rak }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <div>
                                <p class="text-gray-500 text-xs">Jenis Rak</p>
                                <p class="text-gray-800 font-semibold">{{ $rak->jenis_rak }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="text-gray-500 text-xs">Lokasi Gudang</p>
                                <p class="text-gray-800 font-semibold">{{ $rak->gudang->alamat }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- HARGA -->
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mb-1">Harga Sewa</p>
                        <p class="text-2xl font-bold text-green-600">
                            Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                            <span class="text-sm text-gray-500 font-normal">/ bulan</span>
                        </p>
                    </div>

                    <!-- BUTTONS -->
                    <div class="mt-5 flex gap-3">
                        <a href="{{ route('customer.list-rak.show', $rak->id) }}"
                           class="flex-1 bg-gray-100 text-center text-gray-700 py-3 rounded-xl hover:bg-gray-200 transition font-medium">
                            Detail
                        </a>

                        @if ($rak->status === 'tersedia')
                           <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                                class="block mt-5 bg-blue-600 text-center text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                    Bayar / Sewa
                                </a>

                        @else
                            <button class="flex-1 bg-gray-300 text-gray-500 py-3 rounded-xl cursor-not-allowed font-medium">
                                Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>

            </div>
            @endforeach

        </div>
    </section>

</body>
</html>
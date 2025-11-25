<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPACEGO - Daftar Rak</title>
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
                            <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                ⚙ Maintenance
                            </span>
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
                            </div>
                        </div>

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
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('customer.list-rak.show', $rak->id) }}"
                           class="flex-1 bg-gray-100 text-center text-gray-700 py-3 rounded-xl hover:bg-gray-200 transition-all duration-300 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-eye text-sm"></i>
                            <span>Detail</span>
                        </a>

                        @if ($rak->status === 'tersedia')
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
                    </div>
                </div>

            </div>
            @endforeach

        </div>
    </section>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/6281234567890" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition-all duration-300 hover:scale-110 z-50">
        <i class="fab fa-whatsapp text-xl"></i>
    </a>

</body>
</html>
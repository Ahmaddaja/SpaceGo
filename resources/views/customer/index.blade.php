<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPACEGO - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50">

    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md shadow-lg sticky top-0 z-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-3 rounded-2xl shadow-lg">
                        <!-- Logo yang telah diganti -->
                        <svg class="w-6 h-6 text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">SPACEGO</span>
                        <p class="text-xs text-gray-500 font-medium">Storage Solution</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-8">
                    <a href="{{ route('customer.index') }}" class="flex flex-col items-center text-blue-600 group transition-all duration-300">
                        <div class="bg-blue-100 p-3 rounded-xl shadow-sm">
                            <i class="fas fa-home"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">Home</span>
                    </a>
                    
                    <a href="{{ route('customer.list-rak.list-rak') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
                            <i class="fas fa-pallet"></i>
                        </div>
                        <span class="text-xs mt-2 font-medium">Rak</span>
                    </a>
                     <!-- Rak Anda -->
<a href="{{ route('customer.list-rak.rak') }}" class="flex flex-col items-center group transition-all duration-300 {{ request()->routeIs('customer.list-rak.rak') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
    <div class="p-3 rounded-xl shadow-sm transition-all duration-300 transform {{ request()->routeIs('customer.list-rak.rak') ? 'bg-blue-100 shadow-md scale-110' : 'bg-gray-100 group-hover:bg-blue-100 group-hover:shadow-md group-hover:scale-110' }}">
        <i class="fas fa-th-large w-5 h-5"></i>
    </div>
    <span class="text-xs mt-2 font-medium">Rak Anda</span>
    @if(request()->routeIs('customer.list-rak.rak'))
    <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
    @endif
</a>
                    
                    <a href="{{ route('customer.history') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
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
                        <div class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=40&background=4A90E2&color=fff' }}" 
                                         alt="Profile" 
                                         class="w-10 h-10 rounded-lg object-cover border-2 border-blue-500 shadow-sm">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
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

    <!-- Hero -->
    <section class="py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="space-y-6">
                        <h1 class="text-5xl font-bold text-gray-800 leading-tight">
                            Selamat Datang, <span class="text-blue-600">{{ Auth::user()->name }}</span>!
                        </h1>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Solusi penyimpanan yang aman, fleksibel, dan terjangkau. Kelola inventori Anda dengan mudah bersama SPACEGO.
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('customer.list-rak.list-rak') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:shadow-lg transition-all duration-300 shadow-md">
                            Lihat Rak Tersedia <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    <div class="flex items-center space-x-6 pt-4">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span class="text-sm text-gray-600">Aman & Terpercaya</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock text-blue-500"></i>
                            <span class="text-sm text-gray-600">24/7 Support</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-white rounded-2xl p-4 shadow-xl">
                        <svg viewBox="0 0 500 400" class="w-full">
                            <!-- Warehouse Building -->
                            <rect x="50" y="150" width="400" height="200" fill="#5B6FDE" opacity="0.9"/>
                            <rect x="50" y="100" width="400" height="50" fill="#4A5ACC"/>
                            
                            <!-- Roof -->
                            <polygon points="50,100 250,50 450,100" fill="#3D4AB8"/>
                            
                            <!-- Windows -->
                            <rect x="80" y="180" width="60" height="60" fill="#7B8AEE"/>
                            <rect x="160" y="180" width="60" height="60" fill="#7B8AEE"/>
                            <rect x="240" y="180" width="60" height="60" fill="#7B8AEE"/>
                            <rect x="320" y="180" width="60" height="60" fill="#7B8AEE"/>
                            
                            <!-- Door -->
                            <rect x="180" y="260" width="140" height="90" fill="#3D4AB8"/>
                            
                            <!-- Truck -->
                            <rect x="360" y="310" width="100" height="40" fill="#FF6B6B"/>
                            <circle cx="380" cy="355" r="12" fill="#2C3E50"/>
                            <circle cx="440" cy="355" r="12" fill="#2C3E50"/>
                            <rect x="430" y="290" width="30" height="20" fill="#E74C3C"/>
                            
                            <!-- Forklift -->
                            <rect x="100" y="320" width="40" height="30" fill="#FFA500"/>
                            <circle cx="110" cy="355" r="8" fill="#2C3E50"/>
                            <circle cx="130" cy="355" r="8" fill="#2C3E50"/>
                            <rect x="120" y="300" width="8" height="30" fill="#FF8C00"/>
                            
                            <!-- Boxes -->
                            <rect x="280" y="300" width="30" height="30" fill="#E67E22"/>
                            <rect x="315" y="300" width="30" height="30" fill="#D35400"/>
                            <rect x="297" y="270" width="30" height="30" fill="#F39C12"/>
                            
                            <!-- Cloud 1 -->
                            <ellipse cx="100" cy="60" rx="30" ry="20" fill="#BDC3FF" opacity="0.6"/>
                            <ellipse cx="125" cy="55" rx="25" ry="18" fill="#BDC3FF" opacity="0.6"/>
                            
                            <!-- Cloud 2 -->
                            <ellipse cx="380" cy="70" rx="35" ry="22" fill="#BDC3FF" opacity="0.6"/>
                            <ellipse cx="410" cy="65" rx="28" ry="20" fill="#BDC3FF" opacity="0.6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-12 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-sm border border-blue-200">
                    <div class="text-3xl font-bold text-blue-600 mb-2">500+</div>
                    <div class="text-gray-700 font-medium">Klien Aktif</div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow-sm border border-green-200">
                    <div class="text-3xl font-bold text-green-600 mb-2">100+</div>
                    <div class="text-gray-700 font-medium">Rak Tersedia</div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 shadow-sm border border-purple-200">
                    <div class="text-3xl font-bold text-purple-600 mb-2">2,500 m²</div>
                    <div class="text-gray-700 font-medium">Total Area</div>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 shadow-sm border border-orange-200">
                    <div class="text-3xl font-bold text-orange-600 mb-2">24/7</div>
                    <div class="text-gray-700 font-medium">Akses</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Mengapa Memilih SPACEGO?</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kami menyediakan solusi penyimpanan rak yang terbaik untuk kebutuhan bisnis Anda
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-3">Keamanan Terjamin</h3>
                    <p class="text-gray-600 text-center">
                        Sistem keamanan 24/7 dengan CCTV dan petugas jaga untuk menjaga rak penyimpanan Anda.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tags text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-3">Harga Terjangkau</h3>
                    <p class="text-gray-600 text-center">
                        Harga kompetitif dengan berbagai pilihan rak yang dapat disesuaikan dengan budget.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-3">Lokasi Strategis</h3>
                    <p class="text-gray-600 text-center">
                        Lokasi rak yang strategis dan mudah diakses untuk kenyamanan Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="py-16 px-6 bg-gradient-to-r from-blue-600 to-purple-700">
        <div class="max-w-7xl mx-auto text-center text-white">
            <h2 class="text-4xl font-bold mb-4">Butuh Bantuan?</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Tim support kami siap membantu Anda 24/7 untuk semua kebutuhan penyimpanan rak
            </p>
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <i class="fas fa-phone text-2xl mb-3"></i>
                    <div class="font-semibold text-lg mb-2">Telepon</div>
                    <div class="text-blue-100">(021) 1234-5678</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <i class="fas fa-envelope text-2xl mb-3"></i>
                    <div class="font-semibold text-lg mb-2">Email</div>
                    <div class="text-blue-100">support@spacego.id</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                    <i class="fas fa-map-marker-alt text-2xl mb-3"></i>
                    <div class="font-semibold text-lg mb-2">Alamat</div>
                    <div class="text-blue-100">Jakarta Utara</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="flex items-center justify-center space-x-3 mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-2 rounded-lg">
                    <!-- Logo di footer juga diganti -->
                    <svg class="w-5 h-5 text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold">SPACEGO</span>
            </div>
            <p class="text-gray-400 mb-6">
                Solusi penyimpanan rak modern untuk bisnis Anda
            </p>
            <div class="border-t border-gray-700 pt-6 text-gray-400">
                <p>&copy; 2024 SPACEGO. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/6281234567890" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition-all duration-300 hover:scale-110 z-50">
        <i class="fab fa-whatsapp text-xl"></i>
    </a>

</body>
</html>
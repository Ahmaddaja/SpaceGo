<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar Rak - SPACEGO</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
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
                    
                    <a href="{{ route('customer.list-rak.list-rak') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 group transition-all duration-300">
                        <div class="bg-gray-100 p-3 rounded-xl shadow-sm group-hover:bg-blue-100">
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

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-12">
        <!-- Back Button -->
        <a href="{{ route('customer.list-rak.list-rak') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-8 transition-all duration-300 group">
            <i class="fas fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform"></i>
            Kembali ke Daftar Rak
        </a>

        <!-- Payment Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Header with Gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 text-white">
                <div class="flex items-center mb-4">
                    <div class="bg-white/20 p-4 rounded-2xl mr-6 backdrop-blur-sm">
                        <i class="fas fa-credit-card text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Pembayaran Sewa Rak</h1>
                        <p class="text-blue-100 mt-2 text-lg">Selesaikan pembayaran Anda dengan mudah dan aman</p>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="p-8">
                <!-- Rack Info -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 mb-8 border border-blue-100 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 rounded-xl shadow-md">
                                <i class="fas fa-pallet text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-medium mb-1">Nama Rak</p>
                                <h2 class="text-2xl font-bold text-gray-800">{{ $rak->nama_rak }}</h2>
                                <p class="text-gray-600 mt-2 flex items-center">
                                    <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                                    Lokasi: {{ $rak->lokasi ?? 'Gudang Utama' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-receipt text-blue-600 mr-3"></i>
                        Rincian Pembayaran
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Sewa per Bulan
                            </span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($rak->harga_sewa_perbulan,0,',','.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <i class="fas fa-cog text-green-500 mr-2"></i>
                                Biaya Admin
                            </span>
                            <span class="font-semibold text-green-600">Gratis</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4">
                            <span class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-tag text-purple-600 mr-2"></i>
                                Total Pembayaran
                            </span>
                            <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($rak->harga_sewa_perbulan,0,',','.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 text-lg mr-4 mt-1"></i>
                        <div>
                            <p class="font-semibold text-blue-900 mb-2 text-lg">Metode Pembayaran Tersedia</p>
                            <p class="text-blue-700">Transfer Bank, Kartu Kredit, E-Wallet, Virtual Account, dan lainnya melalui Midtrans</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Button -->
                <button id="pay-button" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center group">
                    <i class="fas fa-lock mr-3 group-hover:scale-110 transition-transform"></i>
                    Bayar Sekarang
                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                </button>

                <!-- Security Info -->
                <div class="mt-6 flex items-center justify-center text-sm text-gray-500">
                    <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                    Pembayaran Anda aman dan terenkripsi
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-12 grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bolt text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2 text-lg">Proses Cepat</h3>
                <p class="text-gray-600">Pembayaran diproses dalam hitungan detik</p>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center">
                <div class="bg-gradient-to-br from-green-500 to-green-600 w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2 text-lg">Aman & Terpercaya</h3>
                <p class="text-gray-600">Dilindungi sistem keamanan tingkat tinggi</p>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2 text-lg">Dukungan 24/7</h3>
                <p class="text-gray-600">Tim support siap membantu Anda</p>
            </div>
        </div>
    </div>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/6281234567890" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition-all duration-300 hover:scale-110 z-50">
        <i class="fab fa-whatsapp text-xl"></i>
    </a>

    <script>
        const payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            // Add loading state
            payButton.disabled = true;
            payButton.innerHTML = `
                <i class="fas fa-spinner animate-spin mr-3"></i>
                Memproses Pembayaran...
            `;
            
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    alert("✅ Pembayaran berhasil! Terima kasih telah melakukan pembayaran.");
                    window.location.href = "{{ route('customer.list-rak.rak') }}";
                },
                onPending: function(result){
                    alert("⏳ Pembayaran pending! Mohon selesaikan pembayaran Anda.");
                    payButton.disabled = false;
                    payButton.innerHTML = `
                        <i class="fas fa-lock mr-3"></i>
                        Bayar Sekarang
                        <i class="fas fa-arrow-right ml-3"></i>
                    `;
                },
                onError: function(result){
                    alert("❌ Pembayaran gagal! Silakan coba lagi.");
                    payButton.disabled = false;
                    payButton.innerHTML = `
                        <i class="fas fa-lock mr-3"></i>
                        Bayar Sekarang
                        <i class="fas fa-arrow-right ml-3"></i>
                    `;
                },
                onClose: function(){
                    alert("ℹ️ Anda menutup popup pembayaran tanpa menyelesaikan transaksi.");
                    payButton.disabled = false;
                    payButton.innerHTML = `
                        <i class="fas fa-lock mr-3"></i>
                        Bayar Sekarang
                        <i class="fas fa-arrow-right ml-3"></i>
                    `;
                }
            });
        });
    </script>
</body>
</html>
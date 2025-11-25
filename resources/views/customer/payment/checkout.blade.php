<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar Rak - SPACEGO</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
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
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition shadow-md">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-12">
        <!-- Back Button -->
        <a href="{{ route('customer.list-rak.list-rak') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Rak
        </a>

        <!-- Payment Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header with Gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 text-white">
                <div class="flex items-center mb-4">
                    <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <div>
                        <h1 class="text-3xl font-bold">Pembayaran Sewa Rak</h1>
                        <p class="text-blue-100 mt-1">Selesaikan pembayaran Anda dengan mudah dan aman</p>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="p-8">
                <!-- Rack Info -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 border border-blue-100">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-600 text-white p-3 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Nama Rak</p>
                                <h2 class="text-2xl font-bold text-gray-800">{{ $rak->nama_rak }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Rincian Pembayaran
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Sewa per Bulan</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($rak->harga_sewa_perbulan,0,',','.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Biaya Admin</span>
                            <span class="font-semibold text-green-600">Gratis</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-3">
                            <span class="text-lg font-bold text-gray-800">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($rak->harga_sewa_perbulan,0,',','.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm">
                            <p class="font-semibold text-blue-900 mb-1">Metode Pembayaran Tersedia</p>
                            <p class="text-blue-700">Transfer Bank, Kartu Kredit, E-Wallet, Virtual Account, dan lainnya melalui Midtrans</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Button -->
                <button id="pay-button" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-700 transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center group">
                    <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Bayar Sekarang
                </button>

                <!-- Security Info -->
                <div class="mt-6 flex items-center justify-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Pembayaran Anda aman dan terenkripsi
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 grid md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-md text-center">
                <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Proses Cepat</h3>
                <p class="text-sm text-gray-600">Pembayaran diproses dalam hitungan detik</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md text-center">
                <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Aman & Terpercaya</h3>
                <p class="text-sm text-gray-600">Dilindungi sistem keamanan tingkat tinggi</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md text-center">
                <div class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Dukungan 24/7</h3>
                <p class="text-sm text-gray-600">Tim support siap membantu Anda</p>
            </div>
        </div>
    </div>

    <script>
        const payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            // Add loading state
            payButton.disabled = true;
            payButton.innerHTML = `
                <svg class="animate-spin h-5 w-5 mr-3 inline" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
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
                        <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Bayar Sekarang
                    `;
                },
                onError: function(result){
                    alert("❌ Pembayaran gagal! Silakan coba lagi.");
                    payButton.disabled = false;
                    payButton.innerHTML = `
                        <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Bayar Sekarang
                    `;
                },
                onClose: function(){
                    alert("ℹ️ Anda menutup popup pembayaran tanpa menyelesaikan transaksi.");
                    payButton.disabled = false;
                    payButton.innerHTML = `
                        <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Bayar Sekarang
                    `;
                }
            });
        });
    </script>
</body>
</html>
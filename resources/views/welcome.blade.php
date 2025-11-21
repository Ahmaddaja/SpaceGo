<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SPACEGO - Solusi penyimpanan gudang terpercaya untuk bisnis Anda">
    <title>SPACEGO - Sewa Gudang & Penyimpanan Barang Terpercaya</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .star-filled { fill: currentColor; }
    </style>
</head>
<body class="bg-slate-900" x-data="{ mobileMenuOpen: false }">

    <!-- Navigation -->
    <nav class="fixed w-full bg-slate-900/95 backdrop-blur-sm z-50 border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-2xl font-bold text-white">SPACE<span class="text-blue-500">GO</span></span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#kontak" class="text-gray-300 hover:text-white transition">Kontak</a>
                    <a href="{{ route('login') }}" 
                    class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg block text-center">
                        Mulai Sekarang
                    </a>
                </div>

                <button class="md:hidden text-white" @click="mobileMenuOpen = !mobileMenuOpen">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-slate-800 border-t border-slate-700">
            <div class="px-4 py-4 space-y-3">
                <a href="#kontak" class="block text-gray-300 hover:text-white">Kontak</a>
                <a href="{{ route('login') }}" 
                class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg block text-center">
                    Mulai Sekarang
                </a>

            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                        Solusi Penyimpanan <span class="text-blue-500">Terpercaya</span> untuk Bisnis Anda
                    </h1>
                    <p class="text-xl text-gray-300 mb-8">
                        Sewa gudang dan ruang penyimpanan dengan sistem yang aman, fleksibel, dan terjangkau. Kelola inventori Anda dengan mudah bersama SPACEGO.
                    </p>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold flex items-center justify-center transition">
                        Lihat Gudang Tersedia
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-8 shadow-2xl">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 mb-4">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-white font-semibold">Kapasitas Tersedia</span>
                                <span class="text-blue-300 text-2xl font-bold">2,500 m²</span>
                            </div>
                            <div class="bg-white/20 rounded-full h-3 overflow-hidden">
                                <div class="bg-blue-400 h-full w-3/4"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <svg class="w-8 h-8 text-blue-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <div class="text-2xl font-bold text-white">500+</div>
                                <div class="text-blue-200 text-sm">Klien Aktif</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <svg class="w-8 h-8 text-blue-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <div class="text-2xl font-bold text-white">25+</div>
                                <div class="text-blue-200 text-sm">Lokasi Gudang</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-4 bg-slate-800/50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Mengapa Memilih SPACEGO?</h2>
                <p class="text-gray-400 text-lg">Solusi penyimpanan terbaik dengan berbagai keunggulan</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-8 hover:border-blue-500 transition">
                    <div class="bg-blue-600/20 w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Keamanan Terjamin</h3>
                    <p class="text-gray-400">Sistem keamanan 24/7 dengan CCTV, petugas keamanan, dan asuransi penuh untuk melindungi barang Anda.</p>
                </div>

                <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-8 hover:border-blue-500 transition">
                    <div class="bg-blue-600/20 w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Fleksibel & Mudah</h3>
                    <p class="text-gray-400">Sewa jangka pendek atau panjang dengan akses 24 jam. Tidak ada kontrak yang mengikat.</p>
                </div>

                <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-8 hover:border-blue-500 transition">
                    <div class="bg-blue-600/20 w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Lokasi Strategis</h3>
                    <p class="text-gray-400">Gudang tersebar di berbagai kota besar dengan akses mudah ke jalan tol dan pelabuhan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials - 4 Cards -->
    <section class="py-20 px-4 bg-slate-800/50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Apa Kata Mereka?</h2>
                <p class="text-gray-400 text-lg">Testimoni dari klien yang puas dengan layanan kami</p>
            </div>

            <div class="grid md:grid-cols-4 gap-6">
                <div class="bg-slate-900 border border-slate-700 rounded-xl p-6">
                    <div class="flex mb-4">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-5 h-5 text-yellow-500 star-filled" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-4">"SPACEGO sangat membantu bisnis kami. Gudangnya bersih, aman, dan lokasinya strategis. Highly recommended!"</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">A</div>
                        <div class="ml-3">
                            <div class="text-white font-semibold">Ahmad Rizki</div>
                            <div class="text-gray-400 text-sm">Owner Toko Online</div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl p-6">
                    <div class="flex mb-4">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-5 h-5 text-yellow-500 star-filled" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-4">"Pelayanan ramah dan profesional. Proses sewa sangat mudah dan tidak ribet. Terima kasih SPACEGO!"</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold">S</div>
                        <div class="ml-3">
                            <div class="text-white font-semibold">Siti Nurhaliza</div>
                            <div class="text-gray-400 text-sm">Pengusaha UMKM</div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl p-6">
                    <div class="flex mb-4">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-5 h-5 text-yellow-500 star-filled" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-4">"Sudah 2 tahun pakai SPACEGO dan sangat puas. Harga terjangkau dengan fasilitas lengkap!"</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">B</div>
                        <div class="ml-3">
                            <div class="text-white font-semibold">Budi Santoso</div>
                            <div class="text-gray-400 text-sm">Distributor</div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl p-6">
                    <div class="flex mb-4">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-5 h-5 text-yellow-500 star-filled" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-4">"Fasilitas lengkap dan modern. Tim SPACEGO juga responsif membantu segala kebutuhan kami. Mantap!"</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold">D</div>
                        <div class="ml-3">
                            <div class="text-white font-semibold">Dewi Sartika</div>
                            <div class="text-gray-400 text-sm">Importir</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section - 4 Cards -->
    <section class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Hubungi Kami</h2>
                <p class="text-gray-400 text-lg">Kami siap membantu kebutuhan penyimpanan Anda</p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 text-center hover:border-blue-500 transition">
                    <div class="bg-blue-600/20 w-16 h-16 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Email</h3>
                    <p class="text-gray-400 text-sm">info@spacego.id</p>
                    <p class="text-gray-400 text-sm">support@spacego.id</p>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 text-center hover:border-blue-500 transition">
                    <div class="bg-blue-600/20 w-16 h-16 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Telepon</h3>
                    <p class="text-gray-400 text-sm">(021) 1234-5678</p>
                    <p class="text-gray-400 text-sm">0812-3456-7890</p>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 text-center hover:border-blue-500 transition">
                    <div class="bg-blue-600/20 w-16 h-16 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Alamat</h3>
                    <p class="text-gray-400 text-sm">Jl. Gudang Raya No. 123</p>
                    <p class="text-gray-400 text-sm">Jakarta Utara 14350</p>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 text-center hover:border-blue-500 transition">
                    <div class="bg-blue-600/20 w-16 h-16 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Jam Operasional</h3>
                    <p class="text-gray-400 text-sm">Senin - Jumat: 08.00 - 17.00</p>
                    <p class="text-gray-400 text-sm">Sabtu: 08.00 - 12.00</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 bg-slate-800/50">
        <div class="max-w-4xl mx-auto bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-12 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">Siap Mulai Menyimpan dengan SPACEGO?</h2>
            <p class="text-xl text-blue-100 mb-8">Dapatkan konsultasi gratis dan penawaran khusus untuk pelanggan baru</p>
            <button class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-semibold text-lg transition">Hubungi Kami Sekarang</button>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 px-4" x-data="{ openFaq: null }">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-gray-400 text-lg">Temukan jawaban untuk pertanyaan umum seputar layanan kami</p>
            </div>

            <div class="space-y-4">
                <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-slate-800/50 transition">
                        <span class="text-lg font-semibold text-white">Berapa lama minimal sewa gudang di SPACEGO?</span>
                        <svg class="w-6 h-6 text-blue-500 transition-transform" :class="{ 'rotate-180': openFaq === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 1" x-cloak x-transition class="px-6 pb-5">
                        <p class="text-gray-400">Minimal sewa gudang di SPACEGO adalah 1 bulan. Kami juga menawarkan paket sewa jangka panjang dengan harga lebih ekonomis untuk 6 bulan atau 12 bulan.</p>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-slate-800/50 transition">
                        <span class="text-lg font-semibold text-white">Apakah barang saya diasuransikan?</span>
                        <svg class="w-6 h-6 text-blue-500 transition-transform" :class="{ 'rotate-180': openFaq === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 2" x-cloak x-transition class="px-6 pb-5">
                        <p class="text-gray-400">Ya, untuk paket Business dan Enterprise, barang Anda secara otomatis dilindungi asuransi. Untuk paket Starter, asuransi dapat ditambahkan dengan biaya tambahan.</p>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-slate-800/50 transition">
                        <span class="text-lg font-semibold text-white">Bisakah saya mengakses gudang kapan saja?</span>
                        <svg class="w-6 h-6 text-blue-500 transition-transform" :class="{ 'rotate-180': openFaq === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 3" x-cloak x-transition class="px-6 pb-5">
                        <p class="text-gray-400">Ya, semua paket kami menyediakan akses 24/7. Anda dapat mengakses gudang kapan saja dengan menggunakan kartu akses yang akan diberikan saat registrasi.</p>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-slate-800/50 transition">
                        <span class="text-lg font-semibold text-white">Apa saja yang tidak boleh disimpan di gudang?</span>
                        <svg class="w-6 h-6 text-blue-500 transition-transform" :class="{ 'rotate-180': openFaq === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 4" x-cloak x-transition class="px-6 pb-5">
                        <p class="text-gray-400">Barang yang tidak diperbolehkan: bahan mudah terbakar, bahan kimia berbahaya, senjata api, obat-obatan terlarang, makanan atau minuman yang mudah basi, dan barang ilegal lainnya.</p>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-slate-800/50 transition">
                        <span class="text-lg font-semibold text-white">Bagaimana cara pembayaran sewa gudang?</span>
                        <svg class="w-6 h-6 text-blue-500 transition-transform" :class="{ 'rotate-180': openFaq === 5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 5" x-cloak x-transition class="px-6 pb-5">
                        <p class="text-gray-400">Kami menerima pembayaran melalui transfer bank, virtual account, kartu kredit, dan e-wallet. Pembayaran dilakukan di awal setiap periode sewa.</p>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
                    <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-slate-800/50 transition">
                        <span class="text-lg font-semibold text-white">Apakah ada biaya tambahan selain biaya sewa?</span>
                        <svg class="w-6 h-6 text-blue-500 transition-transform" :class="{ 'rotate-180': openFaq === 6 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 6" x-cloak x-transition class="px-6 pb-5">
                        <p class="text-gray-400">Harga sewa sudah termasuk biaya keamanan, listrik, dan perawatan dasar. Biaya tambahan mungkin berlaku untuk layanan khusus seperti handling barang atau asuransi tambahan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer with Contact Info -->
    <footer id="kontak" class="bg-slate-900 border-t border-slate-700 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="text-xl font-bold text-white">SPACEGO</span>
                    </div>
                    <p class="text-gray-400">Solusi penyimpanan terpercaya untuk bisnis Anda.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Layanan</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Sewa Gudang</a></li>
                        <li><a href="#" class="hover:text-white transition">Penyimpanan Barang</a></li>
                        <li><a href="#" class="hover:text-white transition">Logistik</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Perusahaan</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Karir</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Kontak Kami</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <div>info@spacego.id</div>
                                <div>support@spacego.id</div>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <div>(021) 1234-5678</div>
                                <div>0812-3456-7890</div>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                Jl. Gudang Raya No. 123<br>
                                Jakarta Utara 14350<br>
                                Indonesia
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                Senin - Jumat: 08.00 - 17.00<br>
                                Sabtu: 08.00 - 12.00 WIB
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-700 pt-8 text-center text-gray-400">
                <p>&copy; 2024 SPACEGO. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
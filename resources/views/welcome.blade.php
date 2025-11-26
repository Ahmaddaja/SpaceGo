<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPACEGO - Sewa Rak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <!-- Logo yang telah diganti -->
                <svg class="w-8 h-8 text-blue-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z"/>
                </svg>
                <span class="text-2xl font-bold text-gray-800">SPACEGO</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <a href="#tentang" class="text-gray-600 hover:text-blue-600 transition hidden md:block">Tentang</a>
                <a href="#layanan" class="text-gray-600 hover:text-blue-600 transition hidden md:block">Layanan</a>
                <a href="#kontak" class="text-gray-600 hover:text-blue-600 transition hidden md:block">Kontak</a>
                <a href="{{ route('login') }}">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Login
                    </button>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="py-20 px-6 mt-20">
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl font-bold text-blue-600 mb-6 leading-tight">
                        Sewa Rak untuk Bisnis Anda
                    </h1>
                    <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                        Solusi penyimpanan rak yang aman, Fleksibel, dan Terjangkau. Kelola inventori Anda dengan mudah bersama SPACEGO.
                    </p>
                    <a href="#kontak">
                        <button class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg hover:bg-blue-700 shadow-lg transition">
                            Hubungi Kami Sekarang »
                        </button>
                    </a>
                </div>
                <div class="flex justify-center">
                    <svg viewBox="0 0 500 400" class="w-full max-w-lg">
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
    </section>

    <!-- Stats -->
    <section class="py-12 px-6 bg-white">
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">500+</div>
                <div class="text-gray-600">Klien Aktif</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">25+</div>
                <div class="text-gray-600">Lokasi Rak</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">2,500+</div>
                <div class="text-gray-600">Rak Tersedia</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">24/7</div>
                <div class="text-gray-600">Akses</div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="py-16 px-6 bg-gradient-to-br from-blue-50 to-white">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-4 text-gray-800">Tentang SPACEGO</h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
                Solusi penyimpanan rak terpercaya untuk bisnis modern
            </p>
            
            <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Siapa Kami?</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        SPACEGO adalah perusahaan penyedia layanan sewa rak dan ruang penyimpanan yang telah berpengalaman lebih dari 10 tahun dalam industri logistik dan warehousing. Kami memahami bahwa setiap bisnis memiliki kebutuhan penyimpanan yang unik.
                    </p>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Dengan jaringan rak yang tersebar di berbagai lokasi strategis, kami menawarkan solusi penyimpanan yang fleksibel, aman, dan efisien untuk berbagai jenis industri mulai dari e-commerce, retail, manufaktur, hingga distribusi.
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Pengalaman 10+ Tahun</h4>
                                <p class="text-gray-600 text-sm">Telah melayani ribuan klien dengan kepuasan maksimal</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Teknologi Modern</h4>
                                <p class="text-gray-600 text-sm">Sistem manajemen rak berbasis digital untuk kemudahan monitoring</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Tim Profesional</h4>
                                <p class="text-gray-600 text-sm">Didukung oleh tim berpengalaman dan customer service responsif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="bg-blue-600 rounded-2xl p-8 md:p-12 text-white">
                <h3 class="text-3xl font-bold mb-6 text-center">Visi & Misi Kami</h3>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white/10 backdrop-blur rounded-xl p-6">
                        <h4 class="text-xl font-semibold mb-3">Visi</h4>
                        <p class="text-blue-50">
                            Menjadi penyedia layanan rak dan penyimpanan terdepan di Indonesia yang dikenal dengan inovasi, keandalan, dan kepuasan pelanggan.
                        </p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-6">
                        <h4 class="text-xl font-semibold mb-3">Misi</h4>
                        <ul class="space-y-2 text-blue-50">
                            <li>• Memberikan solusi penyimpanan berkualitas tinggi</li>
                            <li>• Mengutamakan keamanan dan kenyamanan klien</li>
                            <li>• Berinovasi dengan teknologi terkini</li>
                            <li>• Membangun kemitraan jangka panjang</li>
                        </ul>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>

    <!-- Features -->
    <section id="layanan" class="py-16 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-800">Keunggulan Kami</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-center">Keamanan Terjamin</h3>
                    <p class="text-gray-600 text-center">Sistem keamanan 24/7 dengan CCTV dan petugas jaga</p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-center">Harga Terjangkau</h3>
                    <p class="text-gray-600 text-center">Harga kompetitif dengan berbagai paket pilihan</p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-center">Lokasi Strategis</h3>
                    <p class="text-gray-600 text-center">Tersebar di berbagai lokasi mudah diakses</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="kontak" class="py-16 px-6 bg-blue-600">
        <div class="max-w-6xl mx-auto text-center text-white">
            <h2 class="text-4xl font-bold mb-8">Hubungi Kami</h2>
            <p class="text-blue-100 mb-8 text-lg">Siap membantu kebutuhan penyimpanan rak bisnis Anda</p>
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div class="bg-white/10 backdrop-blur rounded-lg p-6 hover:bg-white/20 transition">
                    <div class="font-semibold text-lg mb-2">Telepon</div>
                    <div class="text-blue-100">(021) 1234-5678</div>
                    <div class="text-blue-100">0812-3456-7890</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-lg p-6 hover:bg-white/20 transition">
                    <div class="font-semibold text-lg mb-2">Email</div>
                    <div class="text-blue-100">info@spacego.id</div>
                    <div class="text-blue-100">support@spacego.id</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-lg p-6 hover:bg-white/20 transition">
                    <div class="font-semibold text-lg mb-2">Alamat</div>
                    <div class="text-blue-100">Jl. Gudang Raya No. 123</div>
                    <div class="text-blue-100">Jakarta Utara 14350</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8 px-6">
        <div class="max-w-6xl mx-auto text-center">
            <p>&copy; 2024 SPACEGO. All rights reserved.</p>
        </div>
    </footer>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/6281234567890" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition z-50 hover:scale-110">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
    </a>

</body>
</html>
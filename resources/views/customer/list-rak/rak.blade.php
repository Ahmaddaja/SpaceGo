@extends('layouts.app', ['title' => 'Rak Yang Sudah Dibeli'])

@section('title', 'Rak Yang Sudah Dibeli')

@push('styles')
    <style>
        .rak-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .rak-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .status-dikosongkan {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .type-badge {
            background: rgba(255, 255, 255, 0.95);
            color: #374151;
            border: 1px solid #e5e7eb;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            backdrop-filter: blur(8px);
        }

        .price-gradient {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-left: 4px solid #10b981;
        }

        .duration-gradient {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-left: 4px solid #f59e0b;
        }

        .code-gradient {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-left: 4px solid #3b82f6;
        }

        .empty-state {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1px solid #fbbf24;
        }

        .info-item {
            transition: all 0.2s ease;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item:hover {
            background: #f9fafb;
            border-radius: 0.5rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .pagination-container {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
        }

        .pagination .page-link {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem;
            color: #374151;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .pagination .page-item.active .page-link {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }

        .action-btn {
            transition: all 0.2s ease;
            font-weight: 600;
            border-radius: 0.75rem;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .btn-detail {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .btn-detail:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-history {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        .btn-history:hover {
            background: linear-gradient(135deg, #4b5563, #374151);
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .floating-icon {
            animation: float 3s ease-in-out infinite;
        }

        .gradient-text {
            background: linear-gradient(135deg, #1e40af, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .photo-carousel-container {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            pointer-events: none;
        }

        .carousel-slide.active {
            opacity: 1;
            pointer-events: auto;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 20;
            transition: all 0.3s ease;
            opacity: 0;
        }

        .photo-carousel-container:hover .carousel-btn {
            opacity: 1;
        }

        .carousel-btn:hover {
            background: rgba(0, 0, 0, 0.8);
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-prev {
            left: 12px;
        }

        .carousel-next {
            right: 12px;
        }

        .carousel-indicators {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 20;
        }

        .indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .indicator.active {
            background: white;
            width: 24px;
            border-radius: 4px;
        }

        .photo-counter-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            z-index: 20;
        }

        .image-hover {
            transition: transform 0.5s ease;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .rak-card:hover .image-hover {
            transform: scale(1.05);
        }

        .status-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 20;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .no-photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        }
    </style>
@endpush

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg mb-6">
                    <i class="fas fa-boxes text-white text-2xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 gradient-text">
                    Rak Yang Sudah Disewa
                </h1>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Kelola dan pantau semua rak penyimpanan yang telah Anda sewa dalam satu tempat
                </p>

                <div class="flex justify-center items-center space-x-6 mt-6">
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span>Total: <strong class="text-gray-700">{{ $raks->total() }} rak</strong></span>
                    </div>
                </div>
            </div>

            @if ($raks->isEmpty())
                <div class="empty-state rounded-2xl p-8 md:p-12 text-center max-w-2xl mx-auto">
                    <div class="flex justify-center mb-6">
                        <div class="bg-yellow-100 p-6 rounded-full floating-icon">
                            <i class="fas fa-shopping-cart text-yellow-600 text-4xl"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-yellow-800 mb-3">Belum Ada Rak yang Dibeli</h3>
                    <p class="text-yellow-700 mb-2 text-lg">Anda belum melakukan penyewaan rak apapun.</p>
                    <p class="text-yellow-600 mb-8 text-sm">Mulai sewa rak pertama Anda dan nikmati kemudahan penyimpanan!
                    </p>
                    <a href="{{ route('customer.list-rak.list-rak') }}"
                        class="inline-flex items-center space-x-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all duration-300 font-semibold text-lg action-btn">
                        <i class="fas fa-pallet"></i>
                        <span>Sewa Rak Sekarang</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">

                    @foreach ($raks as $rak)
                        @php
                            $hasPendingTransaction = \App\Models\Transaction::where('rak_id', $rak->id)
                                ->where('transaction_status', 'pending')
                                ->exists();

                            $isLocked = $rak->status !== 'tersedia' || $hasPendingTransaction;

                            // Cek apakah rak sudah dikosongkan
                            $isDikosongkan = false;
                            $dikosongkanAt = null;

                            if ($rak->transaction) {
                                // Logika dari model Transaction
                                $isDikosongkan = $rak->transaction->is_dikosongkan ?? false;
                                $dikosongkanAt = $rak->transaction->dikosongkan_at ?? null;
                            }

                            // Kumpulkan semua foto - PERBAIKAN DI SINI
                            $rakPhotos = [];
                            if (isset($rak->fotos) && $rak->fotos && $rak->fotos->isNotEmpty()) {
                                $rakPhotos = $rak->fotos->pluck('path')->toArray();
                            } elseif (isset($rak->foto) && $rak->foto) {
                                $rakPhotos = [$rak->foto];
                            }
                        @endphp

                        <div class="rak-card bg-white rounded-2xl shadow-lg overflow-hidden">

                            <!-- Header dengan foto -->
                            <div class="relative">
                                <div class="photo-carousel-container">
                                    @if (!empty($rakPhotos) && count($rakPhotos) > 0)
                                        @foreach ($rakPhotos as $index => $photo)
                                            <div class="carousel-slide {{ $index === 0 ? 'active' : '' }}"
                                                data-slide-index="{{ $index }}">
                                                <img src="{{ asset('storage/' . $photo) }}" class="image-hover"
                                                    alt="Foto Rak {{ $index + 1 }}"
                                                    onerror="this.onerror=null; this.src='https://via.placeholder.com/400x200?text=Rak+Tidak+Tersedia';">
                                            </div>
                                        @endforeach

                                        @if (count($rakPhotos) > 1)
                                            <button class="carousel-btn carousel-prev" onclick="changeSlide(this, -1)">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <button class="carousel-btn carousel-next" onclick="changeSlide(this, 1)">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>

                                            <div class="carousel-indicators">
                                                @foreach ($rakPhotos as $idx => $photo)
                                                    <button class="indicator {{ $idx === 0 ? 'active' : '' }}"
                                                        onclick="goToSlide(this, {{ $idx }})">
                                                    </button>
                                                @endforeach
                                            </div>

                                            <div class="photo-counter-badge">
                                                <i class="fas fa-images"></i>
                                                <span class="current-photo">1</span>/<span
                                                    class="total-photos">{{ count($rakPhotos) }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="no-photo-placeholder">
                                            <i class="fas fa-pallet text-4xl text-gray-400"></i>
                                        </div>
                                    @endif

                                    <!-- Status Badge -->
                                    @if ($isDikosongkan)
                                        <div class="status-badge bg-blue-600 text-white">
                                            <i class="fas fa-box-open mr-1"></i> Dikosongkan
                                        </div>
                                    @elseif(isset($rak->status) && $rak->status === 'terisi')
                                        <div class="status-badge bg-red-600 text-white">
                                            <i class="fas fa-box mr-1"></i> Terisi
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 space-y-5">

                                <div class="space-y-3">
                                    <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $rak->nama_rak ?? 'Nama Rak Tidak Tersedia' }}</h3>
                                    <div class="code-gradient px-4 py-3 rounded-xl">
                                        <p class="text-blue-700 text-sm font-semibold flex items-center">
                                            <i class="fas fa-barcode mr-2"></i>
                                            Kode: {{ $rak->kode_rak ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div class="info-item flex items-center justify-between">
                                        <span class="text-gray-600 text-sm flex items-center">
                                            <i class="fas fa-warehouse text-blue-500 mr-3 w-5 text-center"></i>
                                            Gudang
                                        </span>
                                        <span class="text-gray-900 font-semibold text-sm">
                                            {{ $rak->gudang->nama_gudang ?? ($rak->lokasi_gudang ?? 'N/A') }}
                                        </span>
                                    </div>

                                    <div class="info-item flex items-center justify-between">
                                        <span class="text-gray-600 text-sm flex items-center">
                                            <i class="fas fa-layer-group text-green-500 mr-3 w-5 text-center"></i>
                                            Jenis
                                        </span>
                                        <span class="text-gray-900 font-semibold text-sm">{{ $rak->jenis_rak ?? 'N/A' }}</span>
                                    </div>

                                    <div class="info-item flex items-center justify-between">
                                        <span class="text-gray-600 text-sm flex items-center">
                                            <i class="fas fa-ruler-combined text-purple-500 mr-3 w-5 text-center"></i>
                                            Dimensi
                                        </span>
                                        <span class="text-gray-900 font-semibold text-sm">
                                            {{ $rak->panjang ?? 0 }}×{{ $rak->lebar ?? 0 }}×{{ $rak->tinggi ?? 0 }}m
                                        </span>
                                    </div>

                                    <div class="info-item flex items-center justify-between">
                                        <span class="text-gray-600 text-sm flex items-center">
                                            <i class="fas fa-weight text-orange-500 mr-3 w-5 text-center"></i>
                                            Kapasitas
                                        </span>
                                        <span class="text-gray-900 font-semibold text-sm">
                                            {{ number_format($rak->kapasitas_berat ?? 0, 0, ',', '.') }} kg
                                        </span>
                                    </div>

                                    <!-- BAGIAN STATUS SEWA YANG BARU (MENGGANTIKAN KODE LAMA) -->
                                    <div class="info-item flex items-center justify-between">
                                        <span class="text-gray-600 text-sm flex items-center">
                                            <i class="fas fa-info-circle text-purple-500 mr-3 w-5 text-center"></i>
                                            Status Sewa
                                        </span>

                                        @php
                                            // Ambil status info dari tagihan
                                            $statusInfo = $rak->status_rak_info ?? null;

                                            // Jika tidak ada, gunakan status rak default
                                            if (!$statusInfo) {
                                                $statusInfo = [
                                                    'status' => $rak->status ?? 'tersedia',
                                                    'label' => ucfirst($rak->status ?? 'Tersedia'),
                                                    'color' => 'gray',
                                                    'icon' => 'fa-cube',
                                                    'description' => '',
                                                ];
                                            }

                                            // Mapping warna untuk background
                                            $colorMap = [
                                                'green' => [
                                                    'bg' => 'bg-green-100',
                                                    'text' => 'text-green-700',
                                                    'icon' => 'text-green-600',
                                                ],
                                                'yellow' => [
                                                    'bg' => 'bg-yellow-100',
                                                    'text' => 'text-yellow-700',
                                                    'icon' => 'text-yellow-600',
                                                ],
                                                'orange' => [
                                                    'bg' => 'bg-orange-100',
                                                    'text' => 'text-orange-700',
                                                    'icon' => 'text-orange-600',
                                                ],
                                                'purple' => [
                                                    'bg' => 'bg-purple-100',
                                                    'text' => 'text-purple-700',
                                                    'icon' => 'text-purple-600',
                                                ],
                                                'blue' => [
                                                    'bg' => 'bg-blue-100',
                                                    'text' => 'text-blue-700',
                                                    'icon' => 'text-blue-600',
                                                ],
                                                'red' => [
                                                    'bg' => 'bg-red-100',
                                                    'text' => 'text-red-700',
                                                    'icon' => 'text-red-600',
                                                ],
                                                'gray' => [
                                                    'bg' => 'bg-gray-100',
                                                    'text' => 'text-gray-700',
                                                    'icon' => 'text-gray-600',
                                                ],
                                            ];

                                            $colors = $colorMap[$statusInfo['color']] ?? $colorMap['gray'];
                                        @endphp

                                        <span
                                            class="inline-flex items-center px-3 py-1 {{ $colors['bg'] }} {{ $colors['text'] }} rounded-lg text-xs font-semibold">
                                            <i class="fas {{ $statusInfo['icon'] }} {{ $colors['icon'] }} mr-1"></i>
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </div>

                                    <!-- Info Box berdasarkan status -->
                                    @if ($statusInfo['status'] === 'dikosongkan' && isset($rak->dikosongkan_at))
                                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                            <div class="flex items-start">
                                                <i class="fas fa-info-circle text-blue-600 mr-3 mt-1"></i>
                                                <div class="flex-1">
                                                    <p class="text-blue-800 text-sm font-semibold mb-1">
                                                        Rak Telah Dikosongkan
                                                    </p>
                                                    <p class="text-blue-700 text-xs leading-relaxed">
                                                        {{ $statusInfo['description'] }}
                                                    </p>
                                                    <p class="text-blue-600 text-xs mt-2">
                                                        Dikosongkan:
                                                        {{ \Carbon\Carbon::parse($rak->dikosongkan_at)->format('d M Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($statusInfo['status'] === 'pengosongan')
                                        <div class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                                            <div class="flex items-start">
                                                <i class="fas fa-exclamation-triangle text-purple-600 mr-3 mt-1"></i>
                                                <div class="flex-1">
                                                    <p class="text-purple-800 text-sm font-semibold mb-1">
                                                        🚨 Masa Pengosongan Aktif
                                                    </p>
                                                    <p class="text-purple-700 text-xs leading-relaxed mb-2">
                                                        {{ $statusInfo['description'] }}
                                                    </p>
                                                    @if (isset($rak->pengosongan_dimulai) && isset($rak->pengosongan_berakhir))
                                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                                            <div class="bg-white bg-opacity-50 rounded p-2">
                                                                <p class="text-purple-600 font-medium">Dimulai:</p>
                                                                <p class="text-purple-800 font-semibold">
                                                                    {{ \Carbon\Carbon::parse($rak->pengosongan_dimulai)->format('d M Y') }}
                                                                </p>
                                                            </div>
                                                            <div class="bg-white bg-opacity-50 rounded p-2">
                                                                <p class="text-purple-600 font-medium">Berakhir:</p>
                                                                <p class="text-purple-800 font-semibold">
                                                                    {{ \Carbon\Carbon::parse($rak->pengosongan_berakhir)->format('d M Y') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($statusInfo['status'] === 'terlambat')
                                        <div class="mt-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
                                            <div class="flex items-start">
                                                <i class="fas fa-exclamation-circle text-orange-600 mr-3 mt-1"></i>
                                                <div class="flex-1">
                                                    <p class="text-orange-800 text-sm font-semibold mb-1">
                                                        ⚠️ Terlambat Pembayaran
                                                    </p>
                                                    <p class="text-orange-700 text-xs leading-relaxed">
                                                        {{ $statusInfo['description'] }}. Segera perpanjang untuk
                                                        menghindari masa pengosongan.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($statusInfo['status'] === 'masa_tenggang')
                                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <div class="flex items-start">
                                                <i class="fas fa-clock text-yellow-600 mr-3 mt-1"></i>
                                                <div class="flex-1">
                                                    <p class="text-yellow-800 text-sm font-semibold mb-1">
                                                        🕐 Masa Tenggang
                                                    </p>
                                                    <p class="text-yellow-700 text-xs leading-relaxed">
                                                        {{ $statusInfo['description'] }}. Perpanjang sekarang untuk
                                                        menghindari denda.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($statusInfo['status'] === 'terisi')
                                        <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-start">
                                                <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                                                <div class="flex-1">
                                                    <p class="text-green-800 text-sm font-semibold mb-1">
                                                        ✅ Sedang Aktif
                                                    </p>
                                                    <p class="text-green-700 text-xs leading-relaxed">
                                                        {{ $statusInfo['description'] }}. Nikmati layanan penyimpanan Anda.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <!-- AKHIR BAGIAN STATUS SEWA YANG BARU -->

                                    @if (isset($rak->durasi_sewa_hari) && $rak->durasi_sewa_hari)
                                        <div class="duration-gradient p-4 rounded-xl">
                                            <p class="text-amber-700 text-sm font-medium mb-1 flex items-center">
                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                Durasi Sewa
                                            </p>
                                            <p class="text-amber-600 text-2xl font-bold">
                                                {{ $rak->durasi_sewa_hari }} Hari
                                                <span class="text-sm font-normal text-amber-500">
                                                    ({{ round($rak->durasi_sewa_hari / 30, 1) }} bulan)
                                                </span>
                                            </p>
                                        </div>
                                    @endif

                                    @if (isset($rak->harga_sewa_perbulan) && $rak->harga_sewa_perbulan)
                                        <div class="price-gradient p-4 rounded-xl">
                                            <p class="text-green-700 text-sm font-medium mb-1 flex items-center">
                                                <i class="fas fa-money-bill-wave mr-2"></i>
                                                Harga Sewa
                                            </p>
                                            <p class="text-green-600 text-2xl font-bold">
                                                Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                                                <span class="text-sm font-normal text-green-500">
                                                    /{{ $rak->durasi_sewa_hari ?? 30 }} hari
                                                </span>
                                            </p>
                                        </div>
                                    @endif

                                    <div class="flex space-x-3 pt-2">
                                        @if ($isDikosongkan)
                                            <a href="{{ route('customer.list-rak.list-rak') }}"
                                                class="flex-1 action-btn bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center py-3 px-4 rounded-lg font-semibold text-sm hover:shadow-lg">
                                                <i class="fas fa-shopping-cart mr-2"></i> Sewa Lagi
                                            </a>
                                        @endif

                                        <a href="{{ route('customer.list-rak.detail', $rak->id) }}"
                                            class="{{ $isDikosongkan ? 'flex-1' : 'w-full' }} action-btn btn-detail text-center py-3 px-4 rounded-lg font-semibold text-sm hover:shadow-lg">
                                            <i class="fas fa-eye mr-2"></i> Detail Rak
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

                <!-- Pagination -->
                @if ($raks->hasPages())
                    <div class="pagination-container mt-12 p-6">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ $raks->firstItem() }}</span>
                                sampai
                                <span class="font-medium">{{ $raks->lastItem() }}</span>
                                dari
                                <span class="font-medium">{{ $raks->total() }}</span>
                                rak
                            </div>
                            <div class="flex space-x-2">
                                {{ $raks->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                @endif

            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Fungsi untuk carousel
        function changeSlide(button, direction) {
            const container = button.closest('.photo-carousel-container');
            const slides = container.querySelectorAll('.carousel-slide');
            const indicators = container.querySelectorAll('.indicator');
            const counterCurrent = container.querySelector('.current-photo');

            let currentIndex = 0;
            slides.forEach((slide, index) => {
                if (slide.classList.contains('active')) {
                    currentIndex = index;
                }
            });

            let newIndex = currentIndex + direction;
            if (newIndex >= slides.length) newIndex = 0;
            if (newIndex < 0) newIndex = slides.length - 1;

            // Update slides
            slides[currentIndex].classList.remove('active');
            slides[newIndex].classList.add('active');

            // Update indicators
            indicators[currentIndex].classList.remove('active');
            indicators[newIndex].classList.add('active');

            // Update counter
            if (counterCurrent) {
                counterCurrent.textContent = newIndex + 1;
            }
        }

        function goToSlide(button, index) {
            const container = button.closest('.photo-carousel-container');
            const slides = container.querySelectorAll('.carousel-slide');
            const indicators = container.querySelectorAll('.indicator');
            const counterCurrent = container.querySelector('.current-photo');

            // Update slides
            slides.forEach(slide => slide.classList.remove('active'));
            slides[index].classList.add('active');

            // Update indicators
            indicators.forEach(ind => ind.classList.remove('active'));
            indicators[index].classList.add('active');

            // Update counter
            if (counterCurrent) {
                counterCurrent.textContent = index + 1;
            }
        }

        // Animasi kartu saat load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.rak-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Auto-play carousel
            const carousels = document.querySelectorAll('.photo-carousel-container');
            carousels.forEach(carousel => {
                const slides = carousel.querySelectorAll('.carousel-slide');
                if (slides.length > 1) {
                    let autoPlayInterval;

                    const startAutoPlay = () => {
                        autoPlayInterval = setInterval(() => {
                            const nextBtn = carousel.querySelector('.carousel-next');
                            if (nextBtn) {
                                changeSlide(nextBtn, 1);
                            }
                        }, 5000);
                    };

                    const stopAutoPlay = () => {
                        clearInterval(autoPlayInterval);
                    };

                    // Start autoplay
                    startAutoPlay();

                    // Pause on hover
                    carousel.addEventListener('mouseenter', stopAutoPlay);
                    carousel.addEventListener('mouseleave', startAutoPlay);
                }
            });
        });
    </script>
@endpush
@extends('layouts.app')

@section('title', 'Detail Rak - SPACEGO')

@push('styles')
    <style>
        .detail-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .detail-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .spec-card {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .spec-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            backdrop-filter: blur(8px);
        }

        .status-available {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .status-occupied {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .status-maintenance {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .type-badge {
            background: rgba(255, 255, 255, 0.95);
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .image-hover {
            transition: transform 0.5s ease;
        }

        .image-hover:hover {
            transform: scale(1.05);
        }

        .action-button {
            transition: all 0.3s ease;
        }

        .action-button:hover {
            transform: translateY(-2px);
        }

        .rental-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .rental-date-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .rental-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- TITLE -->
            <div class="mb-8 text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Detail Rak</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Informasi lengkap mengenai rak yang Anda pilih</p>
            </div>

            <!-- RENTAL INFO (Jika user sudah menyewa) -->
            @php
                $activeRental = null;
                if (Auth::check()) {
                    $activeRental = \App\Models\Transaction::where('user_id', Auth::id())
                        ->where('rak_id', $rak->id)
                        ->whereIn('transaction_status', ['settlement', 'capture'])
                        ->where('sewa_berakhir', '>=', now())
                        ->orderBy('sewa_berakhir', 'desc')
                        ->first();
                }
            @endphp

            @if ($activeRental)
                <div class="mb-8 rental-info-card">
                    <div class="flex items-center mb-4">
                        <div class="rental-icon mr-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Anda Sedang Menyewa Rak Ini</h3>
                            <p class="text-sm opacity-90">Order ID: {{ $activeRental->order_id }}</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mt-6">
                        <div class="rental-date-box">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-calendar-check mr-2 text-lg"></i>
                                <span class="font-semibold">Tanggal Mulai Sewa</span>
                            </div>
                            <p class="text-2xl font-bold">
                                {{ \Carbon\Carbon::parse($activeRental->sewa_mulai)->format('d M Y') }}
                            </p>
                            <p class="text-sm opacity-80 mt-1">
                                {{ \Carbon\Carbon::parse($activeRental->sewa_mulai)->diffForHumans() }}
                            </p>
                        </div>

                        <div class="rental-date-box">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-calendar-times mr-2 text-lg"></i>
                                <span class="font-semibold">Tanggal Berakhir Sewa</span>
                            </div>
                            <p class="text-2xl font-bold">
                                {{ \Carbon\Carbon::parse($activeRental->sewa_berakhir)->format('d M Y') }}
                            </p>
                            <p class="text-sm opacity-80 mt-1">
                                {{ \Carbon\Carbon::parse($activeRental->sewa_berakhir)->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    @php
                        $daysRemaining = \Carbon\Carbon::parse($activeRental->sewa_berakhir)
                            ->startOfDay()
                            ->diffInDays(now()->startOfDay(), true);
                    @endphp

                    <div class="mt-4 p-3 bg-white bg-opacity-20 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold">Sisa Waktu Sewa:</span>
                            <span class="text-xl font-bold">
                                @if ($daysRemaining > 0)
                                    {{ $daysRemaining }} Hari
                                @elseif($daysRemaining == 0)
                                    Berakhir Hari Ini
                                @else
                                    Sudah Berakhir
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- MAIN CARD -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 detail-card">

                <div class="grid lg:grid-cols-2 gap-8 p-8">
                    <!-- FOTO SECTION -->
                    @include('customer.list-rak.partials.photo-section')

                    <!-- INFO DETAIL SECTION -->
                    @include('customer.list-rak.partials.info-section')
                </div>
            </div>

            <!-- SPESIFIKASI SECTION -->
            @include('customer.list-rak.partials.specifications-section')

            <!-- ACTION BUTTONS -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <a href="{{ route('customer.list-rak.list-rak') }}"
                    class="flex items-center justify-center space-x-3 px-8 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-300 font-semibold shadow-md hover:shadow-lg group action-button">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    <span>Kembali ke Daftar Rak</span>
                </a>

                @if ($activeRental)
                    <button
                        class="flex-1 flex items-center justify-center space-x-3 px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl cursor-default font-semibold shadow-lg">
                        <i class="fas fa-check-circle"></i>
                        <span>Rak Sedang Anda Sewa</span>
                    </button>
                @elseif ($rak->status === 'tersedia')
                    <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                        class="flex-1 flex items-center justify-center space-x-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all duration-300 font-semibold shadow-lg action-button">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Sewa Sekarang</span>
                    </a>
                @else
                    <button
                        class="flex-1 flex items-center justify-center space-x-3 px-8 py-4 bg-gray-300 text-gray-500 rounded-xl cursor-not-allowed font-semibold">
                        <i class="fas fa-ban"></i>
                        <span>Tidak Tersedia</span>
                    </button>
                @endif
            </div>

        </div>
    </div>

    <!-- WhatsApp Button -->
    @include('customer.payment.partials.whatsapp-button')
@endsection

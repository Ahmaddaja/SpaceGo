@extends('layouts.app')

@section('title', 'SPACEGO - Daftar Rak')

@push('styles')
    <style>
        .rak-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .rak-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
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

        .filter-card {
            background: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-button {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            transition: all 0.3s ease;
        }

        .filter-button:hover {
            background: linear-gradient(135deg, #1d4ed8, #7c3aed);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
        }

        .image-hover {
            transition: transform 0.5s ease;
        }

        .rak-card:hover .image-hover {
            transform: scale(1.1);
        }

        .action-button {
            transition: all 0.2s ease;
        }

        .action-button:hover {
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- HEADER SECTION -->
            <section class="mb-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Daftar Rak Tersedia</h1>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">Pilih rak penyimpanan yang sesuai dengan kebutuhan
                        bisnis Anda</p>
                </div>
            </section>

            <!-- FILTER DAN SEARCH -->
            <section class="mb-8">
                @include('customer.list-rak.partials.filter-section')
            </section>

            <!-- LIST RAK -->
            <section class="mb-16">
                @if ($raks->count() > 0)
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($raks as $rak)
                            @include('customer.list-rak.partials.rak-card')
                        @endforeach
                    </div>

                    <!-- PAGINATION - FIXED VERSION -->
                    @if ($raks->hasPages())
                        <div class="mt-12 flex justify-center">
                            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                                {{ $raks->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <!-- EMPTY STATE -->
                    <div class="text-center py-16">
                        <div class="bg-white rounded-2xl shadow-lg p-12 max-w-2xl mx-auto border border-gray-100">
                            <div class="flex justify-center mb-6">
                                <div class="bg-blue-100 p-6 rounded-full">
                                    <i class="fas fa-pallet text-blue-600 text-4xl"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Tidak Ada Rak Ditemukan</h3>
                            <p class="text-gray-600 mb-6">Coba ubah filter pencarian Anda atau hubungi kami untuk informasi
                                lebih lanjut.</p>
                            <a href="{{ route('customer.list-rak.list-rak') }}"
                                class="inline-flex items-center space-x-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                                <i class="fas fa-refresh"></i>
                                <span>Reset Pencarian</span>
                            </a>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>

    <!-- WhatsApp Button -->
    @include('customer.payment.partials.whatsapp-button')
@endsection
@endsection

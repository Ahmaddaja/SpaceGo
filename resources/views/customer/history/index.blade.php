<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPACEGO - History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 min-h-screen">

    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Main Content -->
    <section class="py-8 px-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-history text-blue-600 mr-3"></i>History Aktivitas
                </h1>
                <p class="text-lg text-gray-600">
                    Riwayat lengkap semua aktivitas dan transaksi Anda di SPACEGO
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 mb-8">
                <a href="{{ route('customer.history.payments') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 shadow-md flex items-center">
                    <i class="fas fa-receipt mr-2"></i>History Pembayaran
                </a>
                <a href="{{ route('customer.list-rak.list-rak') }}" class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 shadow border border-gray-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Rak
                </a>
            </div>

            <!-- History Content -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                @if(isset($histories) && $histories->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($histories as $history)
                            <div class="p-6 hover:bg-gray-50 transition-all duration-300 
                                @if($history->activity_type === 'PAYMENT_SUCCESS') border-l-4 border-green-500 
                                @elseif($history->activity_type === 'NEW_RENTAL') border-l-4 border-blue-500 
                                @elseif($history->activity_type === 'RENTAL_EXTENSION') border-l-4 border-yellow-500 
                                @else border-l-4 border-gray-500 @endif">
                                
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-start space-x-4">
                                            <!-- Icon -->
                                            <div class="flex-shrink-0">
                                                @switch($history->activity_type)
                                                    @case('PAYMENT_SUCCESS')
                                                        <div class="bg-green-100 p-3 rounded-xl">
                                                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                                        </div>
                                                        @break
                                                    @case('NEW_RENTAL')
                                                        <div class="bg-blue-100 p-3 rounded-xl">
                                                            <i class="fas fa-cube text-blue-600 text-xl"></i>
                                                        </div>
                                                        @break
                                                    @case('RENTAL_EXTENSION')
                                                        <div class="bg-yellow-100 p-3 rounded-xl">
                                                            <i class="fas fa-calendar-plus text-yellow-600 text-xl"></i>
                                                        </div>
                                                        @break
                                                    @default
                                                        <div class="bg-gray-100 p-3 rounded-xl">
                                                            <i class="fas fa-info-circle text-gray-600 text-xl"></i>
                                                        </div>
                                                @endswitch
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                                    {{ $history->description }}
                                                </h3>
                                                
                                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    <span>{{ $history->created_at->format('d M Y H:i') }}</span>
                                                    @if($history->created_by !== 'system')
                                                        <span class="mx-2">•</span>
                                                        <span>oleh {{ $history->created_by }}</span>
                                                    @endif
                                                </div>

                                                @if($history->additional_data)
                                                    <div class="flex flex-wrap gap-2">
                                                        @php
                                                            $data = $history->additional_data;
                                                        @endphp
                                                        @if(isset($data['amount']))
                                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                                Rp {{ number_format($data['amount'], 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                        @if(isset($data['rack_code']))
                                                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                                                <i class="fas fa-pallet mr-1"></i>
                                                                Rak: {{ $data['rack_code'] }}
                                                            </span>
                                                        @endif
                                                        @if(isset($data['duration']))
                                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                                {{ $data['duration'] }} hari
                                                            </span>
                                                        @endif
                                                        @if(isset($data['payment_method']))
                                                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                                                <i class="fas fa-credit-card mr-1"></i>
                                                                {{ $data['payment_method'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($history->activity_type === 'PAYMENT_SUCCESS') bg-green-100 text-green-800
                                            @elseif($history->activity_type === 'NEW_RENTAL') bg-blue-100 text-blue-800
                                            @elseif($history->activity_type === 'RENTAL_EXTENSION') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $history->activity_type }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination - MODIFIED SECTION -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Menampilkan 
                                <span class="font-medium">{{ $histories->firstItem() }}</span> 
                                sampai 
                                <span class="font-medium">{{ $histories->lastItem() }}</span> 
                                dari 
                                <span class="font-medium">{{ $histories->total() }}</span> 
                                hasil
                            </div>
                            <div class="flex space-x-2">
                                <!-- Previous Page -->
                                @if ($histories->onFirstPage())
                                    <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $histories->previousPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                <!-- Page Numbers -->
                                @php
                                    $current = $histories->currentPage();
                                    $last = $histories->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                @endphp

                                @if($start > 1)
                                    <a href="{{ $histories->url(1) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">1</a>
                                    @if($start > 2)
                                        <span class="px-3 py-2 text-gray-500">...</span>
                                    @endif
                                @endif

                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page == $current)
                                        <span class="px-3 py-2 bg-blue-600 text-white rounded-lg font-medium">{{ $page }}</span>
                                    @else
                                        <a href="{{ $histories->url($page) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">{{ $page }}</a>
                                    @endif
                                @endfor

                                @if($end < $last)
                                    @if($end < $last - 1)
                                        <span class="px-3 py-2 text-gray-500">...</span>
                                    @endif
                                    <a href="{{ $histories->url($last) }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">{{ $last }}</a>
                                @endif

                                <!-- Next Page -->
                                @if ($histories->hasMorePages())
                                    <a href="{{ $histories->nextPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-200">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-history text-4xl text-blue-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum ada history aktivitas</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">
                            Riwayat aktivitas dan transaksi Anda akan muncul di sini. Mulai sewa rak pertama Anda untuk melihat history.
                        </p>
                        <a href="{{ route('customer.list-rak.list-rak') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-xl text-lg font-semibold hover:shadow-lg transition-all duration-300 shadow-md inline-flex items-center">
                            <i class="fas fa-pallet mr-2"></i>Sewa Rak Sekarang
                        </a>
                    </div>
                @endif
            </div>

            <!-- Info Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-sm border border-blue-200">
                    <div class="flex items-center">
                        <div class="bg-blue-500 p-3 rounded-lg mr-4">
                            <i class="fas fa-receipt text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $histories->total() ?? 0 }}</div>
                            <div class="text-gray-700 font-medium">Total Aktivitas</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow-sm border border-green-200">
                    <div class="flex items-center">
                        <div class="bg-green-500 p-3 rounded-lg mr-4">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ isset($histories) ? $histories->where('activity_type', 'PAYMENT_SUCCESS')->count() : 0 }}
                            </div>
                            <div class="text-gray-700 font-medium">Pembayaran Berhasil</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 shadow-sm border border-purple-200">
                    <div class="flex items-center">
                        <div class="bg-purple-500 p-3 rounded-lg mr-4">
                            <i class="fas fa-cube text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600">
                                {{ isset($histories) ? $histories->where('activity_type', 'NEW_RENTAL')->count() : 0 }}
                            </div>
                            <div class="text-gray-700 font-medium">Sewa Rak</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 mt-12">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="flex items-center justify-center space-x-3 mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-2 rounded-lg">
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
@php
    // Deteksi halaman saat ini
    $currentRoute = Route::currentRouteName();
    $isPaymentPage = $currentRoute === 'customer.history.payment';
    $isHistoryPage = $currentRoute === 'customer.history' || $currentRoute === 'customer.history.index';
@endphp

<div class="flex flex-wrap gap-4 mb-8">
    <!-- Tombol untuk berpindah antara History Aktivitas dan History Pembayaran -->
    @if($isHistoryPage)
        <a href="{{ route('customer.history.payment') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 shadow-md flex items-center">
            <i class="fas fa-receipt mr-2"></i>History Pembayaran
        </a>
    @elseif($isPaymentPage)
        <a href="{{ route('customer.history') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 shadow-md flex items-center">
            <i class="fas fa-history mr-2"></i>History Aktivitas
        </a>
    @endif
    
    <!-- Tombol Kembali ke Rak -->
    @if(Route::has('customer.list-rak.list-rak'))
        <a href="{{ route('customer.list-rak.list-rak') }}" class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 shadow border border-gray-200 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Rak
        </a>
    @elseif(Route::has('customer.rak'))
        <a href="{{ route('customer.rak') }}" class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 shadow border border-gray-200 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Rak
        </a>
    @endif
</div>
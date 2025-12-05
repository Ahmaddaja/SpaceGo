@php
    $title = isset($isPaymentPage) && $isPaymentPage 
        ? 'History Pembayaran' 
        : 'History Aktivitas';
    $icon = isset($isPaymentPage) && $isPaymentPage 
        ? 'fas fa-receipt text-blue-600' 
        : 'fas fa-history text-blue-600';
    $desc = isset($isPaymentPage) && $isPaymentPage
        ? 'Riwayat lengkap semua pembayaran dan transaksi keuangan Anda di SPACEGO'
        : 'Riwayat lengkap semua aktivitas dan transaksi Anda di SPACEGO';
@endphp

<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-800 mb-2">
        <i class="{{ $icon }} mr-3"></i>{{ $title }}
    </h1>
    <p class="text-lg text-gray-600">{{ $desc }}</p>
</div>
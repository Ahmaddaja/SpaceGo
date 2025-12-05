@extends('layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 pt-20">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tagihan & Perpanjangan</h1>
                    <p class="text-gray-600 mt-2">Kelola pembayaran dan perpanjangan sewa rak Anda</p>
                </div>
                <a href="{{ route('customer.tagihan.check-overdue') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh Status
                </a>
            </div>
            @include('customer.tagihan.partials.stats')
        </div>

        @if ($pendingTransactions->count() > 0)
            @include('customer.tagihan.partials.pending-table')
        @endif

        @if ($overdueTransactions->count() > 0)
            @include('customer.tagihan.partials.overdue-cards')
        @endif

        @if ($expiredTransactions->count() > 0)
            @include('customer.tagihan.partials.expired-table')
        @endif

        @if ($pendingTransactions->count() == 0 && $expiredTransactions->count() == 0 && $overdueTransactions->count() == 0)
            @include('customer.tagihan.partials.empty-state')
        @endif
    </div>

    @include('customer.tagihan.partials.modals-and-scripts')
@endsection
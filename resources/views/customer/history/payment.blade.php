@extends('layouts.app')
@section('content')
    <section class="py-8 px-6">
        <div class="max-w-7xl mx-auto">
            @include('customer.history.partials.header', ['isPaymentPage' => true])
            @include('customer.history.partials.action-buttons')
            
            <!-- Tampilkan statistik pembayaran khusus -->
            @if(isset($paymentStats) && $paymentStats['total_transactions'] > 0)
                @include('customer.history.partials.payment-stats')
            @endif

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mt-8">
                @if(isset($payments) && $payments->count() > 0)
                    @if($isTransactionData ?? false)
                        <!-- Jika data dari tabel transaksi -->
                        <div class="divide-y divide-gray-100">
                            @foreach($payments as $payment)
                                @include('customer.history.partials.history-item', ['history' => $payment])
                            @endforeach
                        </div>
                    @else
                        <!-- Jika data dari CustomerHistory lama -->
                        @include('customer.history.partials.payment-table')
                    @endif
                    @include('customer.history.partials.pagination', ['histories' => $payments])
                @else
                    @include('customer.history.partials.empty-state')
                @endif
            </div>

            @include('customer.history.partials.payment-modal')
        </div>
    </section>
@endsection
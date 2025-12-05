@extends('layouts.app')
@section('content')
    <section class="py-8 px-6">
        <div class="max-w-7xl mx-auto">
            @include('customer.history.partials.header', ['isPaymentPage' => true])
            @include('customer.history.partials.action-buttons')

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                @if(isset($payments) && $payments->count() > 0)
                    @include('customer.history.partials.payment-table')
                    @include('customer.history.partials.pagination', ['histories' => $payments])
                @else
                    @include('customer.history.partials.empty-state')
                @endif
            </div>

            @include('customer.history.partials.stats', ['histories' => $payments ?? collect()])

            @include('customer.history.partials.payment-modal')
        </div>
    </section>
@endsection
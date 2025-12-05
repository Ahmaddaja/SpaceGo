@extends('layouts.app') {{-- Pastikan layout-nya sesuai --}}
@section('content')
    <section class="py-8 px-6">
        <div class="max-w-7xl mx-auto">
            @include('customer.history.partials.header', ['isPaymentPage' => false])
            @include('customer.history.partials.action-buttons')

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                @if(isset($histories) && $histories->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($histories as $history)
                            @include('customer.history.partials.history-item', ['history' => $history])
                        @endforeach
                    </div>
                    @include('customer.history.partials.pagination', ['histories' => $histories])
                @else
                    @include('customer.history.partials.empty-state')
                @endif
            </div>

            @include('customer.history.partials.stats', ['histories' => $histories ?? collect()])
        </div>
    </section>
@endsection
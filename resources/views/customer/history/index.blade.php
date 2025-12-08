@extends('layouts.app')
@section('content')
    <section class="py-8 px-6">
        <div class="max-w-7xl mx-auto">
            @include('customer.history.partials.header', ['isPaymentPage' => false])
            @include('customer.history.partials.action-buttons')
            
            <!-- Tampilkan statistik hanya di halaman history aktivitas -->
            @if(isset($showStats) && $showStats)
                @include('customer.history.partials.stats')
            @endif

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mt-8">
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
        </div>
    </section>
@endsection
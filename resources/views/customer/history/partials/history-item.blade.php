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
                            @php $data = $history->additional_data; @endphp
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
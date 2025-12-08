@php
    // Deteksi tipe data
    $isTransactionData = isset($history->transaction_status) || isset($history->payment_type);
    $isCustomerHistory = isset($history->activity_type) && isset($history->description);
    
    // Tentukan tipe aktivitas berdasarkan status transaksi (jika ada)
    if ($isTransactionData) {
        // Tentukan warna dan ikon berdasarkan transaction_status
        if (isset($history->transaction_status)) {
            if ($history->transaction_status === 'settlement') {
                $activityType = 'PAYMENT_SUCCESS';
                $iconClass = 'fa-check-circle';
                $colorClass = 'green';
            } elseif ($history->transaction_status === 'pending') {
                $activityType = 'PAYMENT_PENDING';
                $iconClass = 'fa-clock';
                $colorClass = 'yellow';
            } elseif (in_array($history->transaction_status, ['expire', 'deny', 'cancel'])) {
                $activityType = 'PAYMENT_FAILED';
                $iconClass = 'fa-times-circle';
                $colorClass = 'red';
            } else {
                $activityType = 'PAYMENT';
                $iconClass = 'fa-info-circle';
                $colorClass = 'gray';
            }
        } else {
            $activityType = 'PAYMENT_SUCCESS';
            $iconClass = 'fa-check-circle';
            $colorClass = 'green';
        }
    } elseif ($isCustomerHistory) {
        $activityType = $history->activity_type;
        $iconClass = match($history->activity_type) {
            'PAYMENT_SUCCESS' => 'fa-check-circle',
            'NEW_RENTAL' => 'fa-cube',
            'RENTAL_EXTENSION' => 'fa-calendar-plus',
            default => 'fa-info-circle'
        };
        $colorClass = match($history->activity_type) {
            'PAYMENT_SUCCESS' => 'green',
            'NEW_RENTAL' => 'blue',
            'RENTAL_EXTENSION' => 'yellow',
            default => 'gray'
        };
    } else {
        $activityType = 'UNKNOWN';
        $iconClass = 'fa-info-circle';
        $colorClass = 'gray';
    }
    
    // Format description
    if ($isTransactionData) {
        $description = 'Pembayaran Rak #' . ($history->rak_id ?? 'N/A');
        if (isset($history->order_id)) {
            $description .= ' - ' . $history->order_id;
        }
    } else {
        $description = $history->description ?? 'Aktivitas';
    }
    
    // Format tanggal
    $createdAt = isset($history->created_at) 
        ? (is_string($history->created_at) 
            ? \Carbon\Carbon::parse($history->created_at)
            : $history->created_at)
        : now();
@endphp

<div class="p-6 hover:bg-gray-50 transition-all duration-300 
    @if($colorClass === 'green') border-l-4 border-green-500 
    @elseif($colorClass === 'blue') border-l-4 border-blue-500 
    @elseif($colorClass === 'yellow') border-l-4 border-yellow-500 
    @elseif($colorClass === 'red') border-l-4 border-red-500
    @else border-l-4 border-gray-500 @endif">
    
    <div class="flex justify-between items-start">
        <div class="flex-1">
            <div class="flex items-start space-x-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    @if($colorClass === 'green')
                        <div class="bg-green-100 p-3 rounded-xl">
                            <i class="fas {{ $iconClass }} text-green-600 text-xl"></i>
                        </div>
                    @elseif($colorClass === 'blue')
                        <div class="bg-blue-100 p-3 rounded-xl">
                            <i class="fas {{ $iconClass }} text-blue-600 text-xl"></i>
                        </div>
                    @elseif($colorClass === 'yellow')
                        <div class="bg-yellow-100 p-3 rounded-xl">
                            <i class="fas {{ $iconClass }} text-yellow-600 text-xl"></i>
                        </div>
                    @elseif($colorClass === 'red')
                        <div class="bg-red-100 p-3 rounded-xl">
                            <i class="fas {{ $iconClass }} text-red-600 text-xl"></i>
                        </div>
                    @else
                        <div class="bg-gray-100 p-3 rounded-xl">
                            <i class="fas {{ $iconClass }} text-gray-600 text-xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        {{ $description }}
                    </h3>
                    
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-clock mr-2"></i>
                        <span>{{ $createdAt->format('d M Y H:i') }}</span>
                        @if(isset($history->created_by) && $history->created_by !== 'system')
                            <span class="mx-2">•</span>
                            <span>oleh {{ $history->created_by }}</span>
                        @endif
                    </div>

                    <!-- Additional Data / Transaction Details -->
                    <div class="flex flex-wrap gap-2">
                        @if($isTransactionData)
                            <!-- Data dari Tabel Transaksi -->
                            @if(isset($history->amount))
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                    Rp {{ number_format($history->amount, 0, ',', '.') }}
                                </span>
                            @endif
                            
                            @if(isset($history->rak_id))
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-pallet mr-1"></i>
                                    Rak ID: {{ $history->rak_id }}
                                </span>
                            @endif
                            
                            @if(isset($history->payment_type))
                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-credit-card mr-1"></i>
                                    {{ strtoupper($history->payment_type) }}
                                </span>
                            @endif
                            
                            @if(isset($history->transaction_status))
                                @if($history->transaction_status === 'settlement')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Berhasil
                                    </span>
                                @elseif($history->transaction_status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif(in_array($history->transaction_status, ['expire', 'deny', 'cancel']))
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Gagal
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        {{ ucfirst($history->transaction_status) }}
                                    </span>
                                @endif
                            @endif
                            
                            @if(isset($history->is_renewal) && $history->is_renewal)
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-redo mr-1"></i>
                                    Perpanjangan
                                </span>
                            @endif
                            
                        @elseif($isCustomerHistory && $history->additional_data)
                            <!-- Data dari CustomerHistory lama -->
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
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="flex-shrink-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($colorClass === 'green') bg-green-100 text-green-800
                @elseif($colorClass === 'blue') bg-blue-100 text-blue-800
                @elseif($colorClass === 'yellow') bg-yellow-100 text-yellow-800
                @elseif($colorClass === 'red') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800 @endif">
                @if($isTransactionData && isset($history->transaction_status))
                    @if($history->transaction_status === 'settlement')
                        <i class="fas fa-check-circle mr-1"></i> BERHASIL
                    @elseif($history->transaction_status === 'pending')
                        <i class="fas fa-clock mr-1"></i> PENDING
                    @elseif(in_array($history->transaction_status, ['expire', 'deny', 'cancel']))
                        <i class="fas fa-times-circle mr-1"></i> GAGAL
                    @else
                        {{ strtoupper($history->transaction_status) }}
                    @endif
                @else
                    {{ $activityType }}
                @endif
            </span>
        </div>
    </div>
</div>
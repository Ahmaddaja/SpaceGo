@if(isset($paymentStats) && $paymentStats['total_transactions'] > 0)
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 mb-8">
    <!-- Total Uang (HANYA settlement) -->
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 shadow-sm border border-purple-200">
        <div class="flex items-center">
            <div class="bg-purple-500 p-3 rounded-lg mr-4">
                <i class="fas fa-money-bill-wave text-white text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-purple-600">
                    Rp {{ number_format($paymentStats['total_amount_settled'], 0, ',', '.') }}
                </div>
                <div class="text-gray-700 font-medium">Total Transaksi</div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $paymentStats['successful_payments'] }} transaksi berhasil
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaksi Pending -->
    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 shadow-sm border border-yellow-200">
        <div class="flex items-center">
            <div class="bg-yellow-500 p-3 rounded-lg mr-4">
                <i class="fas fa-clock text-white text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-yellow-600">{{ $paymentStats['pending_payments'] }}</div>
                <div class="text-gray-700 font-medium">Pending</div>
                <div class="text-sm text-gray-500 mt-1">
                    Menunggu pembayaran
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaksi Gagal/Dibatalkan -->
    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 shadow-sm border border-red-200">
        <div class="flex items-center">
            <div class="bg-red-500 p-3 rounded-lg mr-4">
                <i class="fas fa-times-circle text-white text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-red-600">{{ $paymentStats['failed_payments'] }}</div>
                <div class="text-gray-700 font-medium">Gagal/Dibatalkan</div>
                <div class="text-sm text-gray-500 mt-1">
                    @php
                        $failedDetails = [];
                        if (isset($paymentStats['expire_count']) && $paymentStats['expire_count'] > 0) {
                            $failedDetails[] = $paymentStats['expire_count'] . ' kedaluwarsa';
                        }
                        if (isset($paymentStats['deny_count']) && $paymentStats['deny_count'] > 0) {
                            $failedDetails[] = $paymentStats['deny_count'] . ' ditolak';
                        }
                        if (isset($paymentStats['cancel_count']) && $paymentStats['cancel_count'] > 0) {
                            $failedDetails[] = $paymentStats['cancel_count'] . ' dibatalkan';
                        }
                        $failedText = !empty($failedDetails) ? implode(', ', $failedDetails) : 'Transaksi gagal';
                    @endphp
                    {{ $failedText }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan -->
{{-- <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-5 shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center mb-2">
                <i class="fas fa-chart-pie text-blue-500 text-xl mr-3"></i>
                <div>
                    <div class="text-gray-800 font-medium">Ringkasan Status Transaksi</div>
                    <div class="text-sm text-gray-600">
                        Total {{ $paymentStats['total_transactions'] }} transaksi
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            @if($paymentStats['total_transactions'] > 0)
            <div class="mt-3">
                <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                    <span>Berhasil: {{ $paymentStats['successful_payments'] }} ({{ number_format(($paymentStats['successful_payments'] / $paymentStats['total_transactions']) * 100, 1) }}%)</span>
                    <span>{{ $paymentStats['pending_payments'] }} pending • {{ $paymentStats['failed_payments'] }} gagal</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($paymentStats['successful_payments'] / $paymentStats['total_transactions']) * 100 }}%"></div>
                    <div class="bg-yellow-500 h-2 rounded-full -mt-2" style="width: {{ ($paymentStats['pending_payments'] / $paymentStats['total_transactions']) * 100 }}%; margin-left: {{ ($paymentStats['successful_payments'] / $paymentStats['total_transactions']) * 100 }}%"></div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="text-right">
            <div class="text-lg font-bold text-green-600">
                @if($paymentStats['total_transactions'] > 0)
                    {{ number_format(($paymentStats['successful_payments'] / $paymentStats['total_transactions']) * 100, 1) }}%
                @else
                    0%
                @endif
            </div>
            <div class="text-sm text-gray-500">Success Rate</div>
        </div>
    </div>
</div> --}}
@endif
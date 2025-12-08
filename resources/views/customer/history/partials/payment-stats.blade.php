@if(isset($paymentStats) && $paymentStats['total_transactions'] > 0)
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 mb-8">
    <!-- Total Transaksi -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-sm border border-blue-200">
        <div class="flex items-center">
            <div class="bg-blue-500 p-3 rounded-lg mr-4">
                <i class="fas fa-receipt text-white text-xl"></i>
            </div>
           <div>
    <div class="text-2xl font-bold text-blue-600">{{ $paymentStats['total_transactions'] }}</div>
    <div class="text-gray-700 font-medium">Total Aktivitas</div>
    {{-- <div class="text-sm text-gray-500 mt-1">
        Semua riwayat pembayaran
    </div> --}}
</div>
        </div>
    </div>
    
    <!-- Pembayaran Berhasil -->
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow-sm border border-green-200">
        <div class="flex items-center">
            <div class="bg-green-500 p-3 rounded-lg mr-4">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">{{ $paymentStats['successful_payments'] }}</div>
                <div class="text-gray-700 font-medium">Berhasil</div>
                {{-- <div class="text-sm text-gray-500 mt-1">
                    @if($paymentStats['total_transactions'] > 0)
                        {{ number_format(($paymentStats['successful_payments'] / $paymentStats['total_transactions']) * 100, 1) }}% sukses
                    @else
                        0% sukses
                    @endif
                </div> --}}
            </div>
        </div>
    </div>
    
    <!-- Total Uang -->
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 shadow-sm border border-purple-200">
        <div class="flex items-center">
            <div class="bg-purple-500 p-3 rounded-lg mr-4">
                <i class="fas fa-money-bill-wave text-white text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-purple-600">
                    Rp {{ number_format($paymentStats['total_amount'], 0, ',', '.') }}
                </div>
                <div class="text-gray-700 font-medium">Total Uang</div>
                {{-- <div class="text-sm text-gray-500 mt-1">
                    @if($paymentStats['total_transactions'] > 0)
                        Rp {{ number_format($paymentStats['average_amount'], 0, ',', '.') }} rata-rata
                    @endif
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endif
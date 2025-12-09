@if(isset($showStats) && $showStats)
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 mb-8">
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-sm border border-blue-200">
        <div class="flex items-center">
            <div class="bg-blue-500 p-3 rounded-lg mr-4">
                <i class="fas fa-receipt text-white text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-blue-600">{{ $histories->total() ?? 0 }}</div>
                <div class="text-gray-700 font-medium">Total Aktivitas</div>
            </div>
        </div>
    </div>
    
    {{-- <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow-sm border border-green-200">
        <div class="flex items-center">
            <div class="bg-green-500 p-3 rounded-lg mr-4">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">
                    {{ $paymentSuccessCount ?? 0 }}
                </div>
                <div class="text-gray-700 font-medium">Pembayaran Berhasil</div>
            </div>
        </div>
    </div> --}}
    
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 shadow-sm border border-purple-200">
        <div class="flex items-center">
            <div class="bg-purple-500 p-3 rounded-lg mr-4">
                <i class="fas fa-cube text-white text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-purple-600">
                    {{ $rakActiveCount ?? 0 }}
                </div>
                <div class="text-gray-700 font-medium">Rak Aktif</div>
            </div>
        </div>
    </div>
</div>
@endif
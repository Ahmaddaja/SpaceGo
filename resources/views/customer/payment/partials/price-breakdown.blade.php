<div class="price-breakdown rounded-xl p-6 mb-8 border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-receipt text-blue-600 mr-3"></i>
        Rincian Pembayaran
    </h3>

    <div class="space-y-4">
        <div class="flex justify-between items-center py-3 border-b border-gray-200">
            <span class="text-gray-600 flex items-center">
                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                Sewa per Bulan
            </span>
            <span class="font-semibold text-gray-800">Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}</span>
        </div>

        <div class="flex justify-between items-center py-3 border-b border-gray-200">
            <span class="text-gray-600 flex items-center">
                <i class="fas fa-cog text-green-500 mr-2"></i>
                Biaya Admin
            </span>
            <span class="font-semibold text-green-600">Gratis</span>
        </div>

        <div class="flex justify-between items-center pt-4">
            <span class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-tag text-purple-600 mr-2"></i>
                Total Pembayaran
            </span>
            <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
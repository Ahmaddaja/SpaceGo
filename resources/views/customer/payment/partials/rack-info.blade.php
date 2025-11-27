<div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 mb-8 border border-blue-100 shadow-sm">
    <div class="flex items-start justify-between">
        <div class="flex items-start space-x-4">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 rounded-xl shadow-md">
                <i class="fas fa-pallet text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium mb-1">Nama Rak</p>
                <h2 class="text-2xl font-bold text-gray-800">{{ $rak->nama_rak }}</h2>
                <p class="text-gray-600 mt-2 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                    Lokasi: {{ $rak->lokasi_gudang ?? 'Gudang Utama' }}
                </p>
            </div>
        </div>
    </div>
</div>
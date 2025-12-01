<div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-2xl border border-blue-200 spec-card">
    <div class="flex items-center space-x-3 mb-3">
        <div class="bg-blue-500 p-2 rounded-lg">
            <i class="fas fa-file-alt text-white text-sm"></i>
        </div>
        <p class="text-gray-700 text-sm font-medium">Deskripsi</p>
    </div>
    <p class="text-gray-800 font-semibold">{{ $rak->deskripsi ?? 'Tidak ada deskripsi' }}</p>
</div>

<div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-2xl border border-green-200 spec-card">
    <div class="flex items-center space-x-3 mb-3">
        <div class="bg-green-500 p-2 rounded-lg">
            <i class="fas fa-weight text-white text-sm"></i>
        </div>
        <p class="text-gray-700 text-sm font-medium">Kapasitas Berat</p>
    </div>
    <p class="text-gray-800 text-xl font-bold">{{ $rak->kapasitas_berat }} <span class="text-sm font-normal text-gray-600">kg</span></p>
</div>

<div class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-2xl border border-purple-200 spec-card">
    <div class="flex items-center space-x-3 mb-3">
        <div class="bg-purple-500 p-2 rounded-lg">
            <i class="fas fa-ruler-combined text-white text-sm"></i>
        </div>
        <p class="text-gray-700 text-sm font-medium">Dimensi (P × L × T)</p>
    </div>
    <p class="text-gray-800 font-bold text-lg">
        {{ $rak->panjang }} × {{ $rak->lebar }} × {{ $rak->tinggi }} m
        <span class="text-sm font-normal text-gray-500">
            ({{ number_format($rak->panjang * $rak->lebar * $rak->tinggi, 0, ',', '.') }} m³)
        </span>
    </p>
</div>

<div class="bg-gradient-to-br from-orange-50 to-orange-100 p-5 rounded-2xl border border-orange-200 spec-card">
    <div class="flex items-center space-x-3 mb-3">
        <div class="bg-orange-500 p-2 rounded-lg">
            <i class="fas fa-layer-group text-white text-sm"></i>
        </div>
        <p class="text-gray-700 text-sm font-medium">Jumlah Tingkat</p>
    </div>
    <p class="text-gray-800 text-xl font-bold">{{ $rak->jumlah_tingkat }} <span class="text-sm font-normal text-gray-600">tingkat</span></p>
</div>

<div class="bg-gradient-to-br from-red-50 to-red-100 p-5 rounded-2xl border border-red-200 spec-card">
    <div class="flex items-center space-x-3 mb-3">
        <div class="bg-red-500 p-2 rounded-lg">
            <i class="fas fa-map-marker-alt text-white text-sm"></i>
        </div>
        <p class="text-gray-700 text-sm font-medium">Lokasi Gudang</p>
    </div>
    <p class="text-gray-800 font-semibold">{{ $rak->lokasi_gudang }}</p>
</div>

<div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 rounded-2xl border border-indigo-200 spec-card">
    <div class="flex items-center space-x-3 mb-3">
        <div class="bg-indigo-500 p-2 rounded-lg">
            <i class="fas fa-list text-white text-sm"></i>
        </div>
        <p class="text-gray-700 text-sm font-medium">Spesifikasi Tambahan</p>
    </div>
    <p class="text-gray-800 font-semibold">{{ $rak->spesifikasi_tambahan ?? 'Tidak ada' }}</p>
</div>

<div class="bg-gradient-to-br from-teal-50 to-teal-100 p-5 rounded-2xl border border-teal-200 spec-card">
    <div class="flex items-center space-x-3 mb-3">
        <div class="bg-teal-500 p-2 rounded-lg">
            <i class="fas fa-check-circle text-white text-sm"></i>
        </div>
        <p class="text-gray-700 text-sm font-medium">Status Aktif</p>
    </div>
    <p class="text-gray-800 font-bold text-lg">
        @if($rak->is_active)
            <span class="text-green-600 flex items-center">
                <i class="fas fa-check mr-2"></i>Aktif
            </span>
        @else
            <span class="text-red-600 flex items-center">
                <i class="fas fa-times mr-2"></i>Tidak Aktif
            </span>
        @endif
    </p>
</div>
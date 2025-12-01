<div class="space-y-6">
    <!-- Kode -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-2xl border-l-4 border-blue-600">
        <p class="text-blue-700 text-sm font-medium mb-2 uppercase tracking-wide">Kode Rak</p>
        <h3 class="text-4xl font-bold text-blue-600">{{ $rak->kode_rak }}</h3>
    </div>

    <!-- Nama -->
    <div class="border-b border-gray-200 pb-4">
        <p class="text-gray-500 text-sm font-medium mb-2 flex items-center">
            <i class="fas fa-tag text-blue-500 mr-2"></i>
            Nama Rak
        </p>
        <p class="text-gray-800 text-xl font-semibold">{{ $rak->nama_rak }}</p>
    </div>

    <!-- Jenis -->
    <div class="border-b border-gray-200 pb-4">
        <p class="text-gray-500 text-sm font-medium mb-2 flex items-center">
            <i class="fas fa-layer-group text-green-500 mr-2"></i>
            Jenis Rak
        </p>
        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold">
            <i class="fas fa-pallet mr-2"></i>
            {{ $rak->jenis_rak }}
        </span>
    </div>

    <!-- Status -->
    <div class="border-b border-gray-200 pb-4">
        <p class="text-gray-500 text-sm font-medium mb-2 flex items-center">
            <i class="fas fa-info-circle text-purple-500 mr-2"></i>
            Status Ketersediaan
        </p>
        @if ($rak->status === 'tersedia')
            <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-xl font-semibold">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                <i class="fas fa-check mr-2"></i>
                Tersedia
            </span>
        @elseif($rak->status === 'terisi')
            <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-xl font-semibold">
                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                <i class="fas fa-times mr-2"></i>
                Terisi
            </span>
        @else
            <span class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl font-semibold">
                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                <i class="fas fa-tools mr-2"></i>
                Maintenance
            </span>
        @endif
    </div>

    <!-- Harga -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-2xl border-l-4 border-green-500">
        <p class="text-green-700 text-sm font-medium mb-2 flex items-center">
            <i class="fas fa-money-bill-wave mr-2"></i>
            Harga Sewa Per Bulan
        </p>
        <p class="text-green-600 text-2xl font-bold">
                            Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                            <span class="text-sm font-normal text-green-500">/{{ $rak->durasi_sewa_hari }} hari</span>
                        </p>
        <p class="text-green-600 text-sm mt-2 flex items-center">
            <i class="fas fa-info-circle mr-1"></i>
            * Harga sudah termasuk perawatan dasar
        </p>

</div>
<div class="rak-card bg-white rounded-2xl shadow-lg overflow-hidden group border border-gray-100">

    <!-- FOTO -->
    <div class="relative w-full h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
        @if ($rak->foto)
            <img src="{{ asset('storage/' . $rak->foto) }}" 
                 class="w-full h-full object-cover image-hover" 
                 alt="Foto Rak">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100">
                <i class="fas fa-pallet text-4xl text-blue-500 opacity-50"></i>
            </div>
        @endif
        
        <!-- Status Badge -->
        <div class="absolute top-4 right-4">
            @if ($rak->status === 'tersedia')
                <span class="status-badge status-available">
                    ✓ Tersedia
                </span>
            @elseif($rak->status === 'terisi')
                <span class="status-badge status-occupied">
                    ✕ Terisi
                </span>
            @else
                <span class="status-badge status-maintenance">
                    ⚙ Maintenance
                </span>
            @endif
        </div>

        <!-- Jenis Badge -->
        <div class="absolute top-4 left-4">
            <span class="status-badge type-badge">
                {{ $rak->jenis_rak }}
            </span>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="p-6">
        <!-- KODE -->
        <div class="mb-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Kode Rak</p>
            <h3 class="text-2xl font-bold text-blue-600">{{ $rak->kode_rak }}</h3>
        </div>

        <!-- INFO -->
        <div class="space-y-4 text-sm mb-4">
            <div class="flex items-start">
                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-tag text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Nama Rak</p>
                    <p class="text-gray-800 font-semibold">{{ $rak->nama_rak }}</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="bg-green-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-layer-group text-green-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Jenis Rak</p>
                    <p class="text-gray-800 font-semibold">{{ $rak->jenis_rak }}</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-map-marker-alt text-purple-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Lokasi Gudang</p>
                    <p class="text-gray-800 font-semibold">{{ $rak->gudang->alamat }}</p>
                </div>
            </div>
        </div>

        <!-- HARGA -->
        <div class="mt-5 pt-5 border-t border-gray-100">
            <p class="text-xs text-gray-500 mb-1">Harga Sewa</p>
            <p class="text-2xl font-bold text-green-600">
                Rp {{ number_format($rak->harga_sewa_perbulan, 0, ',', '.') }}
                <span class="text-sm text-gray-500 font-normal">/ bulan</span>
            </p>
        </div>

        <!-- BUTTONS -->
        <div class="mt-6 flex gap-3">
            <a href="{{ route('customer.list-rak.show', $rak->id) }}"
               class="flex-1 bg-gray-100 text-center text-gray-700 py-3 rounded-xl hover:bg-gray-200 transition-all duration-300 font-medium flex items-center justify-center space-x-2 action-button">
                <i class="fas fa-eye text-sm"></i>
                <span>Detail</span>
            </a>

            @if ($rak->status === 'tersedia')
                <a href="{{ route('customer.payment.checkout', $rak->id) }}"
                   class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-center text-white py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-md hover:shadow-lg font-medium action-button">
                    Sewa Sekarang
                </a>
            @else
                <button class="flex-1 bg-gray-300 text-gray-500 py-3 rounded-xl cursor-not-allowed font-medium flex items-center justify-center space-x-2">
                    <i class="fas fa-ban text-sm"></i>
                    <span>Tidak Tersedia</span>
                </button>
            @endif
        </div>
    </div>
</div>
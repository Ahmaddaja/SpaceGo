<div class="relative">
    <div class="rounded-2xl overflow-hidden shadow-lg h-96 bg-gradient-to-br from-gray-100 to-gray-200">
        @if ($rak->foto)
            <img src="{{ asset('storage/' . $rak->foto) }}" 
                 class="w-full h-full object-cover image-hover" 
                 alt="Foto Rak">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-pallet text-8xl text-blue-500 opacity-30"></i>
            </div>
        @endif
    </div>
    
    <!-- Status Badge on Image -->
    <div class="absolute top-4 right-4">
        @if ($rak->status === 'tersedia')
            <span class="status-badge status-available flex items-center space-x-2">
                <i class="fas fa-check text-xs"></i>
                <span>Tersedia</span>
            </span>
        @elseif($rak->status === 'terisi')
            <span class="status-badge status-occupied flex items-center space-x-2">
                <i class="fas fa-times text-xs"></i>
                <span>Terisi</span>
            </span>
        @else
            <span class="status-badge status-maintenance flex items-center space-x-2">
                <i class="fas fa-tools text-xs"></i>
                <span>Maintenance</span>
            </span>
        @endif
    </div>

    <!-- Jenis Badge -->
    <div class="absolute top-4 left-4">
        <span class="status-badge type-badge flex items-center space-x-2">
            <i class="fas fa-layer-group text-blue-500"></i>
            <span>{{ $rak->jenis_rak }}</span>
        </span>
    </div>
</div>
<div class="rak-card bg-white rounded-2xl shadow-lg overflow-hidden group border border-gray-100 relative">

    <!-- Status Ribbon -->
    @if($rak->status === 'tersedia')
    <div class="status-ribbon ribbon-available">
        <i class="fas fa-check ribbon-icon"></i>
        <span>Tersedia</span>
    </div>
    @elseif($rak->status === 'terisi')
    <div class="status-ribbon ribbon-occupied">
        <i class="fas fa-box ribbon-icon"></i>
        <span>Terisi</span>
    </div>
    @else
    <div class="status-ribbon ribbon-maintenance">
        <i class="fas fa-tools ribbon-icon"></i>
        <span>Maintenance</span>
    </div>
    @endif

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
        
        <!-- Jenis Badge -->
        <div class="absolute top-4 left-4">
            <span class="type-badge">
                <i class="fas fa-layer-group mr-1"></i>
                {{ $rak->jenis_rak }}
            </span>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="p-6">
        <!-- KODE -->
        <div class="mb-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nama Rak</p>
            <h3 class="text-2xl font-bold text-blue-600">{{ $rak->nama_rak }}</h3>
        </div>

        <!-- INFO -->
        <div class="space-y-4 text-sm mb-4">

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
                <span class="text-sm text-gray-500 font-normal">/30 hari</span>
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

@push('styles')
<style>
    .rak-card {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    /* Ribbon Styles */
    .status-ribbon {
        position: absolute;
        top: 20px;
        right: -30px;
        padding: 8px 40px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transform: rotate(45deg);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 120px;
        justify-content: center;
    }
    
    .ribbon-available {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .ribbon-occupied {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .ribbon-maintenance {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    /* Type Badge */
    .type-badge {
        background: rgba(255, 255, 255, 0.95);
        color: #374151;
        border: 1px solid #e5e7eb;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        border-radius: 9999px;
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Ribbon icon animation */
    .ribbon-icon {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Card hover effects */
    .rak-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Button hover effects */
    .action-button {
        transition: all 0.3s ease;
    }

    .action-button:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ribbon hover effect
        const ribbons = document.querySelectorAll('.status-ribbon');
        ribbons.forEach(ribbon => {
            ribbon.addEventListener('mouseenter', function() {
                this.style.transform = 'rotate(45deg) scale(1.05)';
                this.style.transition = 'all 0.3s ease';
            });
            
            ribbon.addEventListener('mouseleave', function() {
                this.style.transform = 'rotate(45deg)';
            });
        });

        // Card animation on load
        const cards = document.querySelectorAll('.rak-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endpush
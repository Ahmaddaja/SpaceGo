<div class="relative">
<!-- Status Ribbon -->
    <div class="status-ribbon ribbon-{{ $rak->status }}">
            <i class="fas 
            @if($rak->status == 'tersedia') fa-check
            @elseif($rak->status == 'terisi') fa-box
            @else fa-tools
            @endif
            "></i>
        <span>
            @if($rak->status == 'tersedia') Tersedia
            @elseif($rak->status == 'terisi') Terisi
            @else Maintenance
            @endif
        </span>
    </div>

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

    <!-- Jenis Badge -->
    <div class="absolute top-4 left-4">
        <span class="type-badge flex items-center space-x-2">
            <i class="fas fa-layer-group text-blue-500"></i>
            <span>{{ $rak->jenis_rak }}</span>
        </span>
    </div>
</div>

@push('styles')
<style>
    .relative {
        position: relative;
        overflow: hidden;
    }

    /* RIBBON CLEAN BIG STYLE */
    .status-ribbon {
        position: absolute;
        top: 18px;
        right: -60px; /* tarik ke kanan biar full */
        transform: rotate(45deg);
        padding: 14px 75px; /* ukuran lebih besar */
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 30;
    }

    /* Warna status */
    .ribbon-tersedia {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .ribbon-terisi {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    .ribbon-maintenance {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    /* Icon biar stabil */
    .status-ribbon i {
        font-size: 1rem;
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Ribbon icon animation */
    .ribbon-icon {
        animation: pulse 2s infinite;
        font-size: 0.8rem;
    }
    
    @keyframes pulse {
        0%, 100% { 
            transform: scale(1); 
        }
        50% { 
            transform: scale(1.1); 
        }
    }

    /* Image hover effect */
    .image-hover {
        transition: transform 0.3s ease;
    }

    .image-hover:hover {
        transform: scale(1.05);
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
                this.style.boxShadow = '0 6px 20px rgba(0, 0, 0, 0.25)';
            });
            
            ribbon.addEventListener('mouseleave', function() {
                this.style.transform = 'rotate(45deg)';
                this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
            });
        });

        // Type badge hover effect
        const typeBadges = document.querySelectorAll('.type-badge');
        typeBadges.forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
                this.style.transition = 'all 0.2s ease';
            });
            
            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>
@endpush
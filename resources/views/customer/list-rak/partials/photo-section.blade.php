<div class="relative">
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

    /* Ribbon Styles */
    .status-ribbon {
        position: absolute;
        top: 20px;
        right: -30px;
        padding: 10px 40px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transform: rotate(45deg);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 130px;
        justify-content: center;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .ribbon-available {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .ribbon-occupied {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .ribbon-maintenance {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
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
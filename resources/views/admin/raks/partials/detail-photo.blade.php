<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-images mr-2"></i>Foto Rak
        </h5>
    </div>
    <div class="card-body">
        @if ($rak->fotos && $rak->fotos->count() > 0)
            <!-- Main Photo -->
            <div class="mb-3">
                @php
                    $mainPhoto = $rak->fotos->where('is_primary', true)->first() ?? $rak->fotos->first();
                @endphp
                <img src="{{ asset('storage/' . $mainPhoto->path) }}" class="img-fluid rounded w-100" id="mainPhoto"
                    alt="Foto Utama {{ $rak->nama_rak }}" style="max-height: 400px; object-fit: cover;">
            </div>

            <!-- Thumbnail Gallery -->
            @if ($rak->fotos->count() > 1)
                <div class="row">
                    @foreach ($rak->fotos as $foto)
                        <div class="col-3 mb-2">
                            <img src="{{ asset('storage/' . $foto->path) }}" class="img-thumbnail thumbnail-gallery"
                                alt="Foto {{ $loop->iteration }}"
                                onclick="changeMainPhoto('{{ asset('storage/' . $foto->path) }}')"
                                style="cursor: pointer; height: 80px; object-fit: cover; width: 100%;">
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-3 text-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    Total {{ $rak->fotos->count() }} foto
                    @if ($rak->fotos->count() > 1)
                        - Klik thumbnail untuk melihat
                    @endif
                </small>
            </div>
        @elseif($rak->foto)
            <!-- Fallback to old single photo -->
            <img src="{{ asset('storage/' . $rak->foto) }}" class="img-fluid rounded w-100"
                alt="Foto {{ $rak->nama_rak }}" style="max-height: 400px; object-fit: cover;">
        @else
            <div class="text-center py-5">
                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada foto tersedia</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        function changeMainPhoto(src) {
            const mainPhoto = document.getElementById('mainPhoto');
            mainPhoto.style.opacity = '0.5';

            setTimeout(() => {
                mainPhoto.src = src;
                mainPhoto.style.opacity = '1';
            }, 200);

            // Update active state on thumbnails
            document.querySelectorAll('.thumbnail-gallery').forEach(thumb => {
                thumb.classList.remove('border-primary');
                thumb.style.borderWidth = '1px';
            });

            event.target.classList.add('border-primary');
            event.target.style.borderWidth = '3px';
        }

        // Set initial active thumbnail
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnails = document.querySelectorAll('.thumbnail-gallery');
            if (thumbnails.length > 0) {
                thumbnails[0].classList.add('border-primary');
                thumbnails[0].style.borderWidth = '3px';
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        #mainPhoto {
            transition: opacity 0.3s ease;
        }

        .thumbnail-gallery {
            transition: all 0.3s ease;
        }

        .thumbnail-gallery:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
@endpush

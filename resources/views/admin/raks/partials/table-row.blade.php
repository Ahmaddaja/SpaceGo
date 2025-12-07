<tr>
    <td class="align-middle">
        <span class="font-weight-bold text-primary">{{ $rak->kode_rak }}</span>
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            @php
                // Prioritas: gunakan fotos (multiple) jika ada, fallback ke foto (single)
                $hasMultiplePhotos = $rak->fotos && $rak->fotos->count() > 0;
                $primaryPhoto = null;

                if ($hasMultiplePhotos) {
                    $primaryPhoto = $rak->fotos->where('is_primary', true)->first() ?? $rak->fotos->first();
                } elseif ($rak->foto) {
                    $primaryPhoto = $rak->foto;
                }
            @endphp

            @if ($primaryPhoto)
                @if (is_string($primaryPhoto))
                    {{-- Old single photo format --}}
                    <img src="{{ asset('storage/' . $primaryPhoto) }}" alt="{{ $rak->nama_rak }}" class="rounded mr-2"
                        style="width: 50px; height: 50px; object-fit: cover;">
                @else
                    {{-- New multiple photos format with click-to-cycle --}}
                    <div class="position-relative mr-2"
                        @if ($rak->fotos->count() > 1) style="cursor: pointer;" 
                            onclick="cyclePhoto(this, {{ $rak->id }})"
                            title="Klik untuk ganti foto" @endif>
                        <img src="{{ asset('storage/' . $primaryPhoto->path) }}" alt="{{ $rak->nama_rak }}"
                            class="rounded rak-photo-thumbnail" id="photo-{{ $rak->id }}" data-current-index="0"
                            data-photos='@json($rak->fotos->pluck('path')->toArray())'
                            style="width: 50px; height: 50px; object-fit: cover; transition: opacity 0.3s ease;">

                        @if ($rak->fotos->count() > 1)
                            <span class="badge badge-primary badge-pill position-absolute photo-counter"
                                id="counter-{{ $rak->id }}"
                                style="top: -5px; right: -5px; font-size: 0.65rem; padding: 2px 6px;">
                                <i class="fas fa-images"></i> <span
                                    class="current-num">1</span>/{{ $rak->fotos->count() }}
                            </span>
                        @endif
                    </div>
                @endif
            @else
                <div class="bg-secondary rounded d-flex align-items-center justify-content-center mr-2 text-white"
                    style="width: 50px; height: 50px; min-width: 50px;">
                    <i class="fas fa-box"></i>
                </div>
            @endif

            <div>
                <div class="font-weight-bold">{{ $rak->nama_rak }}</div>
                <small class="text-muted">{{ Str::limit($rak->deskripsi, 30) }}</small>
            </div>
        </div>
    </td>
    <td class="align-middle">
        <span class="badge badge-info">{{ $rak->jenis_rak }}</span>
    </td>
    <td class="align-middle">
        <div>{{ $rak->lokasi_gudang }}</div>
        {{-- @if ($rak->zona_gudang)
        <small class="text-muted">Zona: {{ $rak->zona_gudang }}</small>
        @endif --}}
    </td>
    <td class="align-middle">
        <small>
            {{ $rak->panjang }}m × {{ $rak->lebar }}m × {{ $rak->tinggi }}m<br>
            <span class="text-muted">{{ $rak->jumlah_tingkat }} tingkat</span>
        </small>
    </td>
    <td class="align-middle">
        <strong>{{ number_format($rak->kapasitas_berat) }} kg</strong>
    </td>
    <td class="align-middle">
        <strong class="text-success">{{ $rak->harga_format }}</strong>
    </td>
    <td class="align-middle">
        @if ($rak->status == 'tersedia')
            <span class="badge badge-success px-3 py-2">Tersedia</span>
        @elseif($rak->status == 'terisi')
            <span class="badge badge-warning px-3 py-2">Terisi</span>
        @else
            <span class="badge badge-danger px-3 py-2">Maintenance</span>
        @endif
    </td>
    <td class="align-middle text-center">
        <div class="btn-group" role="group">
            <a href="{{ route('raks.show', $rak->id) }}" class="btn btn-sm btn-info" title="Detail">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('raks.edit', $rak->id) }}" class="btn btn-sm btn-warning" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $rak->id }})"
                title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <form id="delete-form-{{ $rak->id }}" action="{{ route('raks.destroy', $rak->id) }}" method="POST"
            style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </td>
</tr>

@push('scripts')
    <script>
        function cyclePhoto(element, rakId) {
            const img = document.getElementById('photo-' + rakId);
            const counter = document.getElementById('counter-' + rakId);

            if (!img) return;

            // Get photos array from data attribute
            const photos = JSON.parse(img.getAttribute('data-photos'));

            if (photos.length <= 1) return;

            // Get current index
            let currentIndex = parseInt(img.getAttribute('data-current-index'));

            // Calculate next index (loop back to 0 after last photo)
            let nextIndex = (currentIndex + 1) % photos.length;

            // Fade out effect
            img.style.opacity = '0.3';

            // Change photo after short delay
            setTimeout(() => {
                img.src = "{{ asset('storage') }}/" + photos[nextIndex];
                img.setAttribute('data-current-index', nextIndex);

                // Update counter
                if (counter) {
                    counter.querySelector('.current-num').textContent = nextIndex + 1;
                }

                // Fade in
                img.style.opacity = '1';
            }, 150);

            // Add a subtle scale effect
            element.style.transform = 'scale(0.95)';
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 150);
        }
    </script>
@endpush

@push('styles')
    <style>
        .rak-photo-thumbnail {
            transition: opacity 0.3s ease, transform 0.2s ease;
        }

        .rak-photo-thumbnail:hover {
            opacity: 0.9;
        }

        div[onclick*="cyclePhoto"] {
            transition: transform 0.15s ease;
        }

        div[onclick*="cyclePhoto"]:hover {
            transform: scale(1.05);
        }

        div[onclick*="cyclePhoto"]:active {
            transform: scale(0.95);
        }

        .photo-counter {
            pointer-events: none;
            user-select: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
    </style>
@endpush

{{-- resources/views/admin/raks/partials/form-photo.blade.php --}}

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-images mr-2 text-primary"></i>Foto Rak
        </h5>
    </div>
    <div class="card-body">
        @if (isset($rak) && $rak->fotos->count() > 0)
            {{-- Preview existing photos for edit mode --}}
            <div class="mb-3">
                <label class="font-weight-bold mb-2">Foto Saat Ini</label>
                <div class="alert alert-info alert-sm mb-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    <small>
                        Total foto: <strong>{{ $rak->fotos->count() }}/4</strong>
                        @if ($rak->fotos->count() < 4)
                            | Anda dapat menambah <strong>{{ 4 - $rak->fotos->count() }}</strong> foto lagi
                        @else
                            | <strong>Maksimal tercapai</strong>
                        @endif
                    </small>
                </div>

                <div class="row">
                    @foreach ($rak->fotos->sortBy('urutan') as $foto)
                        <div class="col-6 mb-3">
                            <div class="position-relative border rounded p-2 bg-light">
                                <img src="{{ asset('storage/' . $foto->path) }}" class="img-fluid rounded"
                                    alt="Foto Rak" style="height: 120px; width: 100%; object-fit: cover;">

                                {{-- Primary badge --}}
                                @if ($foto->is_primary)
                                    <span class="badge badge-success position-absolute" style="top: 10px; left: 10px;">
                                        <i class="fas fa-star mr-1"></i>Primary
                                    </span>
                                @endif

                                {{-- Action buttons --}}
                                <div class="mt-2">
                                    <div class="custom-control custom-checkbox d-inline-block">
                                        <input type="checkbox" class="custom-control-input" name="delete_fotos[]"
                                            value="{{ $foto->id }}" id="delete_foto_{{ $foto->id }}">
                                        <label class="custom-control-label small" for="delete_foto_{{ $foto->id }}">
                                            Hapus
                                        </label>
                                    </div>

                                    @if (!$foto->is_primary)
                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                            <input type="radio" class="custom-control-input" name="foto_primary"
                                                value="{{ $foto->id }}" id="primary_{{ $foto->id }}">
                                            <label class="custom-control-label small" for="primary_{{ $foto->id }}">
                                                Set Primary
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <hr class="my-3">
        @endif

        {{-- Upload new photos --}}
        <div class="mb-3">
            <label for="fotos" class="font-weight-bold">
                {{ isset($rak) ? 'Tambah Foto Baru' : 'Upload Foto' }}
                <span class="text-danger">*</span>
            </label>

            {{-- Info counter --}}
            <div class="mb-2 p-2 border rounded bg-light">
                <small class="text-muted">
                    Foto yang dipilih: <strong id="foto-count">{{ isset($rak) ? $rak->fotos->count() : 0 }}</strong> |
                    Foto saat ini: <strong>{{ isset($rak) ? $rak->fotos->count() : 0 }}/4</strong> |
                    Batas maksimal: <strong>4 foto</strong>
                </small>
            </div>

            {{-- Info alert --}}
            <div class="alert alert-info alert-sm mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                <small>
                    Format: JPG, JPEG, PNG | Ukuran maksimal: 2MB per foto
                </small>
            </div>

            <div class="custom-file">
                <input type="file" class="custom-file-input @error('fotos.*') is-invalid @enderror" id="fotos"
                    name="fotos[]" multiple accept="image/jpeg,image/png,image/jpg"
                    onchange="previewMultipleImages(event)">
                <label class="custom-file-label" for="fotos" data-browse="Pilih">
                    Pilih foto (maksimal 4)
                </label>
            </div>

            @error('fotos')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            @error('fotos.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            <small class="form-text text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Foto pertama akan menjadi foto utama jika belum ada foto primary.
            </small>
        </div>

        {{-- Preview container for new photos --}}
        <div id="preview-container" class="row mt-3" style="display: none;"></div>
    </div>
</div>

@push('scripts')
    <script>
        function previewMultipleImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('preview-container');
            const fileLabel = event.target.nextElementSibling;
            const fotoCounter = document.getElementById('foto-count');

            // Clear previous previews
            previewContainer.innerHTML = '';

            if (files.length > 0) {
                // Check maximum photos
                const existingPhotos = {{ isset($rak) ? $rak->fotos->count() : 0 }};
                const maxAllowed = 4 - existingPhotos;
                const totalPhotos = existingPhotos + files.length;

                // Update counter
                if (fotoCounter) {
                    fotoCounter.textContent = totalPhotos;
                }

                if (files.length > maxAllowed) {
                    alert('Maksimal 4 foto!\n\nFoto saat ini: ' + existingPhotos + '\nFoto yang dipilih: ' + files.length +
                        '\nBatas maksimal: 4 foto');
                    event.target.value = '';
                    fileLabel.textContent = 'Pilih foto (maksimal 4)';
                    previewContainer.style.display = 'none';
                    if (fotoCounter) {
                        fotoCounter.textContent = existingPhotos;
                    }
                    return;
                }

                // Update label
                if (files.length === 1) {
                    fileLabel.textContent = files[0].name;
                } else {
                    fileLabel.textContent = files.length + ' foto dipilih';
                }

                // Check file size and type
                let validFiles = true;
                let errorMessage = '';

                Array.from(files).forEach((file, index) => {
                    // Check file size (2MB = 2048KB)
                    if (file.size > 2048 * 1024) {
                        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        errorMessage += '\n• ' + file.name + ' terlalu besar (' + fileSizeMB + ' MB)';
                        validFiles = false;
                        return;
                    }

                    // Check file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        const fileExt = file.name.split('.').pop().toUpperCase();
                        errorMessage += '\n• ' + file.name + ' format tidak didukung (.' + fileExt + ')';
                        validFiles = false;
                        return;
                    }

                    // Create preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-6 mb-3';
                        col.innerHTML = `
                    <div class="border rounded p-2 bg-light">
                        <img src="${e.target.result}" 
                             class="img-fluid rounded" 
                             alt="Preview"
                             style="height: 120px; width: 100%; object-fit: cover;">
                        <small class="d-block mt-2 text-truncate">${file.name}</small>
                        <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                    </div>
                `;
                        previewContainer.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                });

                if (!validFiles) {
                    alert('File tidak valid!' + errorMessage + '\n\nFormat: JPG, JPEG, PNG | Maksimal: 2MB per file');
                    event.target.value = '';
                    fileLabel.textContent = 'Pilih foto (maksimal 4)';
                    previewContainer.style.display = 'none';
                    if (fotoCounter) {
                        fotoCounter.textContent = existingPhotos;
                    }
                    return;
                }

                previewContainer.style.display = 'flex';
            } else {
                fileLabel.textContent = 'Pilih foto (maksimal 4)';
                previewContainer.style.display = 'none';
                const existingPhotos = {{ isset($rak) ? $rak->fotos->count() : 0 }};
                if (fotoCounter) {
                    fotoCounter.textContent = existingPhotos;
                }
            }
        }

        // Update file input label on page load if needed
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fotos');
            if (fileInput && fileInput.files.length > 0) {
                const fileLabel = fileInput.nextElementSibling;
                if (fileInput.files.length === 1) {
                    fileLabel.textContent = fileInput.files[0].name;
                } else {
                    fileLabel.textContent = fileInput.files.length + ' foto dipilih';
                }
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .alert-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .custom-file-label::after {
            content: "Pilih";
        }

        .position-absolute {
            position: absolute !important;
        }

        /* Preview image hover effect */
        #preview-container img:hover {
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        /* Custom checkbox and radio styling */
        .custom-control-label.small {
            font-size: 0.875rem;
            padding-top: 0.125rem;
        }
    </style>
@endpush

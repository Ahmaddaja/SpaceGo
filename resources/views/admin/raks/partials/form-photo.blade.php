<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-images mr-2"></i>Foto Rak
            @if (!isset($rak))
                <span class="badge badge-info ml-2">Bisa upload multiple</span>
            @endif
        </h5>
    </div>
    <div class="card-body">
        @if (isset($rak) && $rak->fotos && $rak->fotos->count() > 0)
            <!-- Existing Photos (Only on Edit) -->
            <div class="mb-3">
                <label class="form-label font-weight-bold">Foto Saat Ini:</label>
                <div class="row" id="existing-photos">
                    @foreach ($rak->fotos as $foto)
                        <div class="col-6 mb-3" data-foto-id="{{ $foto->id }}">
                            <div class="card">
                                <img src="{{ asset('storage/' . $foto->path) }}" class="card-img-top" alt="Foto Rak"
                                    style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="foto_primary"
                                            value="{{ $foto->id }}" id="primary_{{ $foto->id }}"
                                            {{ $foto->is_primary ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="primary_{{ $foto->id }}">
                                            <i class="fas fa-star text-warning"></i> Foto Utama
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="delete_fotos[]"
                                            value="{{ $foto->id }}" id="delete_{{ $foto->id }}">
                                        <label class="form-check-label small text-danger"
                                            for="delete_{{ $foto->id }}">
                                            <i class="fas fa-trash"></i> Hapus foto ini
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <hr>
        @endif

        <!-- Upload New Photos -->
        <div class="form-group">
            <label for="fotos" class="font-weight-bold">
                @if (isset($rak))
                    <i class="fas fa-plus-circle"></i> Tambah Foto Baru
                @else
                    <i class="fas fa-camera"></i> Upload Foto Rak
                @endif
                <span class="text-muted small">(Bisa pilih lebih dari 1 foto)</span>
            </label>

            <div class="custom-file">
                <input type="file" class="custom-file-input @error('fotos.*') is-invalid @enderror" id="fotos"
                    name="fotos[]" accept="image/jpeg,image/png,image/jpg" multiple
                    onchange="previewMultipleImages(event)">
                <label class="custom-file-label" for="fotos" id="file-label">
                    Pilih foto...
                </label>
            </div>

            <small class="form-text text-muted mt-2">
                <i class="fas fa-info-circle"></i>
                <strong>Tips:</strong> Tekan <kbd>Ctrl</kbd> (Windows) atau <kbd>Cmd</kbd> (Mac) untuk memilih beberapa
                foto sekaligus.
                <br>
                <i class="fas fa-check-circle text-success"></i> Format: JPG, JPEG, PNG | Maksimal: 2MB per foto
            </small>

            @error('fotos.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Preview New Photos -->
        <div id="preview-container" class="mt-3" style="display: none;">
            <div class="alert alert-info">
                <i class="fas fa-images"></i>
                <strong id="preview-count">0</strong> foto dipilih:
            </div>
            <div class="row" id="preview-images"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function previewMultipleImages(event) {
            const previewContainer = document.getElementById('preview-container');
            const previewImages = document.getElementById('preview-images');
            const previewCount = document.getElementById('preview-count');
            const fileLabel = document.getElementById('file-label');
            const files = event.target.files;

            // Clear previous previews
            previewImages.innerHTML = '';

            if (files.length > 0) {
                previewContainer.style.display = 'block';
                previewCount.textContent = files.length;

                // Update file label
                if (files.length === 1) {
                    fileLabel.textContent = files[0].name;
                } else {
                    fileLabel.textContent = files.length + ' foto dipilih';
                }

                Array.from(files).forEach((file, index) => {
                    if (file.type.match('image.*')) {
                        // Check file size (2MB = 2097152 bytes)
                        if (file.size > 2097152) {
                            const col = document.createElement('div');
                            col.className = 'col-6 col-md-4 col-lg-3 mb-3';
                            col.innerHTML = `
                        <div class="card border-danger">
                            <div class="card-body p-2 text-center">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                                <p class="small mb-0 text-danger">
                                    <strong>${file.name}</strong><br>
                                    Ukuran terlalu besar (${(file.size / 1024 / 1024).toFixed(2)}MB)
                                </p>
                            </div>
                        </div>
                    `;
                            previewImages.appendChild(col);
                            return;
                        }

                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-6 col-md-4 col-lg-3 mb-3';

                            const fileSize = (file.size / 1024).toFixed(1); // KB

                            col.innerHTML = `
                        <div class="card h-100">
                            <img src="${e.target.result}" 
                                 class="card-img-top" 
                                 style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <small class="text-muted d-block text-truncate" title="${file.name}">
                                    <i class="fas fa-image"></i> ${file.name}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-weight"></i> ${fileSize} KB
                                </small>
                                ${index === 0 ? '<div class="badge badge-primary badge-sm mt-1 w-100"><i class="fas fa-star"></i> Foto Utama</div>' : ''}
                            </div>
                        </div>
                    `;

                            previewImages.appendChild(col);
                        };

                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewContainer.style.display = 'none';
                fileLabel.textContent = 'Pilih foto...';
            }
        }

        // Handle delete checkbox warning (only on edit)
        document.addEventListener('DOMContentLoaded', function() {
            const deleteCheckboxes = document.querySelectorAll('input[name="delete_fotos[]"]');

            deleteCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const card = this.closest('.card');
                    if (this.checked) {
                        card.style.opacity = '0.5';
                        card.style.border = '2px solid #dc3545';
                    } else {
                        card.style.opacity = '1';
                        card.style.border = '';
                    }
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        #existing-photos .card {
            transition: all 0.3s ease;
        }

        #existing-photos .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-check-input:checked+.form-check-label {
            font-weight: bold;
        }

        #preview-images .card {
            transition: all 0.3s ease;
        }

        #preview-images .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .custom-file-label::after {
            content: "Browse";
        }

        kbd {
            padding: 2px 6px;
            font-size: 11px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }

        .badge-sm {
            font-size: 0.7rem;
        }
    </style>
@endpush

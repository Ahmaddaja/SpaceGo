{{-- resources/views/admin/raks/partials/form-photo.blade.php --}}

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-images mr-2 text-primary"></i>Foto Rak
        </h5>
    </div>
    <div class="card-body">
        {{-- PERBAIKAN: Container selalu ada, meskipun kosong --}}
        <div class="mb-3" id="existing-photos-section" @if (!isset($rak) || $rak->fotos->count() == 0) style="display:none;" @endif>
            <label class="font-weight-bold mb-2">Foto Saat Ini</label>
            <div class="alert alert-info alert-sm mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                <small>
                    Total foto: <strong
                        id="existing-photos-count">{{ isset($rak) ? $rak->fotos->count() : 0 }}</strong>/4
                    @if (isset($rak) && $rak->fotos->count() < 4)
                        | Anda dapat menambah <strong id="remaining-slots">{{ 4 - $rak->fotos->count() }}</strong> foto
                        lagi
                    @elseif(isset($rak) && $rak->fotos->count() >= 4)
                        | <strong>Maksimal tercapai</strong>
                    @endif
                </small>
            </div>

            {{-- PERBAIKAN: Container SELALU ada --}}
            <div class="row" id="existing-photos-container">
                @if (isset($rak))
                    @foreach ($rak->fotos->sortBy('urutan') as $foto)
                        <div class="col-6 col-md-3 mb-3 existing-photo-item" data-foto-id="{{ $foto->id }}">
                            <div class="photo-wrapper position-relative square-photo-container">
                                <img src="{{ asset('storage/' . $foto->path) }}" class="square-photo-img"
                                    alt="Foto Rak">

                                <button type="button" class="btn-remove-photo position-absolute"
                                    data-foto-id="{{ $foto->id }}" onclick="deletePhotoInstant({{ $foto->id }})"
                                    title="Hapus foto"
                                    style="top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%; border: none; background: rgba(239, 68, 68, 0.95); color: white; cursor: pointer; z-index: 10; opacity: 0; transition: all 0.2s ease;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        @if (isset($rak) && $rak->fotos->count() > 0)
            <hr class="my-3">
        @endif

        {{-- Upload new photos --}}
        <div class="mb-3">
            <label for="fotos" class="font-weight-bold">
                {{ isset($rak) ? 'Tambah Foto Baru' : 'Upload Foto' }}
                <span class="text-danger">*</span>
            </label>

            <div class="mb-2 p-2 border rounded bg-light">
                <small class="text-muted">
                    Foto saat ini: <strong
                        id="current-foto-count">{{ isset($rak) ? $rak->fotos->count() : 0 }}</strong>/4 |
                    Batas maksimal: <strong>4 foto</strong>
                </small>
            </div>

            <div class="alert alert-info alert-sm mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                <small>
                    Format: JPG, JPEG, PNG | Ukuran maksimal: 2MB per foto |
                    <strong>Foto akan langsung tersimpan</strong>
                </small>
            </div>

            <div class="custom-file">
                <input type="file" class="custom-file-input @error('fotos.*') is-invalid @enderror" id="fotos"
                    name="fotos[]" multiple accept="image/jpeg,image/png,image/jpg"
                    onchange="uploadPhotosInstant(event)" @if (isset($rak) && $rak->fotos->count() >= 4) disabled @endif>
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
        </div>

        <div id="upload-loading" class="text-center py-3" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Uploading...</span>
            </div>
            <p class="mt-2 text-muted">Mengupload foto...</p>
        </div>
    </div>
</div>


@push('styles')
    <style>
        .alert-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .mini-alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .custom-file-label::after {
            content: "Pilih";
        }

        .photo-wrapper {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .photo-wrapper:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .square-photo-container {
            position: relative;
            width: 100%;
            padding-bottom: 100%;
            overflow: hidden;
        }

        .square-photo-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-remove-photo:hover {
            background: rgba(220, 38, 38, 1) !important;
            transform: scale(1.1);
        }

        .existing-photo-item {
            transition: all 0.3s ease;
        }

        #upload-loading {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
        }
    </style>
@endpush

@include('admin.raks.partials.scripts')
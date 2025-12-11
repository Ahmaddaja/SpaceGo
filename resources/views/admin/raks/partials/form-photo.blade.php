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

@push('scripts')
    <script>
        const rakId = {{ isset($rak) ? $rak->id : 'null' }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function uploadPhotosInstant(event) {
            const files = Array.from(event.target.files);
            const existingPhotos = document.querySelectorAll('.existing-photo-item').length;
            const maxAllowed = 4 - existingPhotos;

            if (existingPhotos >= 4) {
                showAlert('Maksimal 4 foto sudah tercapai!', 'error');
                event.target.value = '';
                return;
            }

            if (files.length > maxAllowed) {
                showAlert(`Maksimal ${maxAllowed} foto lagi! Anda memilih ${files.length} foto.`, 'error');
                event.target.value = '';
                return;
            }

            let validFiles = [];
            for (let file of files) {
                if (file.size > 2048 * 1024) {
                    showAlert(`${file.name} terlalu besar (max 2MB)`, 'error');
                    continue;
                }

                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showAlert(`${file.name} format tidak didukung`, 'error');
                    continue;
                }

                validFiles.push(file);
            }

            if (validFiles.length === 0) {
                event.target.value = '';
                return;
            }

            if (rakId) {
                uploadToServer(validFiles);
            } else {
                showAlert('Simpan rak terlebih dahulu sebelum upload foto', 'warning');
            }

            event.target.value = '';
        }

        function uploadToServer(files) {
            const formData = new FormData();
            files.forEach((file) => {
                formData.append('fotos[]', file);
            });

            const loadingDiv = document.getElementById('upload-loading');
            loadingDiv.style.display = 'block';
            loadingDiv.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Uploading...</span>
                </div>
                <p class="mt-2 text-muted">Mengupload ${files.length} foto... <span id="upload-progress">0%</span></p>
            `;
            document.getElementById('fotos').disabled = true;

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    const progressEl = document.getElementById('upload-progress');
                    if (progressEl) {
                        progressEl.textContent = percentComplete + '%';
                    }
                }
            });

            xhr.addEventListener('load', function() {
                loadingDiv.style.display = 'none';
                document.getElementById('fotos').disabled = false;

                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);

                        if (data.success) {
                            showAlert(data.message, 'success');

                            const section = document.getElementById('existing-photos-section');
                            if (section && section.style.display === 'none') {
                                section.style.display = 'block';
                            }
                            // GANTI MENJADI:
                            if (Array.isArray(data.fotos) && data.fotos.length > 0) {
                                data.fotos.forEach(foto => {
                                    addPhotoToContainer(foto);
                                    // ✅ TRACK foto yang baru diupload
                                    if (typeof uploadedPhotosInSession !== 'undefined') {
                                        uploadedPhotosInSession.push(foto.id);
                                    }
                                });
                            }

                            updatePhotoCounters(data.total_photos);

                            if (data.total_photos >= 4) {
                                document.getElementById('fotos').disabled = true;
                                document.querySelector('.custom-file-label').textContent = 'Maksimal foto tercapai';
                            }
                        } else {
                            showAlert(data.message || 'Upload gagal', 'error');
                        }
                    } catch (e) {
                        showAlert('Terjadi kesalahan saat memproses respons (JSON Error)', 'error');
                    }
                } else {
                    let errorMessage = `Upload gagal. Status: ${xhr.status}.`;

                    try {
                        const errorData = JSON.parse(xhr.responseText);

                        if (xhr.status === 422 && errorData.errors) {
                            const firstErrorKey = Object.keys(errorData.errors)[0];
                            errorMessage = errorData.errors[firstErrorKey][0];
                        } else if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        if (xhr.status === 419) {
                            errorMessage =
                                'Token Keamanan (CSRF) Kedaluwarsa. Mohon refresh halaman dan coba lagi.';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Validasi server gagal. Kemungkinan batasan PHP/server tidak sesuai.';
                        } else {
                            errorMessage = `Upload gagal (Server Error ${xhr.status}). Cek log aplikasi.`;
                        }
                    }

                    showAlert(errorMessage, 'error');
                }
            });

            xhr.addEventListener('error', function() {
                loadingDiv.style.display = 'none';
                document.getElementById('fotos').disabled = false;
                showAlert('Terjadi kesalahan saat upload. Periksa koneksi internet Anda.', 'error');
            });

            xhr.addEventListener('timeout', function() {
                loadingDiv.style.display = 'none';
                document.getElementById('fotos').disabled = false;
                showAlert('Upload timeout. Coba lagi atau pilih file yang lebih kecil.', 'error');
            });

            xhr.open('POST', `/raks/${rakId}/upload-photos`);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.timeout = 60000;
            xhr.send(formData);
        }

        function addPhotoToContainer(foto) {
            let container = document.getElementById('existing-photos-container');

            if (!container) {
                const section = document.getElementById('existing-photos-section');
                if (section) {
                    container = document.createElement('div');
                    container.className = 'row';
                    container.id = 'existing-photos-container';
                    section.appendChild(container);
                    section.style.display = 'block';
                } else {
                    return;
                }
            }

            const col = document.createElement('div');
            col.className = 'col-6 col-md-3 mb-3 existing-photo-item';
            col.setAttribute('data-foto-id', foto.id);
            col.style.opacity = '0';
            col.style.transform = 'scale(0.8)';

            col.innerHTML = `
                <div class="photo-wrapper position-relative square-photo-container">
                    <img src="${foto.url}" class="square-photo-img" alt="Foto Rak">
                    <button type="button" class="btn-remove-photo position-absolute"
                        data-foto-id="${foto.id}" onclick="deletePhotoInstant(${foto.id})"
                        title="Hapus foto"
                        style="top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%; border: none; background: rgba(239, 68, 68, 0.95); color: white; cursor: pointer; z-index: 10; opacity: 0; transition: all 0.2s ease;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            container.appendChild(col);

            setTimeout(() => {
                col.style.transition = 'all 0.3s ease';
                col.style.opacity = '1';
                col.style.transform = 'scale(1)';
            }, 10);
        }

        function deletePhotoInstant(fotoId) {
            if (!confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
                return;
            }

            const photoItem = document.querySelector(`.existing-photo-item[data-foto-id="${fotoId}"]`);

            if (photoItem) {
                photoItem.style.transition = 'all 0.3s ease';
                photoItem.style.opacity = '0';
                photoItem.style.transform = 'scale(0.8)';
            }

            fetch(`/raks/photos/${fotoId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // GANTI MENJADI:
                    if (data.success) {
                        setTimeout(() => {
                            if (photoItem) {
                                photoItem.remove();
                            }

                            // ✅ HAPUS dari tracking jika foto dihapus manual
                            if (typeof uploadedPhotosInSession !== 'undefined') {
                                const index = uploadedPhotosInSession.indexOf(fotoId);
                                if (index > -1) {
                                    uploadedPhotosInSession.splice(index, 1);
                                }
                            }

                            updatePhotoCounters(data.total_photos);
                            // Sembunyikan section jika tidak ada foto
                            if (data.total_photos === 0) {
                                const section = document.getElementById('existing-photos-section');
                                if (section) section.style.display = 'none';
                            }

                            if (data.total_photos < 4) {
                                document.getElementById('fotos').disabled = false;
                                document.querySelector('.custom-file-label').textContent =
                                    'Pilih foto (maksimal 4)';
                            }
                        }, 300);

                        showAlert('Foto berhasil dihapus', 'success');
                    } else {
                        if (photoItem) {
                            photoItem.style.opacity = '1';
                            photoItem.style.transform = 'scale(1)';
                        }
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (photoItem) {
                        photoItem.style.opacity = '1';
                        photoItem.style.transform = 'scale(1)';
                    }
                    showAlert('Terjadi kesalahan', 'error');
                });
        }

        function updatePhotoCounters(totalPhotos) {
            const existingCount = document.getElementById('existing-photos-count');
            const remainingSlots = document.getElementById('remaining-slots');
            const currentCount = document.getElementById('current-foto-count');

            if (existingCount) existingCount.textContent = totalPhotos;
            if (currentCount) currentCount.textContent = totalPhotos;

            if (remainingSlots) {
                const remaining = 4 - totalPhotos;
                if (remaining > 0) {
                    remainingSlots.textContent = remaining;
                    remainingSlots.parentElement.innerHTML =
                        `| Anda dapat menambah <strong id="remaining-slots">${remaining}</strong> foto lagi`;
                } else {
                    remainingSlots.parentElement.innerHTML = '| <strong>Maksimal tercapai</strong>';
                }
            }
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className =
                `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} alert-dismissible fade show mini-alert`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;

            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertDiv, cardBody.firstChild);

            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        document.addEventListener('mouseenter', function(e) {
            if (e.target.closest('.photo-wrapper')) {
                const btn = e.target.closest('.photo-wrapper').querySelector('.btn-remove-photo');
                if (btn) btn.style.opacity = '1';
            }
        }, true);

        document.addEventListener('mouseleave', function(e) {
            if (e.target.closest('.photo-wrapper')) {
                const btn = e.target.closest('.photo-wrapper').querySelector('.btn-remove-photo');
                if (btn) btn.style.opacity = '0';
            }
        }, true);
    </script>
@endpush

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

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
                        Total foto: <strong id="existing-photos-count">{{ $rak->fotos->count() }}</strong>/4
                        @if ($rak->fotos->count() < 4)
                            | Anda dapat menambah <strong id="remaining-slots">{{ 4 - $rak->fotos->count() }}</strong>
                            foto lagi
                        @else
                            | <strong>Maksimal tercapai</strong>
                        @endif
                    </small>
                </div>

                <div class="row" id="existing-photos-container">
                    @foreach ($rak->fotos->sortBy('urutan') as $foto)
                        <div class="col-6 col-md-3 mb-3 existing-photo-item" data-foto-id="{{ $foto->id }}">
                            <div class="photo-wrapper position-relative square-photo-container">
                                <img src="{{ asset('storage/' . $foto->path) }}" class="square-photo-img"
                                    alt="Foto Rak">

                                {{-- Delete button dengan icon X --}}
                                <button type="button" class="btn-remove-photo position-absolute"
                                    data-foto-id="{{ $foto->id }}" onclick="deletePhotoInstant({{ $foto->id }})"
                                    title="Hapus foto"
                                    style="top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%; border: none; background: rgba(239, 68, 68, 0.95); color: white; cursor: pointer; z-index: 10; opacity: 0; transition: all 0.2s ease;">
                                    <i class="fas fa-times"></i>
                                </button>
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
                    Foto yang dipilih: <strong id="new-foto-count">0</strong> |
                    Foto saat ini: <strong
                        id="current-foto-count">{{ isset($rak) ? $rak->fotos->count() : 0 }}</strong>/4 |
                    Batas maksimal: <strong>4 foto</strong>
                </small>
            </div>

            {{-- Info alert --}}
            <div class="alert alert-info alert-sm mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                <small>
                    Format: JPG, JPEG, PNG | Ukuran maksimal: 2MB per foto |
                    <strong>Foto akan ditampilkan secara random</strong>
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
        </div>

        {{-- Preview container for new photos --}}
        <div id="preview-container" class="row mt-3"></div>
    </div>
</div>

@push('scripts')
    <script>
        // Array untuk menyimpan file yang akan diupload
        let selectedFiles = [];

        function deletePhotoInstant(fotoId) {
            const photoItem = document.querySelector(`.existing-photo-item[data-foto-id="${fotoId}"]`);

            // Langsung hapus dari DOM dengan animasi
            if (photoItem) {
                photoItem.style.transition = 'all 0.3s ease';
                photoItem.style.opacity = '0';
                photoItem.style.transform = 'scale(0.8)';

                setTimeout(() => {
                    photoItem.remove();
                    // Update counter setelah hapus
                    const remainingExisting = document.querySelectorAll('.existing-photo-item').length;
                    updatePhotoCounters(remainingExisting);
                }, 300);
            }

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Kirim request delete ke server
            fetch(`/raks/photos/${fotoId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update counters
                        updatePhotoCounters(data.total_photos);

                        // Show success notification (mini)
                        showMiniNotification('Foto berhasil dihapus', 'success');
                    } else {
                        // Jika gagal, reload untuk kembalikan foto
                        showMiniNotification('Gagal menghapus foto', 'error');
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMiniNotification('Terjadi kesalahan', 'error');
                    setTimeout(() => location.reload(), 1500);
                });
        }

        function removeNewPhoto(index) {
            // Hapus file dari array
            selectedFiles.splice(index, 1);

            // Update preview
            updatePreview();

            // Update file input
            updateFileInput();
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

        function showMiniNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `mini-notification ${type}`;
            notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }

        function previewMultipleImages(event) {
            const files = Array.from(event.target.files);

            // Hitung foto existing yang masih ada di DOM
            const existingPhotos = document.querySelectorAll('.existing-photo-item').length;
            const maxAllowed = 4 - existingPhotos;

            console.log('Existing photos:', existingPhotos);
            console.log('Max allowed:', maxAllowed);
            console.log('Selected files:', files.length);

            // Cek apakah sudah maksimal
            if (existingPhotos >= 4) {
                showMiniNotification('Maksimal 4 foto sudah tercapai! Hapus foto existing terlebih dahulu.', 'error');
                event.target.value = '';
                return;
            }

            // Cek apakah file yang dipilih melebihi batas
            if (files.length > maxAllowed) {
                showMiniNotification(`Maksimal ${maxAllowed} foto lagi! Anda memilih ${files.length} foto.`, 'error');
                event.target.value = '';
                selectedFiles = [];
                updatePreview();
                updateCounters();
                return;
            }

            // Validasi file
            let validFiles = [];
            for (let file of files) {
                if (file.size > 2048 * 1024) {
                    showMiniNotification(`${file.name} terlalu besar (max 2MB)`, 'error');
                    continue;
                }

                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showMiniNotification(`${file.name} format tidak didukung`, 'error');
                    continue;
                }

                validFiles.push(file);
            }

            // Cek total setelah validasi
            if (validFiles.length + existingPhotos > 4) {
                showMiniNotification(`Total foto tidak boleh lebih dari 4!`, 'error');
                event.target.value = '';
                selectedFiles = [];
                updatePreview();
                updateCounters();
                return;
            }

            selectedFiles = validFiles;
            updatePreview();
            updateCounters();
        }

        function updatePreview() {
            const previewContainer = document.getElementById('preview-container');
            const fileLabel = document.querySelector('.custom-file-label');

            previewContainer.innerHTML = '';

            if (selectedFiles.length === 0) {
                fileLabel.textContent = 'Pilih foto (maksimal 4)';
                return;
            }

            fileLabel.textContent = selectedFiles.length === 1 ?
                selectedFiles[0].name :
                `${selectedFiles.length} foto dipilih`;

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-3 mb-3 new-photo-item';
                    col.innerHTML = `
                <div class="photo-wrapper position-relative square-photo-container">
                    <img src="${e.target.result}" 
                         class="square-photo-img"
                         alt="Preview">
                    <button type="button" 
                            class="btn-remove-photo position-absolute"
                            onclick="removeNewPhoto(${index})"
                            title="Hapus foto"
                            style="top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%; border: none; background: rgba(239, 68, 68, 0.95); color: white; cursor: pointer; z-index: 10; opacity: 0; transition: all 0.2s ease;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
                    previewContainer.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        }

        function updateFileInput() {
            const fileInput = document.getElementById('fotos');
            const dataTransfer = new DataTransfer();

            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });

            fileInput.files = dataTransfer.files;
            updateCounters();
        }

        function updateCounters() {
            const newFotoCounter = document.getElementById('new-foto-count');
            if (newFotoCounter) {
                newFotoCounter.textContent = selectedFiles.length;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize
            selectedFiles = [];

            // Add hover effect to photo wrappers
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

        /* Teknik untuk membuat foto persegi sempurna */
        .square-photo-container {
            position: relative;
            width: 100%;
            padding-bottom: 100%;
            /* Rasio 1:1 (persegi) */
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

        .existing-photo-item,
        .new-photo-item {
            transition: all 0.3s ease;
        }

        .mini-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            opacity: 0;
            transform: translateX(400px);
            transition: all 0.3s ease;
        }

        .mini-notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        .mini-notification.success {
            border-left: 4px solid #10b981;
            color: #059669;
        }

        .mini-notification.error {
            border-left: 4px solid #ef4444;
            color: #dc2626;
        }

        .mini-notification i {
            font-size: 18px;
        }
    </style>
@endpush

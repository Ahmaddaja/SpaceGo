<script>
    // ==============================
    // CONFIGURATION & INITIALIZATION
    // ==============================
    const isEditMode = {{ isset($rak) && $rak->id ? 'true' : 'false' }};
    const rakId = {{ isset($rak) ? $rak->id : 'null' }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let uploadedPhotosInSession = [];

    console.log('Photo Upload Mode:', isEditMode ? 'EDIT' : 'CREATE');
    console.log('Rak ID:', rakId);

    // ==============================
    // MODERN TOAST NOTIFICATION SYSTEM
    // ==============================
    function showAlert(message, type) {
        const existingToasts = document.querySelectorAll('.modern-toast');
        if (existingToasts.length > 3) {
            existingToasts[0].remove();
        }

        const toast = document.createElement('div');
        toast.className = 'modern-toast';

        let icon, bgColor, iconColor, progressColor, titleText;
        switch (type) {
            case 'success':
                icon = 'check-circle';
                bgColor = 'linear-gradient(135deg, #10b981, #059669)';
                iconColor = '#10b981';
                progressColor = '#34d399';
                titleText = 'Berhasil!';
                break;
            case 'error':
                icon = 'exclamation-circle';
                bgColor = 'linear-gradient(135deg, #ef4444, #dc2626)';
                iconColor = '#ef4444';
                progressColor = '#f87171';
                titleText = 'Kesalahan!';
                break;
            case 'warning':
                icon = 'exclamation-triangle';
                bgColor = 'linear-gradient(135deg, #f59e0b, #d97706)';
                iconColor = '#f59e0b';
                progressColor = '#fbbf24';
                titleText = 'Peringatan!';
                break;
            default:
                icon = 'info-circle';
                bgColor = 'linear-gradient(135deg, #3b82f6, #2563eb)';
                iconColor = '#3b82f6';
                progressColor = '#60a5fa';
                titleText = 'Informasi';
        }

        toast.style.cssText = `
            position: fixed;
            top: 24px;
            right: 24px;
            min-width: 320px;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 20px 48px rgba(0, 0, 0, 0.08);
            z-index: 99999;
            display: flex;
            align-items: center;
            gap: 14px;
            transform: translateX(150%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-left: 4px solid ${iconColor};
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        `;

        const iconContainer = document.createElement('div');
        iconContainer.style.cssText = `
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: ${bgColor};
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            animation: pulse 2s infinite;
        `;
        iconContainer.innerHTML = `<i class="fas fa-${icon}" style="font-size: 20px; color: white;"></i>`;

        const contentContainer = document.createElement('div');
        contentContainer.style.cssText = 'flex: 1; min-width: 0;';

        const title = document.createElement('div');
        title.style.cssText = `
            font-weight: 600;
            font-size: 15px;
            color: #1f2937;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        `;

        const titleSpan = document.createElement('span');
        titleSpan.textContent = titleText;

        const closeBtn = document.createElement('button');
        closeBtn.style.cssText = `
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            margin-left: 8px;
            border-radius: 6px;
            transition: all 0.2s;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
        `;
        closeBtn.innerHTML = '<i class="fas fa-times"></i>';
        closeBtn.onmouseover = () => closeBtn.style.color = '#6b7280';
        closeBtn.onmouseout = () => closeBtn.style.color = '#9ca3af';
        closeBtn.onclick = () => hideToast(toast);

        title.appendChild(titleSpan);
        title.appendChild(closeBtn);

        const messageEl = document.createElement('div');
        messageEl.style.cssText = `
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            word-wrap: break-word;
        `;
        messageEl.textContent = message;

        contentContainer.appendChild(title);
        contentContainer.appendChild(messageEl);

        const progressBar = document.createElement('div');
        progressBar.style.cssText = `
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: #e5e7eb;
            border-radius: 0 0 12px 12px;
            overflow: hidden;
        `;

        const progressFill = document.createElement('div');
        progressFill.style.cssText = `
            height: 100%;
            width: 100%;
            background: ${progressColor};
            transform-origin: left;
            animation: shrink 5s linear forwards;
        `;

        progressBar.appendChild(progressFill);

        if (!document.getElementById('toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = `
                @keyframes shrink {
                    from { transform: scaleX(1); }
                    to { transform: scaleX(0); }
                }
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                    100% { transform: scale(1); }
                }
                .modern-toast:hover {
                    transform: translateX(0) scale(1.02) !important;
                    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15), 0 25px 50px rgba(0, 0, 0, 0.1) !important;
                }
            `;
            document.head.appendChild(style);
        }

        toast.appendChild(iconContainer);
        toast.appendChild(contentContainer);
        toast.appendChild(progressBar);
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        });

        toast._createdAt = Date.now();
        let autoHideTimer = setTimeout(() => hideToast(toast), 5000);

        toast.addEventListener('mouseenter', () => {
            clearTimeout(autoHideTimer);
            progressFill.style.animationPlayState = 'paused';
        });

        toast.addEventListener('mouseleave', () => {
            const elapsed = Date.now() - toast._createdAt;
            const remainingTime = 5000 - elapsed;
            if (remainingTime > 0) {
                progressFill.style.animationPlayState = 'running';
                autoHideTimer = setTimeout(() => hideToast(toast), remainingTime);
            } else {
                hideToast(toast);
            }
        });
    }

    function hideToast(toast) {
        toast.style.transform = 'translateX(150%)';
        toast.style.opacity = '0';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 400);
    }

    // ==============================
    // MODERN CONFIRM DIALOG
    // ==============================
    function showConfirm(message, onConfirm, onCancel = null) {
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100000;
            animation: fadeIn 0.3s ease;
        `;

        const dialog = document.createElement('div');
        dialog.style.cssText = `
            background: white;
            border-radius: 16px;
            padding: 24px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            opacity: 0;
            animation: scaleIn 0.3s ease forwards;
        `;

        const icon = document.createElement('div');
        icon.style.cssText = `
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        `;
        icon.innerHTML = '<i class="fas fa-exclamation-triangle" style="font-size: 28px; color: white;"></i>';

        const messageEl = document.createElement('div');
        messageEl.style.cssText = `
            text-align: center;
            font-size: 16px;
            color: #374151;
            line-height: 1.5;
            margin-bottom: 24px;
            font-weight: 500;
        `;
        messageEl.textContent = message;

        const buttonContainer = document.createElement('div');
        buttonContainer.style.cssText = 'display: flex; gap: 12px; justify-content: center;';

        const cancelBtn = document.createElement('button');
        cancelBtn.style.cssText = `
            padding: 10px 24px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            flex: 1;
        `;
        cancelBtn.textContent = 'Batal';
        cancelBtn.onmouseover = () => cancelBtn.style.background = '#e5e7eb';
        cancelBtn.onmouseout = () => cancelBtn.style.background = '#f3f4f6';

        const confirmBtn = document.createElement('button');
        confirmBtn.style.cssText = `
            padding: 10px 24px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            flex: 1;
        `;
        confirmBtn.textContent = 'Ya, Lanjutkan';
        confirmBtn.onmouseover = () => confirmBtn.style.opacity = '0.9';
        confirmBtn.onmouseout = () => confirmBtn.style.opacity = '1';

        cancelBtn.onclick = () => {
            if (onCancel) onCancel();
            document.body.removeChild(overlay);
        };

        confirmBtn.onclick = () => {
            onConfirm();
            document.body.removeChild(overlay);
        };

        buttonContainer.appendChild(cancelBtn);
        buttonContainer.appendChild(confirmBtn);

        dialog.appendChild(icon);
        dialog.appendChild(messageEl);
        dialog.appendChild(buttonContainer);
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);

        if (!document.getElementById('dialog-styles')) {
            const style = document.createElement('style');
            style.id = 'dialog-styles';
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes scaleIn {
                    from { transform: scale(0.9); opacity: 0; }
                    to { transform: scale(1); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }

        overlay.onclick = (e) => {
            if (e.target === overlay) {
                if (onCancel) onCancel();
                document.body.removeChild(overlay);
            }
        };
    }

    // ==============================
    // UPLOAD PHOTOS (UNIFIED)
    // ==============================
    function uploadPhotosInstant(event) {
        const files = Array.from(event.target.files);
        if (!files.length) return;

        const existingPhotos = document.querySelectorAll('.existing-photo-item, .temp-photo-item').length;
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

        if (isEditMode && rakId) {
            uploadToServerInstant(validFiles);
        } else if (!isEditMode) {
            uploadToServerTemp(validFiles);
        } else {
            showAlert('Simpan rak terlebih dahulu sebelum upload foto', 'warning');
        }

        event.target.value = '';
    }

    // ==============================
    // UPLOAD TO SERVER (EDIT MODE)
    // ==============================
    function uploadToServerInstant(files) {
        const formData = new FormData();
        files.forEach((file) => formData.append('fotos[]', file));

        showLoading(`Mengupload ${files.length} foto...`);

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                const progressEl = document.getElementById('upload-progress');
                if (progressEl) progressEl.textContent = percentComplete + '%';
            }
        });

        xhr.addEventListener('load', function() {
            hideLoading();

            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);

                    if (data.success) {
                        showAlert(data.message, 'success');

                        const section = document.getElementById('existing-photos-section');
                        if (section && section.style.display === 'none') {
                            section.style.display = 'block';
                        }

                        if (Array.isArray(data.fotos) && data.fotos.length > 0) {
                            data.fotos.forEach(foto => {
                                addPhotoToContainer(foto, true);
                                uploadedPhotosInSession.push(foto.id);
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
                    showAlert('Terjadi kesalahan saat memproses respons', 'error');
                }
            } else {
                handleUploadError(xhr);
            }
        });

        xhr.addEventListener('error', () => {
            hideLoading();
            showAlert('Terjadi kesalahan saat upload. Periksa koneksi internet Anda.', 'error');
        });

        xhr.addEventListener('timeout', () => {
            hideLoading();
            showAlert('Upload timeout. Coba lagi atau pilih file yang lebih kecil.', 'error');
        });

        xhr.open('POST', `/raks/${rakId}/upload-photos`);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.timeout = 60000;
        xhr.send(formData);
    }

    // ==============================
    // UPLOAD TO SERVER (CREATE MODE - TEMP)
    // ==============================
    function uploadToServerTemp(files) {
        const formData = new FormData();
        files.forEach((file) => formData.append('fotos[]', file));

        showLoading(`Mengupload ${files.length} foto...`);

        fetch('{{ route('raks.photos.temp-upload') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();

                if (data.success) {
                    showAlert(data.message, 'success');

                    const section = document.getElementById('existing-photos-section');
                    if (section) section.style.display = 'block';

                    data.fotos.forEach(foto => {
                        addPhotoToContainer(foto, false);
                    });

                    updatePhotoCounters(data.total_photos);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat mengupload foto', 'error');
            });
    }

    // ==============================
    // ADD PHOTO TO CONTAINER (UNIFIED)
    // ==============================
    function addPhotoToContainer(foto, isPermanent) {
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
        col.className = `col-6 col-md-3 mb-3 ${isPermanent ? 'existing-photo-item' : 'temp-photo-item'}`;

        if (isPermanent) {
            col.setAttribute('data-foto-id', foto.id);
        } else {
            col.setAttribute('data-filename', foto.filename);
        }

        col.style.opacity = '0';
        col.style.transform = 'scale(0.8)';

        const deleteHandler = isPermanent ?
            `deletePhotoInstant(${foto.id})` :
            `deleteTempPhoto('${foto.filename}')`;

        const dataAttr = isPermanent ?
            `data-foto-id="${foto.id}"` :
            `data-filename="${foto.filename}"`;

        col.innerHTML = `
            <div class="photo-wrapper position-relative square-photo-container">
                <img src="${foto.url}" class="square-photo-img" alt="Foto Rak">
                <button type="button" class="btn-remove-photo position-absolute"
                    ${dataAttr} onclick="${deleteHandler}"
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

    // ==============================
    // DELETE PHOTO (EDIT MODE - INSTANT)
    // ==============================
    function deletePhotoInstant(fotoId) {
        showConfirm('Apakah Anda yakin ingin menghapus foto ini?',
            () => {
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
                        if (data.success) {
                            setTimeout(() => {
                                if (photoItem) photoItem.remove();

                                const index = uploadedPhotosInSession.indexOf(fotoId);
                                if (index > -1) {
                                    uploadedPhotosInSession.splice(index, 1);
                                }

                                updatePhotoCounters(data.total_photos);

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
        );
    }

    // ==============================
    // DELETE TEMP PHOTO (CREATE MODE)
    // ==============================
    function deleteTempPhoto(filename) {
        showConfirm('Apakah Anda yakin ingin menghapus foto ini?',
            () => {
                const photoItem = document.querySelector(`.temp-photo-item[data-filename="${filename}"]`);

                if (photoItem) {
                    photoItem.style.transition = 'all 0.3s ease';
                    photoItem.style.opacity = '0';
                    photoItem.style.transform = 'scale(0.8)';
                }

                fetch(`/raks/temp-delete/${filename}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            setTimeout(() => {
                                if (photoItem) photoItem.remove();

                                updatePhotoCounters(data.total_photos);

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
        );
    }

    // ==============================
    // UPDATE PHOTO COUNTERS
    // ==============================
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

    // ==============================
    // LOADING HELPERS
    // ==============================
    function showLoading(message = 'Memproses...') {
        const loadingDiv = document.getElementById('upload-loading');
        if (loadingDiv) {
            loadingDiv.style.display = 'block';
            loadingDiv.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">${message} <span id="upload-progress">0%</span></p>
            `;
        }

        const fileInput = document.getElementById('fotos');
        if (fileInput) fileInput.disabled = true;
    }

    function hideLoading() {
        const loadingDiv = document.getElementById('upload-loading');
        if (loadingDiv) loadingDiv.style.display = 'none';

        const fileInput = document.getElementById('fotos');
        if (fileInput) {
            const totalPhotos = document.querySelectorAll('.existing-photo-item, .temp-photo-item').length;
            fileInput.disabled = totalPhotos >= 4;
        }
    }

    // ==============================
    // ERROR HANDLER
    // ==============================
    function handleUploadError(xhr) {
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
                errorMessage = 'Token Keamanan (CSRF) Kedaluwarsa. Mohon refresh halaman dan coba lagi.';
            } else if (xhr.status === 422) {
                errorMessage = 'Validasi server gagal. Kemungkinan batasan PHP/server tidak sesuai.';
            } else {
                errorMessage = `Upload gagal (Server Error ${xhr.status}).`;
            }
        }

        showAlert(errorMessage, 'error');
    }

    // ==============================
    // CANCEL WITH CLEANUP
    // ==============================
    async function handleCancelWithCleanup() {
        if (isEditMode) {
            // EDIT MODE: Cleanup uploaded photos in session
            if (uploadedPhotosInSession.length === 0) {
                window.location.href = "{{ route('raks.index') }}";
                return;
            }

            showConfirm(
                `Anda telah mengupload ${uploadedPhotosInSession.length} foto. Batalkan dan hapus foto yang sudah diupload?`,
                async () => {
                    showAlert('Menghapus foto yang diupload...', 'warning');

                    const cancelBtn = document.getElementById('cancel-button');
                    const originalText = cancelBtn.innerHTML;
                    cancelBtn.disabled = true;
                    cancelBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membatalkan...';

                    try {
                        const deletePromises = uploadedPhotosInSession.map(fotoId => {
                            return fetch(`/raks/photos/${fotoId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            });
                        });

                        await Promise.all(deletePromises);

                        showAlert('Foto berhasil dihapus. Mengalihkan...', 'success');

                        setTimeout(() => {
                            window.location.href = "{{ route('raks.index') }}";
                        }, 1000);

                    } catch (error) {
                        console.error('Error cleaning up photos:', error);
                        cancelBtn.disabled = false;
                        cancelBtn.innerHTML = originalText;
                        showAlert('Gagal menghapus foto. Silakan coba lagi.', 'error');
                    }
                }
            );
        } else {
            // CREATE MODE: Cleanup temp photos
            const tempPhotos = document.querySelectorAll('.temp-photo-item');

            if (tempPhotos.length === 0) {
                window.location.href = "{{ route('raks.index') }}";
                return;
            }

            showConfirm(
                `Ada ${tempPhotos.length} foto yang belum tersimpan. Yakin ingin membatalkan?`,
                async () => {
                    showAlert('Menghapus foto sementara...', 'warning');

                    const deletePromises = [];
                    tempPhotos.forEach(photo => {
                        const filename = photo.dataset.filename;
                        if (filename) {
                            deletePromises.push(
                                fetch(`/raks/temp-delete/${filename}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json'
                                    }
                                })
                            );
                        }
                    });

                    try {
                        await Promise.all(deletePromises);
                        showAlert('Foto berhasil dihapus. Mengalihkan...', 'success');

                        setTimeout(() => {
                            window.location.href = "{{ route('raks.index') }}";
                        }, 1000);
                    } catch (error) {
                        console.error('Error:', error);
                        showAlert('Gagal menghapus beberapa foto', 'error');
                        setTimeout(() => {
                            window.location.href = "{{ route('raks.index') }}";
                        }, 1500);
                    }
                }
            );
        }
    }

    // ==============================
    // BEFOREUNLOAD WARNING
    // ==============================
    window.addEventListener('beforeunload', function(e) {
        const hasUnsavedPhotos = isEditMode ?
            uploadedPhotosInSession.length > 0 :
            document.querySelectorAll('.temp-photo-item').length > 0;

        if (hasUnsavedPhotos) {
            const message = 'Anda memiliki foto yang belum disimpan. Yakin ingin meninggalkan halaman?';
            e.returnValue = message;
            return message;
        }
    });

    // ==============================
    // FORM SUBMIT - RESET TRACKING
    // ==============================
    const mainForm = document.querySelector('form[action*="raks"]');
    if (mainForm) {
        mainForm.addEventListener('submit', function() {
            uploadedPhotosInSession = [];
            window.onbeforeunload = null;
        });
    }

    // ==============================
    // HOVER EFFECTS FOR DELETE BUTTONS
    // ==============================
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

    // ==============================
    // DOM READY - SETUP
    // ==============================
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Photo upload system initialized');
        console.log('Mode:', isEditMode ? 'EDIT' : 'CREATE');

        // Setup hover effects
        const setupHoverEffects = () => {
            document.querySelectorAll('.photo-wrapper').forEach(wrapper => {
                const removeBtn = wrapper.querySelector('.btn-remove-photo');
                if (!removeBtn) return;

                wrapper.addEventListener('mouseenter', () => {
                    removeBtn.style.opacity = '1';
                });

                wrapper.addEventListener('mouseleave', () => {
                    removeBtn.style.opacity = '0';
                });
            });
        };

        setupHoverEffects();

        // Re-apply hover effects after dynamic content
        const observer = new MutationObserver(setupHoverEffects);
        const container = document.getElementById('existing-photos-container');
        if (container) {
            observer.observe(container, {
                childList: true,
                subtree: true
            });
        }

        // Cleanup observer on page unload
        window.addEventListener('unload', () => {
            if (observer) observer.disconnect();
        });
    });
</script>

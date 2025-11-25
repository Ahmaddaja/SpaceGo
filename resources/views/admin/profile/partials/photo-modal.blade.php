<div class="modal fade" id="photoUploadModal" tabindex="-1" role="dialog" aria-labelledby="photoUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoUploadModalLabel">Ubah Foto Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.profile.upload-photo') }}" method="POST" enctype="multipart/form-data" id="photoUploadForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="photo">Pilih Foto</label>
                        <input type="file" class="form-control-file" id="photo" name="photo" accept="image/*" required>
                        <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG. Maksimal 2MB.</small>
                    </div>
                    
                    <div class="text-center">
                        <div id="photo-preview" class="mb-3" style="display: none;">
                            <img id="preview-image" src="#" alt="Preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #dee2e6;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="upload-button">Upload Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview image sebelum upload
    const photoInput = document.getElementById('photo');
    const previewImage = document.getElementById('preview-image');
    const photoPreview = document.getElementById('photo-preview');
    
    photoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Validasi ukuran file (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                this.value = '';
                return;
            }
            
            // Validasi tipe file
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            
            reader.addEventListener('load', function() {
                previewImage.setAttribute('src', this.result);
                photoPreview.style.display = 'block';
            });
            
            reader.readAsDataURL(file);
        }
    });

    // Handle form submission
    const form = document.getElementById('photoUploadForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const uploadButton = document.getElementById('upload-button');
        
        // Validasi apakah file sudah dipilih
        if (!photoInput.files[0]) {
            alert('Silakan pilih foto terlebih dahulu.');
            return;
        }
        
        uploadButton.disabled = true;
        uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Uploading...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update foto profile di halaman
                const profilePhotoDisplay = document.getElementById('profile-photo-display');
                const profileInitials = document.getElementById('profile-initials');
                
                if (profilePhotoDisplay) {
                    profilePhotoDisplay.src = data.photo_url;
                } else if (profileInitials) {
                    // Ganti initials dengan gambar
                    profileInitials.outerHTML = `<img src="${data.photo_url}" alt="Profile Photo" id="profile-photo-display" class="rounded-circle w-100 h-100" style="object-fit: cover;">`;
                }
                
                $('#photoUploadModal').modal('hide');
                form.reset();
                photoPreview.style.display = 'none';
                
                // Show success message
                alert(data.message || 'Foto profile berhasil diupdate!');
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat mengupload foto.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupload foto: ' + error.message);
        })
        .finally(() => {
            uploadButton.disabled = false;
            uploadButton.innerHTML = 'Upload Foto';
        });
    });
});
</script>
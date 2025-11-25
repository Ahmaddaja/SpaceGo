<script>
// Toggle Password Visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = event.currentTarget;
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Profile Photo Functions
let selectedFile = null;

function openPhotoModal() {
    $('#photoUploadModal').modal('show');
    resetModalPreview();
}

function resetModalPreview() {
    const modalPreview = document.getElementById('modalPhotoPreview');
    const modalInitials = document.getElementById('modalInitials');
    const uploadButton = document.getElementById('uploadButton');
    const fileInput = document.getElementById('photoInput');
    const fileLabel = document.getElementById('photoInputLabel');
    
    // Reset to current photo
    @if(auth()->user()->profile_photo && Storage::exists('public/profile-photos/' . auth()->user()->profile_photo))
        modalPreview.src = '{{ asset('storage/profile-photos/' . auth()->user()->profile_photo) }}';
        modalPreview.style.display = 'block';
        modalInitials.style.display = 'none';
    @else
        modalPreview.src = '{{ asset('assets/images/default-avatar.png') }}';
        modalPreview.style.display = 'block';
        modalInitials.style.display = 'none';
    @endif
    
    // Reset file input
    fileInput.value = '';
    fileLabel.textContent = 'Pilih foto...';
    uploadButton.disabled = true;
    selectedFile = null;
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileLabel = document.getElementById('photoInputLabel');
        const uploadButton = document.getElementById('uploadButton');
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            alert('Harap pilih file gambar yang valid (JPEG, PNG, GIF)');
            input.value = '';
            fileLabel.textContent = 'Pilih foto...';
            uploadButton.disabled = true;
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file harus kurang dari 2MB');
            input.value = '';
            fileLabel.textContent = 'Pilih foto...';
            uploadButton.disabled = true;
            return;
        }
        
        // Store the selected file
        selectedFile = file;
        fileLabel.textContent = file.name;
        uploadButton.disabled = false;
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const modalPreview = document.getElementById('modalPhotoPreview');
            const modalInitials = document.getElementById('modalInitials');
            
            modalPreview.src = e.target.result;
            modalPreview.style.display = 'block';
            modalInitials.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function uploadPhoto() {
    if (!selectedFile) {
        alert('Tidak ada file yang dipilih');
        return;
    }
    
    const formData = new FormData();
    formData.append('profile_photo', selectedFile);
    formData.append('_token', '{{ csrf_token() }}');
    
    // Show loading state
    const uploadButton = document.getElementById('uploadButton');
    const originalText = uploadButton.innerHTML;
    uploadButton.disabled = true;
    uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Uploading...';
    
    fetch('/admin/profile/upload-photo', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        uploadButton.innerHTML = originalText;
        uploadButton.disabled = false;
        
        if (data.success) {
            // Update main profile photo
            const mainPreview = document.getElementById('profilePhotoPreview');
            const mainInitials = document.getElementById('profile-initials');
            
            mainPreview.src = data.photo_url;
            mainPreview.style.display = 'block';
            mainInitials.style.display = 'none';
            
            // Show success message and close modal
            alert('✓ ' + data.message);
            $('#photoUploadModal').modal('hide');
            
            // Reload page to update photo in all places
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
        } else {
            alert('✗ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        uploadButton.innerHTML = originalText;
        uploadButton.disabled = false;
        alert('✗ Terjadi kesalahan saat mengupload foto');
    });
}

function deleteProfilePhoto() {
    if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
        fetch('/admin/profile/delete-photo', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update main profile photo to show initials
            const mainPreview = document.getElementById('profilePhotoPreview');
            const mainInitials = document.getElementById('profile-initials');
            
            mainPreview.style.display = 'none';
            mainInitials.style.display = 'block';
            
            alert('✓ ' + data.message);
            // Reload page to update everywhere
            window.location.reload();
        } else {
            alert('✗ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('✗ Terjadi kesalahan saat menghapus foto');
    });
    }
}

// Bootstrap file input styling
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih foto...';
            document.getElementById('photoInputLabel').textContent = fileName;
        });
    }
});
</script>
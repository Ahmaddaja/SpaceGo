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

$(document).ready(function() {
    // Photo preview functionality
    $('#photo-input-modal').on('change', function(e) {
        const file = e.target.files[0];
        const submitBtn = $('#submit-photo-upload');
        const errorDiv = $('#photo-upload-error');
        
        // Reset states
        errorDiv.addClass('d-none');
        submitBtn.prop('disabled', true);
        
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showError('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
                return;
            }
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                showError('Ukuran file maksimal 2MB.');
                return;
            }
            
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#new-photo-preview').attr('src', e.target.result).removeClass('d-none');
                $('#new-photo-preview-container').removeClass('d-none');
                $('#current-photo-img, #current-photo-initials').addClass('d-none');
                submitBtn.prop('disabled', false);
            }
            reader.readAsDataURL(file);
        }
    });
    
    function showError(message) {
        $('#photo-upload-error').text(message).removeClass('d-none');
        $('#photo-input-modal').val('');
    }
    
    // Photo upload functionality
    $('#submit-photo-upload').on('click', function() {
        const formData = new FormData();
        const fileInput = $('#photo-input-modal')[0];
        const submitBtn = $(this);
        const errorDiv = $('#photo-upload-error');
        
        if (!fileInput.files[0]) {
            showError('Pilih foto terlebih dahulu.');
            return;
        }
        
        formData.append('photo', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Show loading state
        submitBtn.prop('disabled', true);
        submitBtn.find('.spinner-border').removeClass('d-none');
        
        $.ajax({
            url: '/admin/profile/upload-photo',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Berhasil');
                location.reload();
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Error: ' + xhr.responseText);
            }
        });

    });
    
    // Reset modal when closed
    $('#photoUploadModal').on('hidden.bs.modal', function() {
        $('#photoUploadForm')[0].reset();
        $('#photo-upload-error').addClass('d-none');
        $('#new-photo-preview-container').addClass('d-none');
        $('#new-photo-preview').addClass('d-none').attr('src', '');
        $('#submit-photo-upload').prop('disabled', true);
        
        // Restore current photo preview
        @if(Auth::user()->photo)
            $('#current-photo-img').removeClass('d-none');
            $('#current-photo-initials').addClass('d-none');
        @else
            $('#current-photo-img').addClass('d-none');
            $('#current-photo-initials').removeClass('d-none');
        @endif
    });
    
    function showToast(title, message, type = 'success') {
        // You can use your preferred toast library here
        // Example with Bootstrap toast (make sure you have toast container in your layout)
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong>: ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        $('#toast-container').append(toast);
        new bootstrap.Toast(toast[0]).show();
    }
});
</script>
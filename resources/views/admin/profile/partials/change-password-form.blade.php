<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Ubah Password</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.profile.updatePassword') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password" class="font-weight-bold">Password Saat Ini <span class="text-danger">*</span></label>
                <div class="password-input-wrapper position-relative">
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                    <i class="fas fa-eye password-toggle position-absolute" onclick="togglePassword('current_password')"></i>
                </div>
                @error('current_password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password" class="font-weight-bold">Password Baru <span class="text-danger">*</span></label>
                <div class="password-input-wrapper position-relative">
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                    <i class="fas fa-eye password-toggle position-absolute" onclick="togglePassword('new_password')"></i>
                </div>
                <small class="form-text text-muted">Minimal 8 karakter</small>
                @error('new_password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password_confirmation" class="font-weight-bold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                <div class="password-input-wrapper position-relative">
                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    <i class="fas fa-eye password-toggle position-absolute" onclick="togglePassword('new_password_confirmation')"></i>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-key mr-1"></i> Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.password-input-wrapper {
    position: relative;
}

.password-toggle {
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    transition: color 0.2s;
    z-index: 5;
}

.password-toggle:hover {
    color: #495057;
}

.form-control.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid + .password-toggle {
    right: calc(15px + 1.5em + 0.75rem);
}
</style>

<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = passwordInput.nextElementSibling;
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
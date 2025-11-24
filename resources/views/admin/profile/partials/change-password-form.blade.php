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
                        <div class="password-input-wrapper">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            <i class="fas fa-eye password-toggle" onclick="togglePassword('current_password')"></i>
                        </div>
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="font-weight-bold">Password Baru <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                            <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password')"></i>
                        </div>
                        <small class="form-text text-muted">Minimal 8 karakter</small>
                        @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation" class="font-weight-bold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password_confirmation')"></i>
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
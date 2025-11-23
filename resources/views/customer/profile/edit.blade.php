@extends('layouts.customer')

@section('title', 'Edit Profile - SpaceGo')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.profile.index') }}">Profile</a></li>
            <li class="breadcrumb-item active">Edit Profile</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-user-edit fa-3x text-primary"></i>
                    </div>
                    <h4 class="mb-2">Edit Profile Saya</h4>
                    <p class="text-muted mb-0">Perbarui informasi profile Anda di SpaceGo</p>
                </div>
            </div>

            <!-- Form Edit Profile -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2 text-primary"></i>Informasi Personal
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form action="{{ route('customer.profile.update') }}" method="POST" id="formEditProfile">
                        @csrf
                        @method('PUT')

                        <!-- Foto Profile Section -->
                        <div class="text-center mb-4 pb-4 border-bottom">
                            <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : asset('images/default-avatar.png') }}" 
                                 alt="Profile Photo" 
                                 class="rounded-circle mb-3"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #4A90E2;">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editFotoModal">
                                    <i class="fas fa-camera me-1"></i> Ubah Foto Profile
                                </button>
                            </div>
                        </div>

                        <!-- Data Diri -->
                        <h6 class="mb-3 text-primary">
                            <i class="fas fa-user me-2"></i>Data Diri
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="nama" 
                                       class="form-control @error('nama') is-invalid @enderror" 
                                       value="{{ old('nama', $user->nama ?? Auth::user()->nama) }}" 
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email ?? Auth::user()->email) }}" 
                                       placeholder="email@example.com"
                                       required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    No. Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="telepon" 
                                       class="form-control @error('telepon') is-invalid @enderror" 
                                       value="{{ old('telepon', $user->telepon ?? Auth::user()->telepon) }}" 
                                       placeholder="08xxxxxxxxxx"
                                       required>
                                @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: 08xxxxxxxxxx</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Perusahaan/Instansi
                                </label>
                                <input type="text" 
                                       name="perusahaan" 
                                       class="form-control @error('perusahaan') is-invalid @enderror" 
                                       value="{{ old('perusahaan', $user->perusahaan ?? Auth::user()->perusahaan) }}" 
                                       placeholder="Nama perusahaan (opsional)">
                                @error('perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea name="alamat" 
                                          rows="4" 
                                          class="form-control @error('alamat') is-invalid @enderror" 
                                          placeholder="Masukkan alamat lengkap Anda"
                                          required>{{ old('alamat', $user->alamat ?? Auth::user()->alamat) }}</textarea>
                                @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Keamanan Akun -->
                        <h6 class="mb-3 text-primary">
                            <i class="fas fa-lock me-2"></i>Keamanan Akun
                        </h6>
                        <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengubah password</p>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Password Lama</label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="password_lama" 
                                           id="passwordLama"
                                           class="form-control @error('password_lama') is-invalid @enderror"
                                           placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordLama')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password_lama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="password" 
                                           id="passwordBaru"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordBaru')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Min. 8 karakter</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="password_confirmation" 
                                           id="passwordKonfirmasi"
                                           class="form-control"
                                           placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordKonfirmasi')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('customer.profile.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi:</strong> Pastikan data yang Anda masukkan sudah benar. Data ini akan digunakan untuk keperluan transaksi sewa gudang di SpaceGo.
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Foto -->
<div class="modal fade" id="editFotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>Ubah Foto Profile
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.profile.upload-foto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img id="previewFoto" 
                             src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : asset('images/default-avatar.png') }}" 
                             class="rounded-circle mb-3" 
                             style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #4A90E2;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Foto Baru</label>
                        <input type="file" 
                               name="foto" 
                               class="form-control" 
                               accept="image/jpeg,image/png,image/jpg" 
                               id="fotoInput" 
                               required>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-image me-1"></i>Format: JPG, PNG | Maksimal: 2MB
                        </small>
                    </div>

                    <div class="alert alert-warning" role="alert">
                        <small>
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Pastikan foto terlihat jelas dan profesional
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 15px;
    }
    
    .card-header {
        border-bottom: 2px solid #f0f0f0;
        border-radius: 15px 15px 0 0 !important;
    }
    
    .btn-primary {
        background-color: #4A90E2;
        border-color: #4A90E2;
        padding: 10px 24px;
    }
    
    .btn-primary:hover {
        background-color: #357ABD;
        border-color: #357ABD;
    }
    
    .form-control:focus {
        border-color: #4A90E2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
    }
    
    .form-label.fw-semibold {
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .breadcrumb {
        background-color: #f8f9fa;
        padding: 12px 20px;
        border-radius: 8px;
    }
    
    .breadcrumb-item a {
        color: #4A90E2;
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    
    .input-group .btn-outline-secondary:hover {
        background-color: #f0f0f0;
    }
</style>

<script>
    // Preview foto sebelum upload
    document.getElementById('fotoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validasi ukuran file (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 2MB');
                this.value = '';
                return;
            }
            
            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung! Gunakan JPG atau PNG');
                this.value = '';
                return;
            }
            
            // Preview foto
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFoto').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.nextElementSibling;
        const icon = button.querySelector('i');
        
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

    // Konfirmasi sebelum submit
    document.getElementById('formEditProfile').addEventListener('submit', function(e) {
        // Validasi password match jika diisi
        const password = document.querySelector('input[name="password"]').value;
        const passwordConfirm = document.querySelector('input[name="password_confirmation"]').value;
        
        if (password && password !== passwordConfirm) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak sama!');
            return false;
        }
        
        // Konfirmasi penyimpanan
        if (!confirm('Apakah Anda yakin ingin menyimpan perubahan profile?')) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endsection
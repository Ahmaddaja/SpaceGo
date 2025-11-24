@extends('layouts.main', ['title' => 'Profile'])

@section('styles')
<style>
    .profile-avatar {
        width: 120px;
        height: 120px;
        font-size: 48px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
    }

    .profile-avatar:hover .profile-avatar-overlay {
        opacity: 1;
    }

    .profile-avatar-overlay i {
        font-size: 24px;
        color: white;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }

    .password-toggle:hover {
        color: #007bff;
    }

    .password-input-wrapper {
        position: relative;
    }

    .password-input-wrapper .form-control {
        padding-right: 40px;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .alert {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .badge {
        transition: all 0.2s ease;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    .info-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
    }

    .card-header {
        border-bottom: 2px solid #007bff;
    }

    .form-group label {
        color: #495057;
        margin-bottom: 8px;
    }

    .text-danger {
        font-weight: 600;
    }

    hr {
        border-top: 2px solid #e9ecef;
    }

    /* Custom scrollbar untuk textarea */
    textarea::-webkit-scrollbar {
        width: 8px;
    }

    textarea::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    textarea::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    textarea::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Animation untuk form */
    .form-control, .form-group {
        animation: fadeInUp 0.4s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .profile-avatar {
            width: 100px;
            height: 100px;
            font-size: 36px;
        }

        .card:hover {
            transform: none;
        }

        .btn:hover {
            transform: none;
        }
    }

    #photo-input {
        display: none;
    }
</style>
@endsection

@section('title-content')
<div class="d-flex justify-content-between align-items-right">
    <h1 class="m-0">Profile</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 m-0 justify-content-end small">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-lg-4 col-12 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white font-weight-bold profile-avatar" onclick="document.getElementById('photo-input').click()">
                        @if(Auth::user()->photo)
                            <img src="{{ asset(Auth::user()->photo) }}" alt="Profile Photo" id="profile-photo-display">
                        @else
                            <span id="profile-initials">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        @endif
                    </div>
                </div>
                <h4 class="font-weight-bold mb-1">{{ Auth::user()->name }}</h4>
                <p class="text-muted mb-3">{{ Auth::user()->email }}</p>
                <span class="badge badge-primary px-3 py-2" style="font-size: 14px;">{{ ucfirst(Auth::user()->role) }}</span>
                
                <hr class="my-4">
                
                <div class="text-left">
                    <div class="mb-3">
                        <small class="info-label">Join Since</small>
                        <div class="info-value">{{ Auth::user()->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="info-label">Last Updated</small>
                        <div class="info-value">{{ Auth::user()->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <small class="info-label">Status</small>
                        <div><span class="badge badge-success px-3 py-2">Active</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="col-lg-8 col-12 mb-3">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 font-weight-bold">Edit Profile</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="file" name="photo" id="photo-form-input" style="display: none;">

                    <div class="form-group">
                        <label for="name" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="telepon" class="font-weight-bold">No. Telepon</label>
                        <input type="tel" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', Auth::user()->telepon ?? '') }}" placeholder="08xxxxxxxxxx">
                        @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="alamat" class="font-weight-bold">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat', Auth::user()->alamat ?? '') }}</textarea>
                        @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary mr-2" onclick="window.location.reload()">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
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
    </div>
</div>

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

// Handle Photo Upload Preview
document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar!');
            return;
        }

        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB!');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            const avatar = document.querySelector('.profile-avatar');
            const initials = document.getElementById('profile-initials');
            const photo = document.getElementById('profile-photo-display');
            
            if (photo) {
                photo.src = event.target.result;
            } else {
                if (initials) initials.remove();
                const img = document.createElement('img');
                img.src = event.target.result;
                img.alt = 'Profile Photo';
                img.id = 'profile-photo-display';
                avatar.insertBefore(img, avatar.firstChild);
            }
            
            // Set file to form input
            const formInput = document.getElementById('photo-form-input');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            formInput.files = dataTransfer.files;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
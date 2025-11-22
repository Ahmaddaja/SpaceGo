<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SpaceGO – Register</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- GOOGLE FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- CUSTOM STYLE -->
    <style>
        body {
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: "Poppins", sans-serif;
        }

        .register-card {
            width: 100%;
            max-width: 450px;
            padding: 35px 30px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 22px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: .3s ease;
        }

        .register-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.22);
        }

        .logo-storage {
            width: 70px;
            margin-bottom: 10px;
            filter: drop-shadow(0 3px 4px rgba(0,0,0,0.2));
        }

        .btn-register {
            background: #4f46e5;
            font-weight: 600;
            border: none;
            transition: .25s ease;
        }

        .btn-register:hover {
            background: #4338ca;
            transform: scale(1.03);
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
            border: 1px solid #d2d2d2;
            transition: .2s;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .25);
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 28px 22px;
                border-radius: 16px;
            }
            h3 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>

<body>

<div class="register-card">

    <!-- Logo & Judul -->
    <div class="text-center mb-3">
        <svg class="logo-storage" fill="#4f46e5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z"/>
        </svg>

        <h3 class="fw-bold text-dark">Daftar Akun SpaceGO</h3>
        <p class="text-muted">Akses layanan sewa gudang</p>
    </div>

    <!-- REGISTER FORM -->
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nama -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input id="name" type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Username -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input id="username" type="text" name="username"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username') }}" required>
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nomor Telepon -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Telepon</label>
            <input id="phone" type="text" name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   required>
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Button Register -->
        <button type="submit" class="btn btn-register text-white w-100 py-2 mb-3">
            Daftar
        </button>

        <!-- Login Link -->
        <div class="text-center">
            <span class="text-muted small">Sudah Punya Akun?</span>
            <a href="{{ route('login') }}" class="fw-semibold" style="color:#4f46e5">Login</a>
        </div>
    </form>
</div>

</body>
</html>

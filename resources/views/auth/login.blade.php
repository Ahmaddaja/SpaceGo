<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SpaceGO – Login</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- GOOGLE FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- CUSTOM STYLE -->
    <style>
        body {
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            font-family: "Poppins", sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 35px 30px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 22px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: .3s ease;
        }

        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.22);
        }

        .logo-storage {
            width: 70px;
            margin-bottom: 15px;
            filter: drop-shadow(0px 3px 5px rgba(0,0,0,0.2));
        }

        .btn-login {
            background: #4f46e5;
            border: none;
            font-weight: 600;
            transition: .3s ease-in-out;
        }

        .btn-login:hover {
            background: #4338ca;
            transform: scale(1.03);
        }

        .form-control {
            height: 50px;
            border-radius: 12px;
            border: 1px solid #d4d4d4;
            transition: .2s ease-in-out;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .25);
        }

        .register-link a {
            font-weight: 600;
            color: #4f46e5;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* mobile improvements */
        @media (max-width: 480px) {
            .login-card {
                padding: 28px 22px;
                border-radius: 18px;
            }
            h3 {
                font-size: 1.4rem;
            }
            p {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>

<div class="login-card shadow-lg">
    <div class="text-center">

        <!-- LOGO STORAGE (lebih clean & modern) -->
        <svg class="logo-storage" fill="#4f46e5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z"/>
        </svg>

        <h3 class="fw-bold text-dark mb-1">SpaceGO</h3>
        <p class="text-muted mb-4">Layanan Sewa Gudang & Penyimpanan Modern</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input id="email" type="email" name="email" required autofocus
                   class="form-control" value="{{ old('email') }}">
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input id="password" type="password" name="password" required
                   class="form-control">
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label for="remember" class="form-check-label small">Ingat saya</label>
        </div>

        <!-- Button Login -->
        <button type="submit" class="btn btn-login text-white w-100 py-2 mb-3">
            Masuk
        </button>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
        <div class="text-end mb-2">
            <a href="{{ route('password.request') }}" class="small text-secondary">Lupa password?</a>
        </div>
        @endif

        <!-- Register Link -->
        <div class="text-center register-link mt-2">
            <span class="text-muted small">Belum punya akun?</span>
            <a href="{{ route('register') }}">Daftar</a>
        </div>
    </form>
</div>

</body>
</html>

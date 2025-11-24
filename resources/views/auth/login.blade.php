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

    <!-- BOOTSTRAP ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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

        /* Password Toggle Styling */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #4f46e5;
        }

        .password-toggle i {
            font-size: 18px;
        }

        .password-wrapper .form-control {
            padding-right: 45px;
        }
    </style>
</head>

<body>

<div class="login-card shadow-lg">
    <div class="text-center">

        <svg class="logo-storage" fill="#4f46e5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z"/>
        </svg>

        <h3 class="fw-bold text-dark mb-1">SpaceGO</h3>
        <p class="text-muted mb-4">Layanan Sewa Gudang & Penyimpanan</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email / Username -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Email atau Username</label>
            <input id="login" type="text" name="login" required autofocus
                   class="form-control" value="{{ old('login') }}">
            @error('login')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Password with Toggle -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="password-wrapper">
                <input id="password" type="password" name="password" required
                       class="form-control">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i id="toggleIcon" class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label for="remember" class="form-check-label small">Ingat saya</label>
        </div>

        <button type="submit" class="btn btn-login text-white w-100 py-2 mb-3">
            Masuk
        </button>

        <div class="text-center register-link mt-2">
            <span class="text-muted small">Belum punya akun?</span>
            <a href="{{ route('register') }}">Daftar</a>
        </div>
    </form>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    }
</script>

</body>
</html>
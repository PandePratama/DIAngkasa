<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - DiRaditya</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .brand-area {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-bottom: 32px;
        }

        .brand-logo {
            height: 56px;
            object-fit: contain;
        }

        .login-title {
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 36px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            height: 52px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            padding: 0 16px;
            font-size: 15px;
            color: #111827;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            padding: 8px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #10b981;
        }

        .forgot-link {
            font-size: 14px;
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #059669;
        }

        .btn-login {
            height: 52px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            border: none;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 12px;
            border: none;
            background: #fee2e2;
            color: #991b1b;
            font-size: 14px;
            font-weight: 500;
            padding: 12px 16px;
        }

        .alert i {
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 36px 28px;
            }

            .brand-logo {
                height: 48px;
            }

            .login-title {
                font-size: 24px;
                margin-bottom: 28px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-card">

            <!-- LOGO -->
            <div class="brand-area">
                <img src="{{ asset('sbadmin/img/Raditya logo R.webp') }}" class="brand-logo" alt="Raditya Logo">
                <img src="{{ asset('sbadmin/img/Logo Dia_Green.webp') }}" class="brand-logo" alt="DiRaditya Logo">
            </div>

            <h4 class="login-title">Welcome Back</h4>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">NIP atau Email</label>
                    <input type="text" name="login" class="form-control" placeholder="Masukkan NIP atau Email"
                        required autocomplete="username">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Masukkan Password" required autocomplete="current-password"
                            style="padding-right: 48px;">
                        <button type="button" class="password-toggle" onclick="togglePassword()"
                            aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                {{--
                <div class="text-end mb-4">
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div> --}}

                <button type="submit" class="btn btn-login w-100">
                    Sign In
                </button>

                @if (session('failed'))
                    <div class="alert mt-3" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('failed') }}
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

</body>

</html>

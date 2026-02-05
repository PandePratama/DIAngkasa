<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - DiRaditya</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f766e, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 16px;
        }

        .login-card {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .2);
            overflow: hidden;
        }

        .brand-area {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 18px;
            padding: 28px 20px 16px;
        }

        .brand-logo {
            height: 56px;
            object-fit: contain;
        }

        .login-body {
            padding: 28px;
        }

        .login-title {
            font-weight: 700;
            text-align: center;
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
        }

        .form-control {
            border-radius: 14px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
        }

        .form-control:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
        }

        .input-group .btn {
            border-radius: 0 14px 14px 0;
            border-color: #e5e7eb;
        }

        .forgot-link {
            font-size: 13px;
            text-decoration: none;
            color: #6b7280;
        }

        .forgot-link:hover {
            color: #0f766e;
            text-decoration: underline;
        }

        .btn-login {
            margin-top: 8px;
            border-radius: 999px;
            padding: 12px;
            font-weight: 600;
            background: linear-gradient(135deg, #0f766e, #06b6d4);
            border: none;
            transition: all .3s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(6, 182, 212, 0.4);
        }

        .alert {
            border-radius: 12px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">

            <!-- LOGO -->
            <div class="brand-area">
                <img src="{{ asset('sbadmin/img/Raditya logo R.webp') }}" class="brand-logo">
                <img src="{{ asset('sbadmin/img/Logo Dia_Green.webp') }}" class="brand-logo">
            </div>

            <div class="login-body">
                <h4 class="login-title">Welcome Back</h4>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">NIP atau Email</label>
                        <input type="text"
                            name="login"
                            class="form-control"
                            placeholder="NIP atau Email"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="••••••••"
                                required>
                            <button class="btn btn-outline-secondary"
                                type="button"
                                onclick="togglePassword()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-end mb-3">
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-login text-white w-100">
                        Sign In
                    </button>

                    @if(session('failed'))
                    <div class="alert alert-danger text-center mt-3">
                        {{ session('failed') }}
                    </div>
                    @endif
                </form>
            </div>
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
    </script>

</body>

</html>

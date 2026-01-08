<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - DIAngkasa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #097782;
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
        }

        .brand-title {
            text-align: center;
            color: #fff;
            font-size: 32px;
            font-weight: 700;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, .25);
        }

        .btn-login {
            background-color: #097782;
            border: none;
            border-radius: 25px;
            font-weight: 600;
        }

        .btn-login:hover {
            background-color: #075f66;
        }

        .forgot-link {
            font-size: 14px;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .brand-logo {
            height: 80px;
            width: auto;
        }
    </style>
</head>

<body>

    <div class="login-box px-3">
        <div class="card">
            <div class="brand-title">
                <img src="{{ asset('sbadmin/img/Raditya logo R.png') }}" alt="DIAngkasa Logo" class="brand-logo mt-2">
            </div>
            <div class="card-body px-4 pt-1">
                <h5 class="text-center mb-4 fw-bold">Login</h5>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Username / Email / NIP -->
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text"
                            name="login"
                            class="form-control"
                            placeholder="Email atau NIP"
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Password"
                                required>
                            <button class="btn btn-outline-secondary"
                                type="button"
                                onclick="togglePassword()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Forgot password -->
                    <div class="text-end mb-3">
                        <a href="#" class="forgot-link text-muted">
                            Forgot Password?
                        </a>
                    </div>

                    <!-- Button -->
                    <button type="submit" class="btn btn-login text-white w-100">
                        Login
                    </button>

                    <!-- Error -->
                    @if(session('failed'))
                    <div class="alert alert-danger mt-3 text-center">
                        {{ session('failed') }}
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>

</body>

</html>
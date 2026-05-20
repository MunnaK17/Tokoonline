<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | Matrix Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16"
          href="/admin-assets/assets/images/favicon.png">

    <!-- Bootstrap -->
    <link href="/admin-assets/assets/libs/bootstrap/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Icons -->
    <link href="/admin-assets/assets/libs/themify-icons/css/themify-icons.css"
          rel="stylesheet">

    <!-- Matrix CSS -->
    <link href="/admin-assets/dist/css/style.min.css"
          rel="stylesheet">

    <!-- Layout Fix -->
    <style>
        html, body {
            height: 100%;
        }
        .main-wrapper {
            min-height: 100vh;
        }
        .auth-wrapper {
            min-height: 100vh;
        }
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            color: #8a96a3;
            font-size: 13px;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e9f0;
        }
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid #d9dee8;
            background: #fff;
            color: #34495e;
            font-weight: 600;
        }
        .btn-google:hover,
        .btn-google:focus {
            border-color: #b8c1d1;
            background: #f8fafc;
            color: #233142;
        }
        .google-icon {
            width: 20px;
            height: 20px;
            flex: 0 0 20px;
        }
    </style>
</head>

<body>
<div class="main-wrapper">

    <!-- Preloader -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <!-- Login Wrapper -->
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-light">
        <div class="auth-box bg-white border-top border-secondary p-4 shadow-sm" style="width: 100%; max-width: 420px;">

            <form method="POST" action="{{ route('backend.login.submit') }}">
                @csrf

                <h3 class="text-center mb-3">Login</h3>

                <!-- Global Error -->
                @if (session('error'))
                    <div class="alert alert-danger text-center">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Email -->
                <div class="input-group mb-3">
                    <span class="input-group-text bg-success text-white">
                        <i class="ti-user"></i>
                    </span>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           placeholder="Masukkan Email"
                           required autofocus>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input-group mb-4">
                    <span class="input-group-text bg-warning text-white">
                        <i class="ti-lock"></i>
                    </span>
                    <input type="password"
                           name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           placeholder="Masukkan Password"
                           required>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="form-group">
                    <button class="btn btn-info btn-lg w-100" type="submit">
                        Login
                    </button>
                </div>

            </form>

            <div class="auth-divider">atau</div>

            <a href="{{ route('auth.redirect') }}" class="btn btn-google btn-lg w-100">
                <svg class="google-icon" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
                    <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.5l6.8-6.8C35.7 2.3 30.3 0 24 0 14.6 0 6.5 5.4 2.6 13.3l7.9 6.1C12.4 13.5 17.8 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.7h12.7c-.6 2.9-2.2 5.3-4.7 6.9l7.3 5.7c4.3-4 7.2-9.9 7.2-16.8z"/>
                    <path fill="#FBBC05" d="M10.5 28.6c-.5-1.5-.8-3-.8-4.6s.3-3.1.8-4.6l-7.9-6.1C1 16.5 0 20.1 0 24s1 7.5 2.6 10.7l7.9-6.1z"/>
                    <path fill="#34A853" d="M24 48c6.3 0 11.6-2.1 15.4-5.7l-7.3-5.7c-2 1.4-4.6 2.2-8.1 2.2-6.2 0-11.6-4-13.5-9.5l-7.9 6.1C6.5 42.6 14.6 48 24 48z"/>
                </svg>
                Login dengan Google
            </a>

        </div>
    </div>

</div>

<!-- REQUIRED JS -->
<script src="/admin-assets/assets/libs/jquery/dist/jquery.min.js"></script>
<script src="/admin-assets/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- MATRIX CORE -->
<script src="/admin-assets/dist/js/app.min.js"></script>

<script>
    $(".preloader").fadeOut();
</script>

</body>
</html>
    

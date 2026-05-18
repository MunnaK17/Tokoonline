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
    
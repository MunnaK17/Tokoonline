<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $judul ?? 'Toko Online' }}</title>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin-assets/assets/images/icon_univ_bsi.png') }}">

    <link href="https://fonts.googleapis.com/css?family=Hind:400,700" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('eshop/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('eshop/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('eshop/css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('eshop/css/nouislider.min.css') }}">
    <link rel="stylesheet" href="{{ asset('eshop/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('eshop/css/style.css') }}">
</head>
<body>

    @yield('content')

    <script src="{{ asset('eshop/js/jquery.min.js') }}"></script>
    <script src="{{ asset('eshop/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('eshop/js/slick.min.js') }}"></script>
    <script src="{{ asset('eshop/js/nouislider.min.js') }}"></script>
    <script src="{{ asset('eshop/js/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('eshop/js/main.js') }}"></script>
</body>
</html>

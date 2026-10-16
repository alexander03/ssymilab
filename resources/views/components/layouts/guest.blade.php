<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'UNIMEDILAB') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ config('app.name', 'UNIMEDILAB') }}" name="description" />

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/css/main.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">


    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>


</head>

<body class="authentication-bg" style="width:100%!important">
    {{ $slot }}
    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>

    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @livewireScripts
</body>

</html>

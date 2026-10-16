<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'UNIMEDILAB') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ config('app.name', 'UNIMEDILAB') }}" name="description" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link href="{{ asset('assets/libs/toastr/build/toastr.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />
    {{-- <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" /> --}}
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/css/main.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/css/styles.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/css/select2.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">


    <!-- Laravel Mix -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">


    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <style>


    </style>

</head>


<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        @livewire('layouts.header')

        <!-- ========== Left Sidebar Start ========== -->
        <x-layouts.sidebar-app />
        <!-- Left Sidebar End -->

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- start page title -->
                    {{ $content }}

                    <!-- end page title -->
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <x-layouts.footer />
        </div>
        <script>
            @if (Session::has('success'))
                window.onload = function() {
                    toastr.success('{{ Session::get('success') }}', '¡Exito!')
                };
            @endif
            @if (Session::has('error'))
                window.onload = function() {
                    toastr.error('{{ Session::get('error') }}', '¡Error!')
                };
            @endif
            @if (Session::has('warning'))
                window.onload = function() {
                    toastr.warning('{{ Session::get('warning') }}', '¡Advertencia!')
                };
            @endif
        </script>
    </div>
    <!-- END layout-wrapper -->
    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script> --}}

    <script src="{{ asset('assets/js/select2.min.js') }}"></script>


    <!-- toastr --->
    <script src="{{ asset('assets/libs/toastr/build/toastr.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @livewireScripts

    @stack('js')

    <script src="{{ asset('assets/js/scripts.js') }}"></script>

</body>

</html>

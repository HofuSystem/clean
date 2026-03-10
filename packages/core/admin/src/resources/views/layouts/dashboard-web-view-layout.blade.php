<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans("Dashboard") }} | {{ config('app.name') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('control') }}/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{ config('backend-settings.admin_style.admin_login_logo') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/vendors/css/charts/apexcharts.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/app-assets/css/core/menu/menu-types/vertical-menu.css">

    <!-- END: Page CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('control') }}/dist/css/custome.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('settings') }}/settings.css">
    <!-- END: Custom CSS-->
    @stack('css')
    @yield("styles")
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static   menu-collapsed {{ $screen }}" data-open="click" data-menu="vertical-menu-modern" data-col="">


    @include('admin::inc.nav')
    @yield("header")
    @yield('content')
    @yield("footer")
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    @include('admin::inc.footer')



    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('control') }}/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->


    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('control') }}/app-assets/js/core/app-menu.js"></script>
    <script src="{{ asset('control') }}/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->


  
    {{-- custom js --}}
    <script src="{{ asset('control') }}/dist/js/custom.js"></script>
    <script src="{{ asset('settings') }}/settings.js"></script>
    @stack('js')

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })

    </script>
    @yield("scripts")
</body>
<!-- END: Body-->

</html>

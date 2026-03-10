<!DOCTYPE html>

<html
  lang="{{ config('app.locale') }}"
  class="light-style customizer-hide"
  dir="{{ config('app.currentLocaleDir') ?? 'ltr' }}"
  data-theme="theme-default"
  data-assets-path="{{ asset('control') }}/assets/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ $title }} | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('control') }}/assets/img/favicon/favicon.png" />


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/fonts/fontawesome.css" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <!-- <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/fonts/tabler-icons.css" /> -->
    <!-- <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/fonts/flag-icons.css" /> -->

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('control') }}/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <!-- <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/node-waves/node-waves.css" /> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.min.css" integrity="sha512-bsNktdxsLu4ooy7axuzyyFz87SWrDBaCmZsk2Dvin1/2noq49vt1jfNWUAfdybRpFCzRjdWygAOEopdbo8cGpA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <!-- <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" /> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/css/perfect-scrollbar.min.css" integrity="sha512-ygIxOy3hmN2fzGeNqys7ymuBgwSCet0LVfqQbWY10AszPMn2rB9JY0eoG0m1pySicu+nvORrBmhHVSt7+GI9VA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <!-- <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/typeahead-js/typeahead.css" /> -->

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/libs/@form-validation/umd/styles/index.min.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('custom') }}/dist/css/custome.css">

    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('control') }}/assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="{{ asset('control') }}/assets/vendor/js/helpers.js"></script>
    <!--! RM Customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? RM Customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('control') }}/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('control') }}/assets/js/config.js"></script>

    @stack("css")

  </head>

  <body class="{{ $screen }}">
    @include('admin::inc.loader')

    @yield('content')
    @yield("footer")
    <!-- BEGIN: Vendor JS-->
    <!-- <script src="{{ asset('control') }}/app-assets/vendors/js/vendors.min.js"></script> -->
    <!-- BEGIN Vendor JS-->


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('control') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script src="{{ asset('control') }}/assets/vendor/libs/popper/popper.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script src="{{ asset('control') }}/assets/vendor/js/bootstrap.js"></script>
    <!-- <script src="{{ asset('control') }}/assets/vendor/libs/node-waves/node-waves.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.min.js" integrity="sha512-MzXgHd+o6pUd/tm8ZgPkxya3QUCiHVMQolnY3IZqhsrOWQaBfax600esAw3XbBucYB15hZLOF0sKMHsTPdjLFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <!-- <script src="{{ asset('control') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/perfect-scrollbar.min.js" integrity="sha512-X41/A5OSxoi5uqtS6Krhqz8QyyD8E/ZbN7B4IaBSgqPLRbWVuXJXr9UwOujstj71SoVxh5vxgy7kmtd17xrJRw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <!-- <script src="{{ asset('control') }}/assets/vendor/libs/hammer/hammer.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js" integrity="sha512-UXumZrZNiOwnTcZSHLOfcTs0aos2MzBWHXOHOuB0J/R44QB0dwY5JgfbvljXcklVf65Gc4El6RjZ+lnwd2az2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script src="{{ asset('control') }}/assets/vendor/libs/i18n/i18n.js"></script>
    <!-- <script src="{{ asset('control') }}/assets/vendor/libs/typeahead-js/typeahead.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js" integrity="sha512-qOBWNAMfkz+vXXgbh0Wz7qYSLZp6c14R0bZeVX2TdQxWpuKr6yHjBIM69fcF8Ve4GUX6B6AKRQJqiiAmwvmUmQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->

    <script src="{{ asset('control') }}/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('control') }}/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
    <script src="{{ asset('control') }}/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
    <script src="{{ asset('control') }}/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('control') }}/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('control') }}/assets/js/pages-auth.js"></script>
    <script src="{{ asset('custom') }}/dist/js/custom.js"></script>

    <!-- END: Page JS-->

    @stack("js")

  </body>
</html>

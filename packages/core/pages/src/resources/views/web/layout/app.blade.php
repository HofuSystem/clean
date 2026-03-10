<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Spa')</title>
    <link rel="stylesheet" href="{{ asset('web/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('css')
</head>
<body>
    <!-- Navigation -->
    @include('pages::web.sections.navigation')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('pages::web.sections.footer')

    <!-- Social Media Buttons -->
    @include('pages::web.sections.social-buttons')

    <!-- Book Now Modal -->
    @include('pages::web.sections.book-modal')

    <script src="{{ asset('web/main.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    @stack('js')
</body>
</html> 
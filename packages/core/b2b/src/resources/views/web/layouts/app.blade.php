<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" dir="{{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="title" content="{{ $title }}">
    <meta name="description" content="{{ $description ?? '' }}">
    <link rel="icon" href="{{ config('app.icon') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('client/css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('client/js/main.js') }}" defer></script>
    @stack('styles')
</head>

<body class="text-gray-900 flex flex-col min-h-screen relative">

    <!-- Developed By Watermark -->
    <div class="hovo-watermark fixed bottom-3 right-4 z-[9999] text-[11px] font-black text-gray-500/80 bg-white/90 px-4 py-2 rounded-full shadow-md backdrop-blur-md border border-gray-100 print-hidden hover:text-[#1c75bc] transition-colors cursor-default"
        data-i18n="developed_by">
        تم تطوير النظام بواسطة هوفو سيستمز
    </div>

    <!-- Toast Container -->
    <div id="toast-container"
        class="fixed top-8 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <!-- MAIN APP WRAPPER -->
    <div id="app-wrapper" class="flex min-h-screen flex-col relative w-full">

        <a href="https://wa.me/" target="_blank"
            class="whatsapp-btn fixed bottom-16 right-4 md:bottom-8 md:right-8 bg-[#25D366] text-white w-14 h-14 rounded-full shadow-[0_10px_25px_rgba(37,211,102,0.4)] flex items-center justify-center hover:scale-110 transition-transform z-50 print-hidden">
            <svg viewBox="0 0 448 512" fill="currentColor" class="w-7 h-7">
                <path
                    d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157.1zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
            </svg>
        </a>

        @include('b2b::web.partials.header')

        <div class="flex flex-1 overflow-hidden relative">
            <!-- Sidebar Overlay -->
            <div id="sidebar-overlay" class="sidebar-overlay lg:hidden"></div>

            @include('b2b::web.partials.sidebar')

            <!-- Main Content Area -->
            <main
                class="flex-1 flex flex-col h-[calc(100vh-90px)] overflow-y-auto relative custom-scrollbar w-full bg-[#fafafa]">
                <div class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @include('b2b::web.partials.modals')

    <form id="logout-form" action="{{ route('client.logout') }}" method="POST" class="hidden">
        @csrf
    </form>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    @stack('scripts')
</body>

</html>
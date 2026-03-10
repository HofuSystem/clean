<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? trans('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ config('app.icon') }}" />

    <link rel="icon" href="{{ asset('frontend') }}/images/fevicon.png" type="image/gif" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('website/client/style.css') }}">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('control/assets/vendor/libs/toastr/toastr.css') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
</head>
@php
    $currentContract = Core\Users\Models\Contract::where('client_id', Auth::user()->id)->currentActive()->first();
@endphp

<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <!-- User Section -->
            <div class="user-section">
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="user-profile">
                    <img src="{{ Auth::user()->avatarUrl }}" alt="{{ trans('client.user') }}" class="user-avatar">
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->fullname }}</span>
                        <div>
                            <img src="{{ asset('website/client/image/icon.png') }}" alt="">
                            <span
                                class="user-role">{{ Auth::user()->is_active ? trans('client.active_account') : trans('client.inactive_account') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 fs-7" type="button"
                        id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i>
                        <span>{{ LaravelLocalization::getCurrentLocaleNative() }}</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <li><a class="dropdown-item"
                                    href="{{ LaravelLocalization::getLocalizedURL($localeCode) }}">{{ $properties['native'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="logo-section">
                    <img src="{{ config('app.logo') }}" alt="{{ trans('app.name') }}" class="header-logo">
                </div>


            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li class="sidebar-item @if(request()->routeIs('client.dashboard')) active @endif">
                        <a href="{{ route('client.dashboard') }}" class="sidebar-link">
                            <ion-icon name="home-outline"></ion-icon>
                            <span>{{ trans('client.dashboard') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item @if(request()->routeIs('client.order.index')) active @endif">
                        <a href="{{ route('client.order.index') }}" class="sidebar-link">
                            <ion-icon name="cart-outline"></ion-icon>
                            <span>{{ trans('client.hotel_orders') }}</span>
                        </a>
                    </li>
                    @if(isset($currentContract))
                        <li class="sidebar-item @if(request()->routeIs('client.clientsOrders')) active @endif">
                            <a href="{{ route('client.clientsOrders') }}" class="sidebar-link">
                                <ion-icon name="cart-outline"></ion-icon>
                                <span>{{ trans('client.clients_orders') }}</span>
                            </a>
                        </li>
                        <li class="sidebar-item @if(request()->routeIs('client.monthly-invoices')) active @endif">
                            <a href="{{ route('client.monthly-invoices') }}" class="sidebar-link">
                                <ion-icon name="document-outline"></ion-icon>
                                <span>{{ trans('client.monthly_invoice') }}</span>
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-item @if(request()->routeIs('client.schedule.*')) active @endif">
                        <a href="{{ route('client.schedule.index') }}" class="sidebar-link">
                            <ion-icon name="calendar-outline"></ion-icon>
                            <span>{{ trans('client.schedule') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item @if(request()->routeIs('client.points')) active @endif">
                        <a href="{{ route('client.points') }}" class="sidebar-link">
                            <ion-icon name="podium-outline"></ion-icon>
                            <span>{{ trans('client.my_points') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item @if(request()->routeIs('client.address.index')) active @endif">
                        <a href="{{ route('client.address.index') }}" class="sidebar-link">
                            <ion-icon name="location-outline"></ion-icon>
                            <span>{{ trans('client.address_details') }}</span>
                        </a>
                    </li>
                    <li
                        class="sidebar-item @if(request()->routeIs('client.profile.update-profile') || request()->routeIs('client.profile.update-password')) active @endif ">
                        <a href="{{ route('client.profile.update-profile') }}" class="sidebar-link">
                            <ion-icon name="create-outline"></ion-icon>
                            <span>{{ trans('client.edit_account') }}</span>
                        </a>
                    </li>
                    @if(isset($currentContract))
                        <li class="sidebar-item @if(request()->routeIs('client.contracts.contract')) active @endif">
                            <a href="{{ route('client.contracts.contract') }}" class="sidebar-link">
                                <ion-icon name="document-text-outline"></ion-icon>
                                <span>{{ trans('client.subscription') }}</span>
                            </a>
                        </li>
                        <li class="sidebar-item @if(request()->routeIs('client.contracts.customer-prices')) active @endif">
                            <a href="{{ route('client.contracts.customer-prices') }}" class="sidebar-link">
                                <ion-icon name="pricetags-outline"></ion-icon>
                                <span>{{ trans('client.customer_overprices') }}</span>
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-item">
                        <form action="{{ route('client.logout') }}" method="post">
                            @csrf
                            @method('post')
                            <a href="javascript:void(0)" class="sidebar-link" onclick="this.closest('form').submit()">
                                <ion-icon name="log-out-outline"></ion-icon>
                                <span>{{ trans('auth.logout') }}</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="main-footer" style="background-image: url({{ asset('website/client/image/image2.jpg') }});">
        <div class="footer-content">
            <div class="footer-social">
                <div class="social-links">
                    @php
                        $whatsapp = settings('whatsapp');
                        $email = settings('email');
                        $instagram = settings('instagram');
                        $twitter = settings('twitter');
                        $facebook = settings('facebook');
                        $youtube = settings('youtube');
                        $tiktok = settings('tiktok');
                        $g_app = settings('g_play_app');
                        $apple_app = settings('app_store_app');
                    @endphp
                    @if ($facebook)
                        <a href="{{ $facebook }}" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if ($twitter)
                        <a href="{{ $twitter }}" class="social-link"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if ($instagram)
                        <a href="{{ $instagram }}" class="social-link"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if ($youtube)
                        <a href="{{ $youtube }}" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    @endif
                    @if ($whatsapp)
                        <a href="https://wa.me/{{ $whatsapp }}" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    @endif
                    @if ($tiktok)
                        <a href="{{ $tiktok }}" class="social-link"><i class="fab fa-tiktok"></i></a>
                    @endif
                </div>
                <p class="copyright">© {{ date('Y') }} {{ trans('app.name') }}</p>
            </div>
            <div class="footer-apps">
                <p class="footer-text">{{ trans('client.download_app_message') }}</p>
                <div class="app-buttons">
                    <a href="{{ $g_app }}" target="_blank" rel="noopener"
                        onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'google', campaign_source: 'client_footer' });"
                        class="app-button">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
                            alt="{{ trans('client.google_play') }}">
                    </a>
                    <a href="{{ $apple_app }}" target="_blank" rel="noopener"
                        onclick="typeof gtag === 'function' && gtag('event', 'click_download', { app_store: 'apple', campaign_source: 'client_footer' });"
                        class="app-button">
                        <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg"
                            alt="{{ trans('client.app_store') }}">
                    </a>
                </div>
            </div>
        </div>
    </footer>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('control/assets/vendor/libs/toastr/toastr.js') }}"></script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="{{ asset('website/client/main.js') }}"></script>

    <style>
        /* Custom toast styling for better appearance */
        .toast-top-right {
            top: 20px;
            right: 20px;
        }

        .toast-success {
            background-color: #28a745;
            color: white;
        }

        .toast-error {
            background-color: #dc3545;
            color: white;
        }

        .toast-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .toast-info {
            background-color: #17a2b8;
            color: white;
        }
    </style>

    <script>
        // Wait for jQuery and toastr to be loaded
        $(document).ready(function () {
            // Configure toastr options
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };

                // Display success messages
                @if(session('success'))
                    toastr.success("{{ session('success') }}", "Success");
                @endif

                // Display error messages
                @if(session('error'))
                    toastr.error("{{ session('error') }}", "Error");
                @endif

                // Display validation errors
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        toastr.error("{{ $error }}", "Error");
                    @endforeach
                @endif

                // Display warning messages
                @if(session('warning'))
                    toastr.warning("{{ session('warning') }}", "Warning");
                @endif

                // Display info messages
                @if(session('info'))
                    toastr.info("{{ session('info') }}", "Info");
                @endif
            }
        });
    </script>
</body>

</html>
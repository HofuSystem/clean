<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
    class="scroll-smooth">

<head>
    {{-- ============================================================ --}}
    {{-- Google Tag Manager - MUST be first script in <head>         --}}
    {{-- ============================================================ --}}
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WQTQ9CV');</script>
    {{-- End Google Tag Manager --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ config('app.icon') }}">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? rtrim(config('app.url'), '/') . request()->getRequestUri() }}">

    {{-- ============================================================ --}}
    {{-- Hreflang – bilingual site (AR default, EN alternate)        --}}
    {{-- ============================================================ --}}
    <link rel="alternate" hreflang="ar" href="https://cleanstation.app/ar{{ request()->getPathInfo() !== '/' ? str_replace('/ar', '', str_replace('/en', '', request()->getPathInfo())) : '/' }}" />
    <link rel="alternate" hreflang="en" href="https://cleanstation.app/en{{ request()->getPathInfo() !== '/' ? str_replace('/ar', '', str_replace('/en', '', request()->getPathInfo())) : '/' }}" />
    <link rel="alternate" hreflang="x-default" href="https://cleanstation.app/ar{{ request()->getPathInfo() !== '/' ? str_replace('/ar', '', str_replace('/en', '', request()->getPathInfo())) : '/' }}" />

    {{-- ============================================================ --}}
    {{-- Open Graph & Twitter Card (Social Sharing Meta)             --}}
    {{-- ============================================================ --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ rtrim(config('app.url'), '/') . request()->getRequestUri() }}">
    <meta property="og:site_name" content="Clean Station">
    <meta property="og:locale" content="{{ LaravelLocalization::getCurrentLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ LaravelLocalization::getCurrentLocale() === 'ar' ? 'en_US' : 'ar_SA' }}">
    <meta property="og:title" content="{{ $metaTitle ?? 'Clean Station | أفضل تطبيق غسيل ملابس في السعودية' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'اطلب غسيل ملابسك وتتبع المندوب لحظياً. غسيل منفصل 100%، استلام وتسليم عند الباب خلال 24 ساعة. حمل التطبيق الآن!' }}">
    <meta property="og:image" content="https://cleanstation.app/assets/images/social-share-cover.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@CleanStationSA">
    <meta name="twitter:title" content="{{ $metaTitle ?? 'Clean Station | تطبيق غسيل الملابس رقم 1' }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'غسيل منفصل 100%، استلام وتسليم عند الباب خلال 24 ساعة. حمل التطبيق الآن!' }}">
    <meta name="twitter:image" content="https://cleanstation.app/assets/images/social-share-cover.jpg">

    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Cairo:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            800: '#075985',
                            900: '#0c4a6e'
                        },
                        accent: {
                            500: '#f59e0b'
                        },
                        dark: {
                            900: '#111827'
                        }
                    },
                    fontFamily: {
                        sans: ['Tajawal', 'sans-serif'],
                        en: ['Cairo', 'sans-serif']
                    },
                    screens: {
                        'xs': '475px'
                    }
                }
            }
        }
    </script>
    <style>
        /* Mobile Optimization */
        html,
        body {
            overflow-x: hidden;
            width: 100%;
            -webkit-text-size-adjust: 100%;
            touch-action: manipulation;
        }

        /* Language Support - Using html dir attribute */
        html[dir="rtl"] body {
            text-align: right;
            direction: rtl;
            font-family: 'Tajawal', sans-serif;
        }

        html[dir="ltr"] body {
            text-align: left;
            direction: ltr;
            font-family: 'Cairo', sans-serif;
        }

        /* Show/Hide language-specific content */
        html[dir="rtl"] .lang-en {
            display: none !important;
        }

        html[dir="rtl"] .lang-ar {
            display: inline-block !important;
        }

        html[dir="ltr"] .lang-ar {
            display: none !important;
        }

        html[dir="ltr"] .lang-en {
            display: inline-block !important;
        }

        /* Floating WhatsApp Button */
        .floating-wa {
            position: fixed;
            bottom: 20px;
            z-index: 999;
            background: #25d366;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
            transition: all 0.3s;
        }

        .floating-wa:hover {
            transform: scale(1.1);
        }

        html[dir="rtl"] .floating-wa {
            right: 20px;
            left: auto;
        }

        html[dir="ltr"] .floating-wa {
            left: 20px;
            right: auto;
        }

        /* Fix Table Overflow on Mobile */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Animation for dropdowns */
        .animate-fade-in-down {
            animation: fadeInDown 0.2s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Swiper Custom Styles */
        .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: #cbd5e1;
            opacity: 1;
            transition: all 0.3s;
        }

        .swiper-pagination-bullet-active {
            background: #0284c7;
            width: 24px;
            border-radius: 5px;
        }

        .swiper-slide {
            height: auto;
        }
    </style>
    <!-- Begin Linktree conversion tracking code -->
    <script>
        (function(l, i, n, k, t, r, ee) {
            l[t] = l[t] || function() {
                    (l[t].q = l[t].q || []).push(arguments)
                },
                l[t].l = 1 * new Date();
            r = i.createElement(n);
            ee = i.getElementsByTagName(n)[0];
            r.async = 1;
            r.src = k;
            ee.parentNode.insertBefore(r, ee)
        })
        (window, document, 'script', 'https://assets.production.linktr.ee/ltpixel/ltpix.min.js?t=' + 864e5 * Math.ceil(
            new Date / 864e5), 'lti')
    </script>
    <script>
        lti('init', 'LTU-446620bc-c895-4910-b5de-3b2053381f18')
        lti('pageloaded')
    </script>
    <!-- End Linktree conversion tracking code -->

    <!-- Snap Pixel Code -->
    <script>
        (function(e, t, n) {
            if (e.snaptr) return;
            var a = e.snaptr = function() {
                a.handleRequest ? a.handleRequest.apply(a, arguments) : a.queue.push(arguments)
            };
            a.queue = [];
            var s = 'script';
            r = t.createElement(s);
            r.async = !0;
            r.src = n;
            var u = t.getElementsByTagName(s)[0];
            u.parentNode.insertBefore(r, u);
        })(window, document,
            'https://sc-static.net/scevent.min.js');

        snaptr('init', '3da29568-b309-48dd-86d1-84323f2e2699', {
            'user_email': '__INSERT_USER_EMAIL__'
        });

        snaptr('track', 'PAGE_VIEW');
    </script>
    <!-- End Snap Pixel Code -->

    <!-- Tiktok Pixel Code -->
    <script>
        ! function(w, d, t) {
            w.TiktokAnalyticsObject = t;
            var ttq = w[t] = w[t] || [];
            ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias",
                "group", "enableCookie", "disableCookie"
            ], ttq.setAndDefer = function(t, e) {
                t[e] = function() {
                    t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
                }
            };
            for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
            ttq.instance = function(t) {
                for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]);
                return e
            }, ttq.load = function(e, n) {
                var i = "https://analytics.tiktok.com/i18n/pixel/events.js";
                ttq._i = ttq._i || {}, ttq._i[e] = [], ttq._i[e]._u = i, ttq._t = ttq._t || {}, ttq._t[e] = +new Date,
                    ttq._o = ttq._o || {}, ttq._o[e] = n || {};
                n = document.createElement("script");
                n.type = "text/javascript", n.async = !0, n.src = i + "?sdkid=" + e + "&lib=" + t;
                e = document.getElementsByTagName("script")[0];
                e.parentNode.insertBefore(n, e)
            };

            ttq.load('CKPTFQ3C77U1BIIGBE10');
            ttq.page();
        }(window, document, 'ttq');
    </script>
    <!-- End Tiktok Pixel Code -->


    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-X8KY7HG0VB"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-X8KY7HG0VB');
    </script>
    <!--End Google tag (gtag.js) -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-JM4ZEBBXSJ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-JM4ZEBBXSJ');
    </script>
    <!--End Google tag (gtag.js) -->


    {{-- ============================================================ --}}
    {{-- Schema Markup / JSON-LD – Rich Snippets                     --}}
    {{-- ============================================================ --}}

    {{-- 1. DryCleaningOrLaundry – Local Business (Home page) --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "DryCleaningOrLaundry",
      "name": "Clean Station - كلين ستيشن",
      "image": "https://cleanstation.app/assets/images/social-share-cover.jpg",
      "@id": "https://cleanstation.app/",
      "url": "https://cleanstation.app/",
      "telephone": "+966559098685",
      "email": "support@cleanstation.app",
      "logo": "https://cleanstation.app/images/logo.png",
      "sameAs": [
        "https://www.instagram.com/clean.stationn",
        "https://twitter.com/CleanStationSA",
        "https://www.snapchat.com/add/clean.stationn"
      ],
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Riyadh",
        "addressRegion": "Riyadh Province",
        "addressCountry": "SA"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"
        ],
        "opens": "00:00",
        "closes": "23:59"
      },
      "priceRange": "$$"
    }
    </script>

    {{-- 2. SoftwareApplication – Mobile App (Home page) --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "Clean Station App",
      "operatingSystem": "iOS, Android",
      "applicationCategory": "LifestyleApplication",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "ratingCount": "12000"
      },
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "SAR"
      }
    }
    </script>

    <script>
        (function(d, t) {
            var BASE_URL = "https://app.karzoun.chat";
            var g = d.createElement(t),
                s = d.getElementsByTagName(t)[0];
            g.src = BASE_URL + "/packs/js/sdk.js";
            g.defer = true;
            g.async = true;
            s.parentNode.insertBefore(g, s);
            g.onload = function() {
                window.chatwootSDK.run({
                    websiteToken: 'NXcdSvd43X7vBeZLRQTBdaBt',
                    baseUrl: BASE_URL
                })
            }
        })(document, "script");
    </script>
</head>

<body
    class="bg-gray-50 text-gray-900 antialiased selection:bg-brand-200 selection:text-brand-900 flex flex-col min-h-screen">

    {{-- Google Tag Manager (noscript) - immediately after <body> --}}
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WQTQ9CV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    {{-- End Google Tag Manager (noscript) --}}

    @if (setting('whatsapp'))
        <a href="https://wa.me/{{ setting('whatsapp') }}" target="_blank" class="floating-wa"
           id="floating-whatsapp-btn"
           onclick="window.cleanTrack && window.cleanTrack.contact('whatsapp')"><i
                class="fa-brands fa-whatsapp text-3xl"></i></a>
    @endif

    @include('layouts.partials.navbar')

    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    </script>

    {{-- ============================================================ --}}
    {{-- DataLayer Event Helpers – centralised tracking              --}}
    {{-- ============================================================ --}}
    <script>
    window.dataLayer = window.dataLayer || [];

    window.cleanTrack = {
        /**
         * Fire an app_download_click event.
         * @param {string} platform   - 'ios' | 'android'
         * @param {string} location   - e.g. 'hero', 'navbar', 'footer'
         */
        appDownload: function(platform, location) {
            dataLayer.push({
                'event': 'app_download_click',
                'platform': platform,
                'button_location': location || 'unknown'
            });
            // Mirror to TikTok Pixel
            if (typeof ttq !== 'undefined') {
                ttq.track('ClickButton', { description: 'app_download_' + platform });
            }
            // Mirror to Snap Pixel
            if (typeof snaptr !== 'undefined') {
                snaptr('track', 'APP_INSTALL');
            }
        },

        /**
         * Fire a contact_click event.
         * @param {string} method - 'whatsapp' | 'phone' | 'email'
         */
        contact: function(method) {
            dataLayer.push({
                'event': 'contact_click',
                'contact_method': method || 'unknown'
            });
            if (typeof ttq !== 'undefined') {
                ttq.track('Contact', { description: 'contact_' + method });
            }
        },

        /**
         * Push a virtual pageview (for SPA-style navigation if needed).
         * @param {string} path  - e.g. '/faq'
         * @param {string} title - e.g. 'FAQ'
         */
        virtualPageview: function(path, title) {
            dataLayer.push({
                'event': 'virtual_pageview',
                'page_path': path,
                'page_title': title
            });
        }
    };
    </script>
    
    {{-- SweetAlert flash messages: success, error, or validation errors --}}
    @if (session('success_message') || session('success') || session('error') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successMsg = {!! json_encode(session('success_message')) !!};       
                var errorMsg = {!! json_encode(session('error')) !!};
                var validationErrors = {!! json_encode($errors->all()) !!};
                var opts = {
                    confirmButtonColor: '#0284c7'
                };

                if (successMsg) {
                    Swal.fire({
                        icon: 'success',
                        title: {!! json_encode(trans('Success')) !!},
                        text: successMsg,
                        ...opts
                    });
                } else if (errorMsg) {
                    Swal.fire({
                        icon: 'error',
                        title: {!! json_encode(trans('Error')) !!},
                        text: errorMsg,
                        ...opts
                    });
                } else if (validationErrors && validationErrors.length) {
                    opts.icon = 'error';
                    opts.title = validationErrors.length > 1 ? {!! json_encode(trans('Please correct the following errors:')) !!} : {!! json_encode(trans('Error')) !!};
                    if (validationErrors.length === 1) {
                        opts.text = validationErrors[0];
                    } else {
                        opts.html = '<ul class="text-start list-disc list-inside mt-2 space-y-1">' + validationErrors
                            .map(function(m) {
                                return '<li>' + m + '</li>';
                            }).join('') + '</ul>';
                    }
                    Swal.fire(opts);
                }
            });
        </script>
    @endif
    @stack('scripts')
</body>

</html>

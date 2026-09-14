<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
    class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    @stack('preloads')
    {{-- ============================================================ --}}
    {{-- Unified Lazy-Loaded Tracking Pixels (GTM, GA, Snap, TikTok, Linktree) --}}
    {{-- ============================================================ --}}
    <script>
        // 1. Google Tag Manager & Google Analytics stubs
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        
        // 2. Facebook/Meta Pixel stub (GTM template requires fbq defined globally)
        window.fbq = window.fbq || function() {
            (fbq.q = fbq.q || []).push(arguments);
        };
        fbq.q = fbq.q || [];

        // 3. Snap Pixel stub
        window.snaptr = function() {
            snaptr.handleRequest ? snaptr.handleRequest.apply(snaptr, arguments) : snaptr.queue.push(arguments);
        };
        snaptr.queue = [];

        // 4. TikTok Pixel stub (Robust environment configuration to prevent setting '_env' of undefined)
        window.TiktokAnalyticsObject = 'ttq';
        window.ttq = window.ttq || [];
        ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias", "group", "enableCookie", "disableCookie"];
        ttq.setAndDefer = function(t, e) {
            t[e] = function() {
                t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
            }
        };
        for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
        ttq.instance = function(t) {
            for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]);
            return e;
        };
        var raw_i = {};
        var proxy_i = new Proxy(raw_i, {
            get: function(target, prop) {
                if (!(prop in target)) {
                    target[prop] = [];
                    target[prop]._u = "https://analytics.tiktok.com/i18n/pixel/events.js";
                }
                return target[prop];
            }
        });

        Object.defineProperty(ttq, '_i', {
            get: function() { return proxy_i; },
            set: function(val) {
                if (val && typeof val === 'object') {
                    for (var key in val) {
                        if (val.hasOwnProperty(key)) {
                            raw_i[key] = val[key];
                        }
                    }
                }
            },
            configurable: true,
            enumerable: true
        });

        var raw_o = {};
        Object.defineProperty(ttq, '_o', {
            get: function() { return raw_o; },
            set: function(val) {
                if (val && typeof val === 'object') {
                    for (var key in val) {
                        if (val.hasOwnProperty(key)) {
                            raw_o[key] = val[key];
                        }
                    }
                }
            },
            configurable: true,
            enumerable: true
        });

        var raw_t = {};
        Object.defineProperty(ttq, '_t', {
            get: function() { return raw_t; },
            set: function(val) {
                if (val && typeof val === 'object') {
                    for (var key in val) {
                        if (val.hasOwnProperty(key)) {
                            raw_t[key] = val[key];
                        }
                    }
                }
            },
            configurable: true,
            enumerable: true
        });

        // Pre-populate default pixel timestamp and options
        var defaultPixelId = 'CKPTFQ3C77U1BIIGBE10';
        raw_t[defaultPixelId] = +new Date();
        raw_o[defaultPixelId] = {};

        var ttqPixelsToLoad = [];
        ttq.load = function(e, n) {
            ttq._i[e] = [];
            ttq._i[e]._u = "https://analytics.tiktok.com/i18n/pixel/events.js";
            ttq._t[e] = +new Date();
            ttq._o[e] = n || {};
            if (ttqPixelsToLoad.indexOf(e) === -1) {
                ttqPixelsToLoad.push(e);
            }
            if (window.trackingActive) {
                injectTikTokScript(e);
            }
        };

        function injectTikTokScript(e) {
            var i = "https://analytics.tiktok.com/i18n/pixel/events.js";
            var scr = document.createElement("script");
            scr.type = "text/javascript";
            scr.async = true;
            scr.src = i + "?sdkid=" + e + "&lib=ttq";
            var firstScr = document.getElementsByTagName("script")[0];
            firstScr.parentNode.insertBefore(scr, firstScr);
        }

        // 5. Linktree stub
        window.lti = window.lti || function() {
            (lti.q = lti.q || []).push(arguments);
        };
        lti.l = 1 * new Date();

        (function() {
            var trackingLoaded = false;
            function initTracking() {
                if (trackingLoaded) return;
                trackingLoaded = true;
                window.trackingActive = true;

                // 1. Google Tag Manager
                (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','GTM-WQTQ9CV');

                // 2. Snap Pixel
                var snapScript = document.createElement('script');
                snapScript.async = true;
                snapScript.src = 'https://sc-static.net/scevent.min.js';
                var firstScript = document.getElementsByTagName('script')[0];
                firstScript.parentNode.insertBefore(snapScript, firstScript);
                snaptr('init', '3da29568-b309-48dd-86d1-84323f2e2699', {
                    'user_email': '__INSERT_USER_EMAIL__'
                });
                snaptr('track', 'PAGE_VIEW');

                // 3. TikTok Pixel
                for (var k = 0; k < ttqPixelsToLoad.length; k++) {
                    injectTikTokScript(ttqPixelsToLoad[k]);
                }
                if (ttqPixelsToLoad.length === 0) {
                    ttq.load('CKPTFQ3C77U1BIIGBE10');
                }
                ttq.page();

                // 4. Linktree
                var ltScript = document.createElement('script');
                ltScript.async = true;
                ltScript.src = 'https://assets.production.linktr.ee/ltpixel/ltpix.min.js?t=' + 864e5 * Math.ceil(new Date / 864e5);
                var firstScrLt = document.getElementsByTagName('script')[0];
                firstScrLt.parentNode.insertBefore(ltScript, firstScrLt);
                lti('init', 'LTU-446620bc-c895-4910-b5de-3b2053381f18');
                lti('pageloaded');

                // 5. Google Analytics (gtag.js)
                // GA4 tags removed to prevent double page_view. GTM (GTM-WQTQ9CV) is the primary management layer.
            }

            // Keep third-party pixels out of the critical 10-second load window.
            // Interaction still starts tracking immediately when intent is clear.
            var trackingTimeout = setTimeout(initTracking, 12000);
            window.addEventListener('scroll', initTracking, { passive: true });
            window.addEventListener('touchstart', initTracking, { passive: true });
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ config('app.icon') }}">

    @php
        $actualDesc = $metaDescription ?? '';
        if (LaravelLocalization::getCurrentLocale() === 'ar') {
            $actualKeywords = "مغسلة ملابس قريبة مني, مغسلة ملابس بالرياض, غسيل ملابس بالرياض, مغاسل الرياض, دراي كلين الرياض, غسيل ملابس منفصل, استلام وتوصيل ملابس, غسيل سجاد بالرياض, مغسلة سجاد بالرياض, غسيل مفروشات بالرياض, غسيل كنب بالرياض, غسيل أحذية بالرياض, توصيل هدايا بالرياض, توصيل باقات ورد الرياض, محلات ورد بالرياض, تطبيق غسيل ملابس, مغسلة ملابس استلام وتوصيل, أفضل مغسلة بالرياض, غسيل بطانيات الرياض, تنظيف أحذية الرياض";
        } else {
            $actualKeywords = "laundry near me, laundry app Riyadh, dry cleaning Riyadh, separate washing Riyadh, free laundry pickup, carpet cleaning Riyadh, shoe cleaning Riyadh, gift delivery Riyadh, flower delivery Riyadh, online laundry Riyadh, best laundry Riyadh, premium flower boutique Riyadh, express laundry Riyadh, door to door laundry Riyadh, dry cleaner near me Riyadh";
        }
        
        $pathSuffix = preg_replace('/^\/(ar|en)\b/', '', request()->getPathInfo());
        if ($pathSuffix === '') {
            $pathSuffix = '/';
        }

        $socialShareImagePath = \Core\Settings\Services\SettingsService::getDataBaseSetting('social_share_image');
        $socialShareImageUrl = $socialShareImagePath
            ? \Core\Settings\Services\SettingsService::getDataBaseSettingImage('social_share_image')
            : asset('assets/images/social-share-cover.jpg');
    @endphp

    @php
        $resolvedCanonicalUrl = $canonicalUrl ?? \App\Support\CanonicalUrl::fromRequest(request(), config('app.url'));
        $resolvedMetaTitle = trim($metaTitle ?? '') ?: (trim($title ?? '') ?: 'Clean Station');
    @endphp
    <title>{{ $resolvedMetaTitle }}</title>
    <meta name="description" content="{{ $actualDesc }}">
    <meta name="keywords" content="{{ $actualKeywords }}">
    <link rel="canonical" href="{{ $resolvedCanonicalUrl }}">

    {{-- ============================================================ --}}
    {{-- Hreflang – bilingual site (AR default, EN alternate)        --}}
    {{-- ============================================================ --}}
    <link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar{{ $pathSuffix !== '/' ? $pathSuffix : '' }}" />
    <link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en{{ $pathSuffix !== '/' ? $pathSuffix : '' }}" />
    <link rel="alternate" hreflang="x-default" href="https://cleanstation.app{{ $pathSuffix !== '/' ? $pathSuffix : '' }}" />

    {{-- ============================================================ --}}
    {{-- Open Graph & Twitter Card (Social Sharing Meta)             --}}
    {{-- ============================================================ --}}
    <meta property="og:type" content="{{ isset($blog) ? 'article' : 'website' }}">
    <meta property="og:url" content="{{ $resolvedCanonicalUrl }}">
    <meta property="og:site_name" content="Clean Station">
    <meta property="og:locale" content="{{ LaravelLocalization::getCurrentLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ LaravelLocalization::getCurrentLocale() === 'ar' ? 'en_US' : 'ar_SA' }}">
    <meta property="og:title" content="{{ $metaTitle ?? 'Clean Station | أفضل تطبيق غسيل ملابس في السعودية' }}">
    <meta property="og:description" content="{{ $actualDesc ?: 'اطلب غسيل ملابسك وتتبع المندوب لحظياً. غسيل منفصل 100%، استلام وتسليم عند الباب خلال 24 ساعة. حمل التطبيق الآن!' }}">
    <meta property="og:image" content="{{ $socialShareImageUrl }}">
    <meta property="og:image:secure_url" content="{{ $socialShareImageUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $metaTitle ?? 'Clean Station' }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@CleanStationSA">
    <meta name="twitter:title" content="{{ $metaTitle ?? 'Clean Station | تطبيق غسيل الملابس رقم 1' }}">
    <meta name="twitter:description" content="{{ $actualDesc ?: 'غسيل منفصل 100%، استلام وتسليم عند الباب خلال 24 ساعة. حمل التطبيق الآن!' }}">
    <meta name="twitter:image" content="{{ $socialShareImageUrl }}">

    @if (Vite::isRunningHot())
        @vite(['resources/css/landing.css'])
    @else
        <style>{!! Vite::content('resources/css/landing.css') !!}</style>
    @endif
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/vendor/landing-icons-full.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ Vite::asset('resources/css/vendor/landing-icons-full.css') }}"></noscript>

    <!-- Unified Lazy-Loaded Tracking Pixels: Moved to head -->


    {{-- ============================================================ --}}
    {{-- Schema Markup / JSON-LD – Rich Snippets                     --}}
    {{-- ============================================================ --}}
    @yield('schema')

    @if(request()->routeIs('home'))
        @if(LaravelLocalization::getCurrentLocale() === 'ar')
            {{-- Arabic JSON-LD Schema --}}
            <script type="application/ld+json">
            {
              "@@context": "https://schema.org",
              "@graph": [
                {
                  "@type": "Organization",
                  "@id": "https://cleanstation.app/#organization",
                  "name": "كلين ستيشن",
                  "alternateName": "Clean Station",
                  "url": "https://cleanstation.app",
                  "logo": "https://cleanstation.app/assets/images/logo.png",
                  "areaServed": { "@type": "City", "name": "Riyadh" },
                  "makesOffer": [
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "تطبيق غسيل ملابس بالرياض",
                        "serviceType": "Laundry App",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "غسيل وكوي الملابس",
                        "serviceType": "Laundry and Ironing",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "دراي كلين بالرياض",
                        "serviceType": "Dry Cleaning",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "استلام وتوصيل منزلي",
                        "serviceType": "Pickup and Delivery",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "غسيل بطانيات ومفروشات بالرياض",
                        "serviceType": "Blanket and Bedding Cleaning",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "غسيل سجاد وموكيت بالرياض",
                        "serviceType": "Carpet and Rug Cleaning",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "تنظيف وتلميع الأحذية",
                        "serviceType": "Shoe Cleaning",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "طلب الورود والهدايا بالرياض",
                        "serviceType": "Flowers and Gifts Ordering",
                        "areaServed": "Riyadh",
                        "description": "الورود والهدايا خدمة مستقلة لطلب الورود والهدايا من داخل تطبيق كلين ستيشن، ويمكن طلبها بدون طلب غسيل."
                      }
                    }
                  ]
                },
                {
                  "@type": "WebSite",
                  "@id": "https://cleanstation.app/#website",
                  "url": "https://cleanstation.app",
                  "name": "كلين ستيشن",
                  "alternateName": "Clean Station",
                  "inLanguage": "ar-SA",
                  "publisher": { "@id": "https://cleanstation.app/#organization" }
                },
                {
                  "@type": "MobileApplication",
                  "@id": "https://cleanstation.app/#mobileapplication",
                  "name": "كلين ستيشن",
                  "alternateName": "Clean Station",
                  "operatingSystem": "iOS, Android",
                  "applicationCategory": "LifestyleApplication",
                  "url": "https://cleanstation.app.link/?channel=website",
                  "installUrl": "https://cleanstation.app.link/?channel=website",
                  "downloadUrl": "https://cleanstation.app.link/?channel=website",
                  "description": "تطبيق كلين ستيشن يقدم خدمات الغسيل، الكوي، الدراي كلين، البطانيات، السجاد، الاستلام والتوصيل، والخدمة المنزلية لطلب الورود والهدايا داخل الرياض.",
                  "areaServed": { "@type": "City", "name": "Riyadh" },
                  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "SAR" }
                }
              ]
            }
            </script>

            {{-- FAQPage Arabic --}}
            <script type="application/ld+json">
            {
              "@@context": "https://schema.org",
              "@type": "FAQPage",
              "inLanguage": "ar-SA",
              "mainEntity": [
                {
                  "@type": "Question",
                  "name": "هل تطبيق كلين ستيشن يوفر خدمات غسيل ملابس في الرياض؟",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "نعم، يوفر تطبيق كلين ستيشن خدمات غسيل ملابس في الرياض تشمل الغسيل، الكوي، الدراي كلين، البطانيات، السجاد، والاستلام والتوصيل المنزلي."
                  }
                },
                {
                  "@type": "Question",
                  "name": "هل يمكن طلب الورود والهدايا بدون طلب غسيل؟",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "نعم، الورود والهدايا خدمة مستقلة داخل تطبيق كلين ستيشن، ويمكن طلبها بدون طلب غسيل."
                  }
                }
              ]
            }
            </script>
        @else
            {{-- English JSON-LD Schema --}}
            <script type="application/ld+json">
            {
              "@@context": "https://schema.org",
              "@graph": [
                {
                  "@type": "Organization",
                  "@id": "https://cleanstation.app/#organization",
                  "name": "Clean Station",
                  "alternateName": "كلين ستيشن",
                  "url": "https://cleanstation.app",
                  "logo": "https://cleanstation.app/assets/images/logo.png",
                  "areaServed": { "@type": "City", "name": "Riyadh" },
                  "makesOffer": [
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "Laundry App in Riyadh",
                        "serviceType": "Laundry App",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "Laundry and Ironing",
                        "serviceType": "Laundry and Ironing",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "Dry Cleaning in Riyadh",
                        "serviceType": "Dry Cleaning",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "Doorstep Pickup and Delivery",
                        "serviceType": "Pickup and Delivery",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "Blanket and Bedding Cleaning",
                        "serviceType": "Blanket and Bedding Cleaning",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "Carpet and Rug Cleaning",
                        "serviceType": "Carpet and Rug Cleaning",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "Shoe Cleaning",
                        "serviceType": "Shoe Cleaning",
                        "areaServed": "Riyadh"
                      }
                    },
                    {
                      "@type": "Offer",
                      "itemOffered": {
                        "@type": "Service",
                        "name": "Flowers and Gifts Ordering in Riyadh",
                        "serviceType": "Flowers and Gifts Ordering",
                        "areaServed": "Riyadh",
                        "description": "A separate flowers and gifts ordering service inside the Clean Station app. It can be ordered without placing a laundry order."
                      }
                    }
                  ]
                },
                {
                  "@type": "WebSite",
                  "@id": "https://cleanstation.app/#website",
                  "url": "https://cleanstation.app",
                  "name": "Clean Station",
                  "alternateName": "كلين ستيشن",
                  "inLanguage": "en-SA",
                  "publisher": { "@id": "https://cleanstation.app/#organization" }
                },
                {
                  "@type": "MobileApplication",
                  "@id": "https://cleanstation.app/#mobileapplication",
                  "name": "Clean Station",
                  "alternateName": "كلين ستيشن",
                  "operatingSystem": "iOS, Android",
                  "applicationCategory": "LifestyleApplication",
                  "url": "https://cleanstation.app.link/?channel=website",
                  "installUrl": "https://cleanstation.app.link/?channel=website",
                  "downloadUrl": "https://cleanstation.app.link/?channel=website",
                  "description": "Clean Station app offers laundry, ironing, dry cleaning, blanket cleaning, carpet cleaning, doorstep pickup and delivery, plus a separate flowers and gifts ordering service in Riyadh.",
                  "areaServed": { "@type": "City", "name": "Riyadh" },
                  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "SAR" }
                }
              ]
            }
            </script>

            {{-- FAQPage English --}}
            <script type="application/ld+json">
            {
              "@@context": "https://schema.org",
              "@type": "FAQPage",
              "inLanguage": "en-SA",
              "mainEntity": [
                {
                  "@type": "Question",
                  "name": "Is Clean Station a laundry app in Riyadh?",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, Clean Station is a laundry and services app in Riyadh offering laundry, ironing, dry cleaning, blanket cleaning, carpet cleaning, and doorstep pickup and delivery."
                  }
                },
                {
                  "@type": "Question",
                  "name": "Can flowers and gifts be ordered without a laundry order?",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, flowers and gifts are a separate service inside the Clean Station app and can be ordered without placing a laundry order."
                  }
                }
              ]
            }
            </script>
        @endif
    @endif

    <script>
        function loadKarzounChat() {
            if (window.karzounChatLoaded) return;
            window.karzounChatLoaded = true;
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
        }
        window.addEventListener('scroll', loadKarzounChat, { passive: true });
        window.addEventListener('touchstart', loadKarzounChat, { passive: true });
        setTimeout(loadKarzounChat, 4000);
    </script>
    @if(isset($blog))
        @include('partials.blog-structured-data')
    @endif
</head>

<body
    class="bg-gray-50 text-gray-900 antialiased selection:bg-brand-200 selection:text-brand-900 flex flex-col min-h-screen">

    {{-- Google Tag Manager (noscript) - immediately after <body> --}}
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WQTQ9CV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    {{-- End Google Tag Manager (noscript) --}}

    @if (setting('whatsapp'))
        <a href="https://wa.me/{{ setting('whatsapp') }}" target="_blank" rel="noopener" aria-label="{{ trans('Contact us on WhatsApp') }}" class="floating-wa"
           id="floating-whatsapp-btn"
           onclick="window.cleanTrack && window.cleanTrack.contact('whatsapp')"><i
                class="fa-brands fa-whatsapp text-3xl"></i></a>
    @endif

    @include('layouts.partials.navbar')

    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
    @if (session('success_message') || session('success') || session('error') || $errors->any())
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    @endif
    <script>
        function initLandingAnimations() {
            if (!window.AOS || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            try {
                AOS.init({ duration: 800, easing: 'ease-in-out', once: true, mirror: false });
                document.documentElement.classList.add('aos-ready');
            } catch (error) {
                document.documentElement.classList.remove('aos-ready');
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initLandingAnimations, { once: true });
        } else {
            initLandingAnimations();
        }
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
        },

        /**
         * pricing_search — when user searches/filters the pricing table.
         * @param {string} query            - search term
         * @param {string} serviceCategory  - active category tab
         */
        pricingSearch: function(query, serviceCategory) {
            dataLayer.push({
                'event': 'pricing_search',
                'search_query': query || '',
                'service_category': serviceCategory || 'all',
                'page_path': window.location.pathname,
                'language': document.documentElement.lang || 'ar'
            });
        },

        /**
         * pricing_cta_click — when user clicks "order" from pricing page.
         * @param {string} itemOrService - item/service name
         * @param {string} placement     - e.g. 'table_row', 'header_cta'
         */
        pricingCta: function(itemOrService, placement) {
            dataLayer.push({
                'event': 'pricing_cta_click',
                'item_service': itemOrService || '',
                'placement': placement || 'unknown',
                'page_path': window.location.pathname
            });
        },

        /**
         * coverage_search — when user searches for a district/city.
         * @param {string} query - search term
         */
        coverageSearch: function(query) {
            dataLayer.push({
                'event': 'coverage_search',
                'search_query': query || '',
                'page_path': window.location.pathname,
                'language': document.documentElement.lang || 'ar'
            });
        },

        /**
         * coverage_result — after search returns results.
         * @param {string} status  - 'active' | 'coming_soon' | 'not_available'
         * @param {string} district - district/city name searched
         */
        coverageResult: function(status, district) {
            dataLayer.push({
                'event': 'coverage_result',
                'coverage_status': status || 'unknown',
                'district_city': district || '',
                'page_path': window.location.pathname
            });
        },

        /**
         * b2b_lead_submit — after B2B form is successfully sent.
         * @param {string} sector     - e.g. 'hotel', 'restaurant'
         * @param {string} volumeBand - e.g. 'small', 'medium', 'large'
         */
        b2bLeadSubmit: function(sector, volumeBand) {
            dataLayer.push({
                'event': 'b2b_lead_submit',
                'lead_sector': sector || 'unknown',
                'volume_band': volumeBand || 'unknown',
                'page_path': window.location.pathname,
                'language': document.documentElement.lang || 'ar'
            });
            if (typeof ttq !== 'undefined') {
                ttq.track('SubmitForm', { description: 'b2b_lead_' + (sector || '') });
            }
        },

        /**
         * contact_form_confirmed — only after user confirms sending via WhatsApp/form backend.
         */
        contactFormConfirmed: function() {
            dataLayer.push({
                'event': 'contact_form_confirmed',
                'page_path': window.location.pathname,
                'language': document.documentElement.lang || 'ar'
            });
            if (typeof ttq !== 'undefined') {
                ttq.track('Contact', { description: 'contact_form_confirmed' });
            }
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

    <!-- Sticky Mobile CTA -->
    <div id="smart-mobile-cta" class="sticky-mobile-cta">
        <div class="cta-content">
            <div class="stars">
                ★ ★ ★ ★ ★
            </div>
            <div class="sticky-mobile-cta-title">
                @if(LaravelLocalization::getCurrentLocale() === 'ar')
                    أسرع تطبيق غسيل بالرياض
                @else
                    Fastest Laundry App in Riyadh
                @endif
            </div>
        </div>
        <a href="https://cleanstation.app.link/?channel=website" class="sticky-mobile-cta-btn btn-glow-pulse">
            @if(LaravelLocalization::getCurrentLocale() === 'ar')
                حمّل التطبيق
            @else
                Download App
            @endif
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stickyCta = document.getElementById('smart-mobile-cta');
            if (stickyCta) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        stickyCta.classList.add('visible');
                    } else {
                        stickyCta.classList.remove('visible');
                    }
                }, { passive: true });
            }
        });
    </script>
</body>

</html>

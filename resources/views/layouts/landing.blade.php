<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
    class="scroll-smooth">

<head>
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
                var gaScript1 = document.createElement('script');
                gaScript1.async = true;
                gaScript1.src = 'https://www.googletagmanager.com/gtag/js?id=G-X8KY7HG0VB';
                var firstScrGa = document.getElementsByTagName('script')[0];
                firstScrGa.parentNode.insertBefore(gaScript1, firstScrGa);
                gtag('config', 'G-X8KY7HG0VB');

                var gaScript2 = document.createElement('script');
                gaScript2.async = true;
                gaScript2.src = 'https://www.googletagmanager.com/gtag/js?id=G-JM4ZEBBXSJ';
                firstScrGa.parentNode.insertBefore(gaScript2, firstScrGa);
                gtag('config', 'G-JM4ZEBBXSJ');
            }

            var trackingTimeout = setTimeout(initTracking, 4000);
            window.addEventListener('scroll', initTracking, { passive: true });
            window.addEventListener('touchstart', initTracking, { passive: true });
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    @endphp

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $actualDesc }}">
    <meta name="keywords" content="{{ $actualKeywords }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? rtrim(config('app.url'), '/') . request()->getRequestUri() }}">

    {{-- ============================================================ --}}
    {{-- Hreflang – bilingual site (AR default, EN alternate)        --}}
    {{-- ============================================================ --}}
    <link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar{{ $pathSuffix !== '/' ? $pathSuffix : '' }}" />
    <link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en{{ $pathSuffix !== '/' ? $pathSuffix : '' }}" />
    <link rel="alternate" hreflang="x-default" href="https://cleanstation.app{{ $pathSuffix !== '/' ? $pathSuffix : '' }}" />

    {{-- ============================================================ --}}
    {{-- Open Graph & Twitter Card (Social Sharing Meta)             --}}
    {{-- ============================================================ --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ rtrim(config('app.url'), '/') . request()->getRequestUri() }}">
    <meta property="og:site_name" content="Clean Station">
    <meta property="og:locale" content="{{ LaravelLocalization::getCurrentLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ LaravelLocalization::getCurrentLocale() === 'ar' ? 'en_US' : 'ar_SA' }}">
    <meta property="og:title" content="{{ $metaTitle ?? 'Clean Station | أفضل تطبيق غسيل ملابس في السعودية' }}">
    <meta property="og:description" content="{{ $actualDesc ?: 'اطلب غسيل ملابسك وتتبع المندوب لحظياً. غسيل منفصل 100%، استلام وتسليم عند الباب خلال 24 ساعة. حمل التطبيق الآن!' }}">
    <meta property="og:image" content="https://cleanstation.app/assets/images/social-share-cover.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@CleanStationSA">
    <meta name="twitter:title" content="{{ $metaTitle ?? 'Clean Station | تطبيق غسيل الملابس رقم 1' }}">
    <meta name="twitter:description" content="{{ $actualDesc ?: 'غسيل منفصل 100%، استلام وتسليم عند الباب خلال 24 ساعة. حمل التطبيق الآن!' }}">
    <meta name="twitter:image" content="https://cleanstation.app/assets/images/social-share-cover.jpg">

    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Cairo:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    @if(config('app.env') === 'local')
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
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
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

        /* =========================================================
           UI/UX & Premium Enhancements from cleanstation_dev
           ========================================================= */

        /* 1. تأثير النبض المتوهج لأزرار التحميل (لجذب الانتباه) */
        .btn-glow-pulse {
            animation: glow-pulse 2s infinite;
            box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.7);
        }

        @keyframes glow-pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(14, 165, 233, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0); }
        }

        /* 2. الشريط اللاصق السفلي لأجهزة الجوال (Sticky Mobile CTA) */
        .sticky-mobile-cta {
            position: fixed;
            bottom: -100px; /* مخفي افتراضياً */
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid #f1f5f9;
            padding: 12px 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 9999;
            transition: bottom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .sticky-mobile-cta.visible {
            bottom: 0;
        }

        /* إخفاء الشريط اللاصق في الشاشات الكبيرة */
        @media (min-width: 768px) {
            .sticky-mobile-cta {
                display: none !important;
            }
        }

        .sticky-mobile-cta .stars {
            color: #fbbf24;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .sticky-mobile-cta-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .sticky-mobile-cta-btn {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
        }

        /* 3. تأثير "طفو" للبطاقات (Floating Cards) */
        .card-float-hover {
            transition: all 0.3s ease;
        }
        .card-float-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #e0f2fe;
        }

        /* 5. قسم الهدايا والورود الفاخر (Glassmorphism Gifts Section) */
        .glass-container {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.03), 
                        inset 0 1px 0 rgba(255, 255, 255, 1);
            border-radius: 2.5rem;
            position: relative;
            z-index: 10;
        }

        .premium-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.4) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 1.5rem;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
        }

        .premium-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(14, 165, 233, 0.12);
            border-color: rgba(14, 165, 233, 0.3);
        }

        .premium-card img {
            transition: transform 0.8s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .premium-card:hover img {
            transform: scale(1.08);
        }

        .text-gradient {
            background: linear-gradient(135deg, #0284c7, #3b82f6, #0ea5e9);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            display: inline-block;
            padding-top: 0.25em;
            padding-bottom: 0.15em;
            margin-top: -0.25em;
            margin-bottom: -0.15em;
            vertical-align: middle;
            animation: textShine 4s linear infinite;
        }

        .btn-shimmer {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            position: relative;
            overflow: hidden;
            border: none;
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
            transition: all 0.3s ease;
        }

        .btn-shimmer:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(14, 165, 233, 0.5);
        }

        .btn-shimmer::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        .blob {
            position: absolute;
            filter: blur(60px);
            z-index: 0;
            opacity: 0.6;
            animation: blobBounce 10s infinite alternate;
        }
        .blob-1 { background: #bae6fd; width: 300px; height: 300px; top: -50px; right: -50px; border-radius: 40% 60% 60% 40%; }
        .blob-2 { background: #fbcfe8; width: 250px; height: 250px; bottom: -50px; left: -20px; border-radius: 60% 40% 30% 70%; animation-delay: -5s; }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        @keyframes textShine {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        @keyframes blobBounce {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, 20px) scale(1.1); }
            100% { transform: translate(20px, -20px) scale(0.9); }
        }

        /* 6. تنسيق وتكبير الشعارات (Logo Scaling - Headers & Footers) */
        #navbar img[alt="Logo"], 
        nav img[alt="Logo"], 
        nav a.flex-shrink-0 img {
            height: 3.5rem !important; /* يعادل h-14 */
            width: auto !important;
        }

        #footer img[alt="Logo"], 
        footer img[alt="Logo"] {
            height: 4rem !important; /* تكبير اللوجو بأسفل الموقع ليكون واضحاً ومميزاً */
            width: auto !important;
        }

        /* 7. تنسيق صفحات الشروط والخصوصية (Policy Page Typography) */
        .policy-content {
            font-family: 'Tajawal', sans-serif;
            color: #334155; /* slate-700 */
        }
        .policy-content h1, .policy-content h2, .policy-content h3 {
            color: #0f172a; /* slate-900 */
            font-weight: 800;
            margin-top: 2rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        .policy-content h1 {
            font-size: 1.8rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }
        .policy-content h3 {
            font-size: 1.4rem;
            color: #0284c7; /* brand sky color */
        }
        .policy-content p {
            line-height: 1.8;
            margin-bottom: 1.2rem;
            font-size: 1rem;
        }
        .policy-content strong {
            color: #0f172a;
            font-weight: 700;
        }
        .policy-content ol, .policy-content ul {
            margin-bottom: 1.5rem;
            padding-right: 1.5rem;
            padding-left: 1.5rem;
        }
        .policy-content li {
            margin-bottom: 0.8rem;
            line-height: 1.7;
            position: relative;
            list-style-type: decimal;
        }
        html[dir="ltr"] .policy-content ol, html[dir="ltr"] .policy-content ul {
            padding-left: 1.5rem;
            padding-right: 0;
        }
    </style>
    <!-- Unified Lazy-Loaded Tracking Pixels: Moved to head -->


    {{-- ============================================================ --}}
    {{-- Schema Markup / JSON-LD – Rich Snippets                     --}}
    {{-- ============================================================ --}}

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
                  "url": "https://cleanstation.app.link/download",
                  "installUrl": "https://cleanstation.app.link/download",
                  "downloadUrl": "https://cleanstation.app.link/download",
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
                  "url": "https://cleanstation.app.link/download",
                  "installUrl": "https://cleanstation.app.link/download",
                  "downloadUrl": "https://cleanstation.app.link/download",
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

    <!-- Sticky Mobile CTA -->
    <div id="smart-mobile-cta" class="sticky-mobile-cta">
        <div class="cta-content">
            <div class="stars">
                ★ ★ ★ ★ ★ <span style="color: #64748b; font-size: 10px;">(12k+)</span>
            </div>
            <div class="sticky-mobile-cta-title">
                @if(LaravelLocalization::getCurrentLocale() === 'ar')
                    أسرع تطبيق غسيل بالرياض
                @else
                    Fastest Laundry App in Riyadh
                @endif
            </div>
        </div>
        <a href="https://cleanstation.app.link/download" class="sticky-mobile-cta-btn btn-glow-pulse">
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

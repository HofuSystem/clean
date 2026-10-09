@php
    $lang = $lang ?? (app()->getLocale() ?: 'ar');
    $isRtl = $isRtl ?? ($lang === 'ar');
    $whatsappPhone = preg_replace('/[^0-9]/', '', $settings['whatsapp'] ?? '966559098685') ?: '966559098685';
    $appStoreLink = 'https://cleanstation.app.link/?channel=gifts';
    $gPlayLink = function_exists('setting') ? (setting('g_play_app') ?: 'https://play.google.com/store/apps/details?id=com.googansolutions.cleanstation') : 'https://play.google.com/store/apps/details?id=com.googansolutions.cleanstation';
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isRtl ? 'توصيل ورد وهدايا في الرياض | كلين ستيشن' : 'Flower & Gift Delivery in Riyadh | Clean Station' }}</title>
    <meta name="description" content="{{ $isRtl ? 'باقات ورود وفازات فاخرة، تهديها لنفسك أو لمن تحب، مع اختيار موعد التوصيل من تطبيق كلين ستيشن في الرياض.' : 'Flower bouquets and luxury vases for yourself or someone special, with scheduled delivery from the Clean Station app in Riyadh.' }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('control/assets/img/favicon/favicon_white.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Landing Icons CSS -->
    <link rel="stylesheet" href="{{ asset('build/assets/landing-ByN9Vf1B.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/landing-icons-full.css') }}">

    <style>
        :root {
            --navy: #1F3364;
            --blue: #027BC0;
            --sky: #68C9E2;
            --mid: #4675B9;
            --sky-soft: #EAF6FB;
            --ink: #16222E;
            --muted: #5D6B78;
            --line: #E3E8EE;
            --bg: #FFFFFF;
            --bg-2: #F5F8FB;
            --green: #3BA55C;
            --wa: #25D366;
            --rose: #A33A5B;
            --rose-soft: #FDF0F4;
            --rose-border: #F3DCE4;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: {{ $isRtl ? '"IBM Plex Sans Arabic", system-ui, sans-serif' : '"Inter", system-ui, sans-serif' }};
            color: var(--ink);
            background: #fff;
            line-height: 1.7;
            overflow-x: hidden;
        }

        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }

        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 24px; }
        section { padding: 56px 0; }

        .kicker {
            display: inline-block;
            font-size: 13px;
            font-weight: 700;
            color: var(--rose);
            background: var(--rose-soft);
            padding: 4px 14px;
            border-radius: 999px;
            margin-bottom: 12px;
        }

        h1, h2, h3, h4, h5 { color: var(--navy); font-weight: 700; }
        h2 { font-size: 28px; line-height: 1.35; margin-bottom: 8px; text-align: center; }

        /* Store Badges */
        .stores { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-top: 24px; }
        .store { display: inline-flex; }
        .sb {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            height: 46px;
            padding: 0 16px;
            background: #000;
            color: #fff;
            border: 1px solid #444;
            border-radius: 10px;
            white-space: nowrap;
            transition: transform .2s, box-shadow .2s;
        }
        .sb:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.18); }
        .sb svg { width: 22px; height: 22px; flex: none; }
        .sb .t { display: flex; flex-direction: column; line-height: 1.15; text-align: start; }
        .sb .t small { font-size: 10px; opacity: .85; }
        .sb .t b { font-size: 15px; font-weight: 700; }

        /* Hero Section */
        .ghero {
            background: linear-gradient(160deg, #FDF2F6 0%, #fff 64%);
            padding: 130px 0 56px;
        }
        .ghero .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 40px;
            align-items: center;
        }
        .ghero h1 {
            font-size: 42px;
            line-height: 1.35;
            color: var(--navy);
            font-weight: 800;
            margin-bottom: 14px;
        }
        .ghero .lead {
            font-size: 17px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 520px;
        }
        .ghero .pic {
            border-radius: 26px;
            overflow: hidden;
            aspect-ratio: 1/1;
            background: var(--rose-soft);
            box-shadow: 0 16px 40px rgba(163,58,91,0.12);
            border: 1px solid var(--rose-border);
        }
        .ghero .pic img { width: 100%; height: 100%; object-fit: cover; }

        /* Collection Section */
        .cats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 28px;
        }
        .cat {
            border: 1px solid var(--rose-border);
            border-radius: 22px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            transition: transform .25s, box-shadow .25s;
        }
        .cat:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 32px rgba(163,58,91,0.1);
        }
        .cat img { width: 100%; aspect-ratio: 4/3; object-fit: cover; }
        .cat .in { padding: 22px 24px; }
        .cat .k {
            font-size: 12.5px;
            color: var(--rose);
            font-weight: 700;
            display: inline-block;
            margin-bottom: 6px;
        }
        .cat h3 { font-size: 20px; color: var(--navy); font-weight: 700; margin-bottom: 6px; }
        .cat p { font-size: 14px; color: var(--muted); margin: 0; line-height: 1.6; }
        .inapp {
            margin-top: 18px;
            font-size: 13.5px;
            color: var(--muted);
            text-align: center;
            font-weight: 500;
        }

        /* Occasions Row */
        .occ-row {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-top: 26px;
            padding-top: 24px;
            border-top: 1px solid var(--rose-border);
            text-align: center;
        }
        .occ-l {
            font-size: 15px;
            font-weight: 700;
            color: var(--rose);
        }
        .occ {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            max-width: 820px;
        }
        .occ span {
            font-size: 13px;
            background: var(--rose-soft);
            color: var(--ink);
            padding: 5px 14px;
            border-radius: 999px;
            white-space: nowrap;
            border: 1px solid rgba(163,58,91,0.12);
            font-weight: 500;
        }

        /* Features Section */
        .features-section {
            background: #FDF6F8;
            border-top: 1px solid var(--rose-border);
            border-bottom: 1px solid var(--rose-border);
        }
        .gfeat {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 28px;
        }
        .gfeat .why {
            border: 1px solid var(--rose-border);
            border-radius: 20px;
            padding: 24px 22px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: transform .2s, box-shadow .2s;
        }
        .gfeat .why:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(163,58,91,0.07);
        }
        .gfeat .why .ic {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: var(--rose-soft);
            display: grid;
            place-items: center;
            margin-bottom: 16px;
            color: var(--rose);
        }
        .gfeat .why h4 { color: var(--navy); font-size: 16.5px; font-weight: 700; margin-bottom: 6px; }
        .gfeat .why p { color: var(--muted); font-size: 13.5px; margin: 0; line-height: 1.6; }
        .chips.slots { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
        .chips.slots span {
            font-size: 11.5px;
            background: var(--rose-soft);
            color: var(--rose);
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            border: 1px solid rgba(163,58,91,0.15);
        }

        /* Steps Section */
        .gsteps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 26px;
        }
        .gsteps .j {
            background: #fff;
            border-radius: 20px;
            padding: 24px 20px;
            border: 1px solid var(--line);
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .gsteps .j h4 { color: var(--navy); font-size: 16px; font-weight: 700; margin-bottom: 6px; }
        .gsteps .j p { color: var(--muted); font-size: 13.5px; margin: 0; line-height: 1.6; }

        /* FAQ Section (matches land.blade.php style without +/-) */
        .faq { max-width: 760px; margin: 24px auto 0; }
        .faq details {
            border: 1px solid var(--line);
            border-radius: 14px;
            margin-bottom: 8px;
            background: #fff;
            padding: 12px 18px;
        }
        .faq summary {
            font-weight: 600;
            font-size: 15px;
            color: var(--navy);
            cursor: pointer;
            list-style: none;
        }
        .faq summary::-webkit-details-marker { display: none; }
        .faq p { margin-top: 8px; color: var(--muted); font-size: 14px; line-height: 1.6; }

        /* Final CTA */
        .final {
            background: var(--navy);
            color: #fff;
            border-radius: 24px;
            padding: 44px 24px;
            text-align: center;
            margin-top: 40px;
        }
        .final h2 { color: #fff; font-size: 30px; margin-bottom: 8px; }
        .final .pts { font-size: 15px; opacity: .9; margin-bottom: 20px; }
        .final .contact-line { margin-top: 20px; font-size: 14px; opacity: .85; }
        .final .contact-line a { color: #fff; font-weight: 700; text-decoration: underline; }

        /* Stores */
        .stores { display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap; align-items: center; }
        .store-link { display: inline-block; transition: transform 0.2s ease; border-radius: 8px; }
        .store-link:hover { transform: translateY(-2px); }
        .store-badge-img { height: 44px; width: auto; border-radius: 8px; display: block; }

        /* Sticky Mobile CTA (Original Home Page Style) */
        .sticky-mobile-cta {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-top: 1px solid #e2e8f0;
            padding: 10px 18px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
            justify-content: space-between;
            align-items: center;
            z-index: 9999;
        }
        .sticky-mobile-cta .cta-content {
            display: flex;
            flex-direction: column;
            text-align: start;
        }
        .sticky-mobile-cta .stars {
            color: #fbbf24;
            font-size: 11px;
            letter-spacing: 2px;
            margin-bottom: 2px;
            line-height: 1;
        }
        .sticky-mobile-cta-title {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.3;
        }
        .sticky-mobile-cta-btn {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: #ffffff !important;
            padding: 9px 20px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13.5px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s ease;
        }
        .sticky-mobile-cta-btn:active {
            transform: scale(0.97);
        }

        /* Navbar Logo position (left) and height */
        #navbar .h-20 { direction: ltr !important; }
        #navbar #nav-pill-menu, #navbar .hidden.xl\:flex { direction: {{ $isRtl ? 'rtl' : 'ltr' }} !important; }
        #navbar .h-10, #navbar img.h-10 {
            height: 58px !important;
            max-height: 58px !important;
            width: auto !important;
            object-fit: contain !important;
        }

        /* Footer styling (Navy background + White large logo) */
        #footer {
            background-color: #1F3364 !important;
            color: #fff !important;
            padding-top: 60px !important;
            padding-bottom: 36px !important;
        }
        #footer .max-w-7xl { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        #footer .grid {
            display: grid !important;
            grid-template-columns: 1.3fr 1fr 1fr 1.2fr !important;
            gap: 36px !important;
            margin-bottom: 48px !important;
            text-align: start !important;
        }
        #footer h3 {
            font-size: 14px !important;
            font-weight: 800 !important;
            color: #fff !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            margin-bottom: 18px !important;
        }
        #footer ul { list-style: none !important; padding: 0 !important; margin: 0 !important; }
        #footer li { margin: 9px 0 !important; font-size: 13.5px !important; }
        #footer a { color: rgba(255,255,255,0.75) !important; transition: color 0.2s !important; }
        #footer a:hover { color: #fff !important; }
        #footer p { color: rgba(255,255,255,0.75) !important; font-size: 13.5px !important; line-height: 1.65 !important; }
        #footer svg {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            flex-shrink: 0 !important;
            display: inline-block !important;
            fill: currentColor !important;
        }
        #footer img.h-10, #footer .h-10, #footer div>img[alt="Logo"], #footer img[width="1503"], #footer .footer-logo {
            height: 64px !important;
            width: auto !important;
            max-height: 64px !important;
            max-width: 240px !important;
            object-fit: contain !important;
            filter: brightness(0) invert(1) !important;
            -webkit-filter: brightness(0) invert(1) !important;
            opacity: 1 !important;
            display: block !important;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .ghero .hero-grid { grid-template-columns: 1fr; gap: 28px; text-align: center; }
            .ghero .lead { margin: 12px auto 0; }
            .ghero .stores { justify-content: center; }
            .ghero .pic { max-width: 440px; margin: 0 auto; }
            .gfeat { grid-template-columns: 1fr 1fr; }
            .gsteps { grid-template-columns: 1fr 1fr; }
            #footer .grid { grid-template-columns: 1fr 1fr !important; gap: 32px !important; }
        }

        @media (max-width: 640px) {
            section { padding: 36px 0; }
            .ghero { padding: 96px 0 32px; }
            .ghero h1 { font-size: 28px; line-height: 1.3; }
            .ghero .lead { font-size: 14.5px; }
            .ghero .pic { max-width: 320px; margin: 0 auto; border-radius: 20px; }

            /* Collection: 2 cards per row on mobile (exact to cleanstation-site-3.html) */
            .cats {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
            }
            .cat { border-radius: 16px !important; }
            .cat .in { padding: 12px 10px !important; }
            .cat .k { font-size: 11px !important; }
            .cat h3 { font-size: 14px !important; line-height: 1.3 !important; }
            .cat p { font-size: 11.5px !important; line-height: 1.5 !important; margin-top: 2px !important; }
            .inapp { font-size: 12px !important; margin-top: 12px !important; }

            .occ-row { margin-top: 14px !important; padding-top: 14px !important; gap: 8px !important; }
            .occ-l { font-size: 13.5px !important; }
            .occ { gap: 5px !important; }
            .occ span { font-size: 11.5px !important; padding: 3px 10px !important; }

            /* Gifting Features: 2 cards per row on mobile (exact to cleanstation-site-3.html) */
            .gfeat {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
            }
            .gfeat .why { padding: 13px 12px !important; border-radius: 14px !important; }
            .gfeat .why .ic { width: 34px !important; height: 34px !important; border-radius: 10px !important; margin-bottom: 8px !important; }
            .gfeat .why .ic svg { width: 22px !important; height: 22px !important; }
            .gfeat .why h4 { font-size: 13.5px !important; line-height: 1.35 !important; }
            .gfeat .why p { font-size: 11.5px !important; line-height: 1.45 !important; margin-top: 3px !important; }
            .chips.slots { gap: 4px !important; margin-top: 6px !important; }
            .chips.slots span { font-size: 10px !important; padding: 2px 7px !important; }

            /* How to order steps: 2 cards per row on mobile (exact to cleanstation-site-3.html) */
            .gsteps {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
            }
            .gsteps .j { padding: 13px 12px !important; border-radius: 14px !important; }
            .gsteps .j h4 { font-size: 13.5px !important; line-height: 1.35 !important; }
            .gsteps .j p { font-size: 11.5px !important; line-height: 1.45 !important; margin-top: 3px !important; }

            /* FAQ: matches land on mobile without +/- */
            .faq details { padding: 12px 16px !important; margin-bottom: 8px !important; border-radius: 12px !important; }
            .faq summary { font-size: 14px !important; }
            .faq p { font-size: 13px !important; line-height: 1.55 !important; margin-top: 6px !important; }

            /* Final Banner */
            .final { padding: 32px 18px !important; border-radius: 18px !important; margin-top: 28px !important; }
            .final h2 { font-size: 20px !important; }
            .final .pts { font-size: 13.5px !important; }
            .final .contact-line { font-size: 12.5px !important; }

            .store-badge-img { height: 40px; }
            .sticky-mobile-cta { display: flex !important; }
            body { padding-bottom: 72px !important; }
            #footer .grid { grid-template-columns: 1fr !important; text-align: center !important; gap: 28px !important; }
            #footer .space-y-6, #footer .flex-col { align-items: center !important; }
            #footer .flex.flex-nowrap { justify-content: center !important; }
            #footer .border-t { justify-content: center !important; text-align: center !important; }
        }
    </style>
</head>
<body>

    <!-- Main Navigation (Preserved exactly as requested) -->
    @include('layouts.partials.navbar')

    <!-- Flowers & Gifts Hero Section -->
    <section class="ghero">
        <div class="wrap">
            <div class="hero-grid">
                <div>
                    <span class="kicker">{{ $isRtl ? 'جديد · الورود والهدايا' : 'New · Flowers & Gifts' }}</span>
                    <h1>{!! $isRtl ? 'نهتم بما ترتديه<br>ونعتني بما تهديه' : 'We care for what you wear<br>and what you give' !!}</h1>
                    <p class="lead">
                        {{ $isRtl ? 'باقات ورود وفازات فاخرة، تهديها لنفسك أو لمن تحب، وتختار موعد التوصيل من تطبيق كلين ستيشن' : 'Flower bouquets and luxury vases for yourself or someone special, with the delivery time you choose in the Clean Station app' }}
                    </p>
                    <div class="stores">
                        <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="store-link" aria-label="App Store">
                            <img src="{{ asset($isRtl ? 'assets/store-badges/app-store-ar.svg' : 'assets/store-badges/app-store.svg') }}" alt="App Store" class="store-badge-img">
                        </a>
                        <a href="{{ $gPlayLink }}" target="_blank" rel="noopener" class="store-link" aria-label="Google Play">
                            <img src="{{ asset($isRtl ? 'assets/store-badges/google-play-ar.png' : 'assets/store-badges/google-play.svg') }}" alt="Google Play" class="store-badge-img">
                        </a>
                    </div>
                </div>
                <div class="pic">
                    <img src="https://images.unsplash.com/photo-1591886960571-74d43a9d4166?auto=format&fit=crop&q=75&w=1000" alt="{{ $isRtl ? 'باقات الورود والهدايا' : 'Flowers and Gifts' }}">
                </div>
            </div>
        </div>
    </section>

    <!-- Collection Section (تشكيلتنا) -->
    <section>
        <div class="wrap">
            <h2>{{ $isRtl ? 'تشكيلتنا' : 'Our collection' }}</h2>
            <div class="cats">
                <div class="cat">
                    <img src="https://images.unsplash.com/photo-1591886960571-74d43a9d4166?auto=format&fit=crop&q=75&w=800" alt="{{ $isRtl ? 'باقات الورود' : 'Flower bouquets' }}">
                    <div class="in">
                        <span class="k">{{ $isRtl ? 'طبيعية وساحرة' : 'Natural and charming' }}</span>
                        <h3>{{ $isRtl ? 'باقات الورود' : 'Flower bouquets' }}</h3>
                        <p>{{ $isRtl ? 'تنسيقات من أجود الورود، مصممة بحب لتنقل مشاعرك' : 'Arrangements of the finest roses, made with love to share your feelings' }}</p>
                    </div>
                </div>
                <div class="cat">
                    <img src="https://images.unsplash.com/photo-1561181286-d3fee7d55364?auto=format&fit=crop&q=75&w=800" alt="{{ $isRtl ? 'فازات ورد فاخرة' : 'Luxury flower vases' }}">
                    <div class="in">
                        <span class="k">{{ $isRtl ? 'أناقة تدوم' : 'Lasting elegance' }}</span>
                        <h3>{{ $isRtl ? 'فازات ورد فاخرة' : 'Luxury flower vases' }}</h3>
                        <p>{{ $isRtl ? 'فازات مصممة بعناية تجمع بين سحر الورود وأناقة التقديم' : 'Carefully designed vases that pair beautiful roses with elegant presentation' }}</p>
                    </div>
                </div>
            </div>
            <p class="inapp">{{ $isRtl ? 'التشكيلة كاملة وأسعارها في قسم الهدايا داخل التطبيق' : 'The full collection and prices are in the Gifts section of the app' }}</p>
            <div class="occ-row">
                <span class="occ-l">{{ $isRtl ? 'لكل مناسبة' : 'For every occasion' }}</span>
                <div class="occ">
                    @if($isRtl)
                        <span>زواج</span><span>خطوبة</span><span>حب</span><span>تخرج ونجاح</span><span>ترقية</span><span>مولود جديد</span><span>يوم ميلاد</span><span>زيارة مريض</span><span>تهنئة بمنزل جديد</span><span>شكر وتقدير</span><span>اعتذار</span><span>عيد</span><span>اليوم الوطني</span><span>بدون مناسبة</span>
                    @else
                        <span>Wedding</span><span>Engagement</span><span>Love</span><span>Graduation</span><span>Promotion</span><span>New baby</span><span>Birthday</span><span>Get well soon</span><span>New home</span><span>Thank you</span><span>Sorry</span><span>Eid</span><span>National Day</span><span>Just because</span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Gifting Features (أهدِ بالطريقة اللي تناسبك) -->
    <section class="features-section">
        <div class="wrap">
            <div style="text-align:center">
                <span class="kicker">{{ $isRtl ? 'مميزات الإهداء' : 'Gifting features' }}</span>
                <h2>{{ $isRtl ? 'أهدِ بالطريقة اللي تناسبك' : 'Gift it your way' }}</h2>
            </div>
            <div class="gfeat">
                <!-- 1. For you or anyone -->
                <div class="why">
                    <div class="ic">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"><path d="M4 11v4c0 3.3 0 4.95 1.025 5.975S7.7 22 11 22h2c3.3 0 4.95 0 5.975-1.025S20 18.3 20 15v-4" stroke-linecap="round"/><path d="M3 9c0-.748 0-1.122.201-1.4a1.4 1.4 0 0 1 .549-.44C4.098 7 4.565 7 5.5 7h13c.935 0 1.402 0 1.75.16c.228.106.417.258.549.44C21 7.878 21 8.252 21 9s0 1.121-.201 1.4a1.4 1.4 0 0 1-.549.44c-.348.16-.815.16-1.75.16h-13c-.935 0-1.402 0-1.75-.16a1.4 1.4 0 0 1-.549-.44C3 10.121 3 9.748 3 9Zm3-5.214C6 2.799 6.8 2 7.786 2h.357A3.857 3.857 0 0 1 12 5.857V7H9.214A3.214 3.214 0 0 1 6 3.786Zm12 0C18 2.799 17.2 2 16.214 2h-.357A3.857 3.857 0 0 0 12 5.857V7h2.786A3.214 3.214 0 0 0 18 3.786Z"/><path d="M12 11v11" stroke-linecap="round"/></svg>
                    </div>
                    <h4>{{ $isRtl ? 'لك أو لمن تحب' : 'For you or anyone' }}</h4>
                    <p>{{ $isRtl ? 'اطلبها لنفسك، أو أرسلها لأي شخص تبيه' : 'Order for yourself, or send it to anyone you like' }}</p>
                </div>

                <!-- 2. Send anonymously -->
                <div class="why">
                    <div class="ic">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M19.439 15.439a19.5 19.5 0 0 0 2.105-2.484c.304-.426.456-.64.456-.955c0-.316-.152-.529-.456-.955C20.178 9.129 16.689 5 12 5c-.908 0-1.77.155-2.582.418m-2.67 1.33c-2.017 1.36-3.506 3.195-4.292 4.297c-.304.426-.456.64-.456.955c0 .316.152.529.456.955C3.822 14.871 7.311 19 12 19c1.99 0 3.765-.744 5.253-1.747" stroke-linejoin="round"/><path d="M9.858 10A2.929 2.929 0 1 0 14 14.142"/><path d="m3 3l18 18" stroke-linejoin="round"/></svg>
                    </div>
                    <h4>{{ $isRtl ? 'إهداء بدون اسم' : 'Send anonymously' }}</h4>
                    <p>{{ $isRtl ? 'اختر عدم إظهار اسمك، وتبقى الهدية مفاجأة' : 'Choose to hide your name and keep it a surprise' }}</p>
                </div>

                <!-- 3. Recipient location -->
                <div class="why">
                    <div class="ic">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M13.618 21.367A2.37 2.37 0 0 1 12 22a2.37 2.37 0 0 1-1.617-.633C6.412 17.626 1.09 13.447 3.685 7.38C5.09 4.1 8.458 2 12.001 2s6.912 2.1 8.315 5.38c2.592 6.06-2.717 10.259-6.698 13.987Z"/><path d="M15.5 11a3.5 3.5 0 1 1-7 0a3.5 3.5 0 0 1 7 0Z"/></svg>
                    </div>
                    <h4>{{ $isRtl ? 'موقع المستلم' : 'Recipient location' }}</h4>
                    <p>{{ $isRtl ? 'حدده بنفسك، أو أضف رقمه ونتواصل معه لأخذ الموقع' : 'Set it yourself, or add their number and we\'ll contact them for it' }}</p>
                </div>

                <!-- 4. Gift card -->
                <div class="why">
                    <div class="ic">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 18.985V20.5h1.514c1.227 0 1.84 0 2.391-.228c.551-.229.985-.662 1.852-1.53l9.864-9.863c.883-.883 1.324-1.324 1.373-1.866q.012-.135 0-.269c-.05-.541-.49-.983-1.373-1.865c-.883-.883-1.324-1.324-1.865-1.373a1.5 1.5 0 0 0-.27 0c-.541.049-.982.49-1.865 1.373l-9.864 9.864c-.867.867-1.3 1.3-1.529 1.852c-.228.55-.228 1.164-.228 2.39M13.5 6.5l4 4"/></svg>
                    </div>
                    <h4>{{ $isRtl ? 'بطاقة إهداء' : 'Gift card' }}</h4>
                    <p>{{ $isRtl ? 'اكتب رسالتك، ونطبعها على بطاقة مع الهدية' : 'Write your message and we print it on a card with the gift' }}</p>
                </div>

                <!-- 5. Delivery date and time -->
                <div class="why">
                    <div class="ic">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 2v4M8 2v4m5-2h-2C7.229 4 5.343 4 4.172 5.172S3 8.229 3 12v2c0 3.771 0 5.657 1.172 6.828S7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172S21 17.771 21 14v-2c0-3.771 0-5.657-1.172-6.828S16.771 4 13 4M3 10h18"/><path d="M12.126 14H12m.125 4H12m-4.376-4H7.5m.125 4H7.5m9.125-4H16.5m-4.25 0a.25.25 0 1 1-.5 0a.25.25 0 0 1 .5 0m0 4a.25.25 0 1 1-.5 0a.25.25 0 0 1 .5 0m-4.5-4a.25.25 0 1 1-.5 0a.25.25 0 0 1 .5 0m0 4a.25.25 0 1 1-.5 0a.25.25 0 0 1 .5 0m9-4a.25.25 0 1 1-.5 0a.25.25 0 0 1 .5 0"/></svg>
                    </div>
                    <h4>{{ $isRtl ? 'تاريخ ووقت التوصيل' : 'Delivery date and time' }}</h4>
                    <p>{{ $isRtl ? 'تختار اليوم وفترة ساعتين تناسبك' : 'Choose the day and a two-hour slot that suits you' }}</p>
                    <div class="chips slots">
                        @if($isRtl)
                            <span>12 – 2 م</span><span>2 – 4 م</span><span>4 – 6 م</span><span>6 – 8 م</span><span>8 – 10 م</span><span>10 م – 12 ص</span>
                        @else
                            <span>12–2 pm</span><span>2–4 pm</span><span>4–6 pm</span><span>6–8 pm</span><span>8–10 pm</span><span>10 pm–12 am</span>
                        @endif
                    </div>
                </div>

                <!-- 6. Order in the app only -->
                <div class="why">
                    <div class="ic">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 2h-3c-2.357 0-3.536 0-4.268.732S5.5 4.643 5.5 7v10c0 2.357 0 3.535.732 4.268S8.143 22 10.5 22h3c2.357 0 3.535 0 4.268-.732c.732-.733.732-1.911.732-4.268V7c0-2.357 0-3.536-.732-4.268C17.035 2 15.857 2 13.5 2"/><path d="M12.125 19H12m.25 0a.25.25 0 1 1-.5 0a.25.25 0 0 1 .5 0"/></svg>
                    </div>
                    <h4>{{ $isRtl ? 'الطلب من التطبيق فقط' : 'Order in the app only' }}</h4>
                    <p>{{ $isRtl ? 'كل الباقات بأسمائها وأوصافها وأسعارها في التطبيق' : 'Every bouquet with its name, description and price is in the app' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How To Order (هديتك بأربع خطوات) -->
    <section>
        <div class="wrap">
            <div style="text-align:center">
                <span class="kicker">{{ $isRtl ? 'كيف تطلب' : 'How to order' }}</span>
                <h2>{{ $isRtl ? 'هديتك بأربع خطوات' : 'Your gift in four steps' }}</h2>
            </div>
            <div class="gsteps">
                <div class="j">
                    <h4>{{ $isRtl ? '1 · اختر الباقة' : '1 · Pick a bouquet' }}</h4>
                    <p>{{ $isRtl ? 'من قسم الهدايا في التطبيق' : 'From the Gifts section in the app' }}</p>
                </div>
                <div class="j">
                    <h4>{{ $isRtl ? '2 · حدد المستلم' : '2 · Set the recipient' }}</h4>
                    <p>{{ $isRtl ? 'لك أو لغيرك، باسمك أو بدون اسم' : 'You or someone else, named or anonymous' }}</p>
                </div>
                <div class="j">
                    <h4>{{ $isRtl ? '3 · اكتب البطاقة واختر الموعد' : '3 · Card and timing' }}</h4>
                    <p>{{ $isRtl ? 'رسالتك على البطاقة، واليوم والفترة' : 'Your message on the card, plus day and slot' }}</p>
                </div>
                <div class="j">
                    <h4>{{ $isRtl ? '4 · نوصلها بعناية' : '4 · Delivered with care' }}</h4>
                    <p>{{ $isRtl ? 'تغليف أنيق في الوقت اللي اخترته' : 'Elegant wrapping at the time you chose' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ (قبل ما تطلب) -->
    <section style="padding-top:0">
        <div class="wrap">
            <h2>{{ $isRtl ? 'قبل ما تطلب' : 'Before you order' }}</h2>
            <div class="faq">
                <details open>
                    <summary>{{ $isRtl ? 'كيف أطلب هدية؟' : 'How do I order a gift?' }}</summary>
                    <p>{{ $isRtl ? 'من تطبيق كلين ستيشن فقط، من قسم الهدايا' : 'In the Clean Station app only, from the Gifts section' }}</p>
                </details>
                <details>
                    <summary>{{ $isRtl ? 'أقدر أرسل الهدية بدون ما يعرف المستلم مني؟' : 'Can I send a gift anonymously?' }}</summary>
                    <p>{{ $isRtl ? 'نعم، تقدر تختار عدم إظهار اسمك عند الطلب' : 'Yes, you can choose to hide your name when you order' }}</p>
                </details>
                <details>
                    <summary>{{ $isRtl ? 'ما أعرف موقع الشخص اللي بهديه، وش أسوي؟' : 'I don\'t know the recipient\'s location, what do I do?' }}</summary>
                    <p>{{ $isRtl ? 'أضف رقم جواله في الطلب، ونتواصل معه ونأخذ الموقع منه' : 'Add their mobile number to the order, and we\'ll contact them for the location' }}</p>
                </details>
                <details>
                    <summary>{{ $isRtl ? 'متى توصل الهدية؟' : 'When will the gift arrive?' }}</summary>
                    <p>{{ $isRtl ? 'في اليوم والفترة اللي تختارها في التطبيق، والفترات كل ساعتين من 12 الظهر إلى 12 الليل' : 'On the day and slot you choose in the app, with two-hour slots from 12 noon to midnight' }}</p>
                </details>
                <details>
                    <summary>{{ $isRtl ? 'أقدر أطلب عن طريق الواتساب؟' : 'Can I order on WhatsApp?' }}</summary>
                    <p>{{ $isRtl ? 'لا، الطلب من التطبيق فقط، والواتساب للتواصل مع خدمة العملاء وللاستفسارات' : 'No, orders are placed in the app only, and WhatsApp is for customer service and enquiries' }}</p>
                </details>
            </div>

            <!-- Final CTA Banner -->
            <div class="final">
                <h2>{{ $isRtl ? 'فاجئ اللي تحب بهدية أنيقة' : 'Surprise someone with an elegant gift' }}</h2>
                <div class="pts">
                    <span>{{ $isRtl ? 'اطلبها الحين من قسم الهدايا في التطبيق' : 'Order now from the Gifts section in the app' }}</span>
                </div>
                <div class="stores" style="justify-content:center">
                    <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="store-link" aria-label="App Store">
                        <img src="{{ asset($isRtl ? 'assets/store-badges/app-store-ar.svg' : 'assets/store-badges/app-store.svg') }}" alt="App Store" class="store-badge-img">
                    </a>
                    <a href="{{ $gPlayLink }}" target="_blank" rel="noopener" class="store-link" aria-label="Google Play">
                        <img src="{{ asset($isRtl ? 'assets/store-badges/google-play-ar.png' : 'assets/store-badges/google-play.svg') }}" alt="Google Play" class="store-badge-img">
                    </a>
                </div>
                <p class="contact-line">
                    {{ $isRtl ? 'للاستفسارات والملاحظات:' : 'Questions or feedback?' }}
                    <a href="https://wa.me/{{ $whatsappPhone }}?text={{ urlencode($isRtl ? 'استفسار عن خدمة الورود والهدايا' : 'Enquiry about Flowers & Gifts service') }}" target="_blank" rel="noopener">
                        {{ $isRtl ? 'تواصل مع خدمة العملاء عبر واتساب' : 'Contact customer service on WhatsApp' }}
                    </a>
                </p>
            </div>
        </div>
    </section>

    <!-- Footer (Preserved exactly as requested) -->
    @include('layouts.partials.footer')

    <!-- Sticky Mobile CTA (Original Home Page Style) -->
    <div id="smart-mobile-cta" class="sticky-mobile-cta">
        <div class="cta-content">
            <div class="stars">★ ★ ★ ★ ★</div>
            <div class="sticky-mobile-cta-title">
                {{ $isRtl ? 'أسرع تطبيق غسيل بالرياض' : 'Fastest Laundry App in Riyadh' }}
            </div>
        </div>
        <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="sticky-mobile-cta-btn">
            {{ $isRtl ? 'حمّل التطبيق' : 'Download App' }}
        </a>
    </div>

</body>
</html>

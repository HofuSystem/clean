@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
    use Illuminate\Support\Str;

    $lang = LaravelLocalization::getCurrentLocale() ?: app()->getLocale() ?: 'ar';
    $isRtl = ($lang === 'ar');
    $dir = LaravelLocalization::getCurrentLocaleDirection() ?: ($isRtl ? 'rtl' : 'ltr');
    $otherLocale = $isRtl ? 'en' : 'ar';
    $currRoute = \Illuminate\Support\Facades\Route::currentRouteName() ?: 'home';
    $otherLocaleUrl = LaravelLocalization::getLocalizedURL($otherLocale, route($currRoute, [], false), [], true);

    // Fallbacks if accessed directly without controller
    if (!isset($heroImageUrl)) {
        $homePage = \Core\Pages\Models\Page::with(['translations', 'sections.translations'])
            ->where('slug', 'home')
            ->where('is_active', true)
            ->first();
        $heroSec = $homePage ? $homePage->sections->where('template', 'hero')->first() : null;
        $heroImageUrl = $heroSec ? $heroSec->image_url : null;
    }

    if (!isset($bags)) {
        $bagCat = \Core\Categories\Models\Category::with(['products' => function ($q) {
            $q->where('status', 'active')->where('is_package', 1);
        }, 'products.translations'])->where('slug', 'economic-bags')->first();
        $bags = $bagCat ? $bagCat->products->sortBy('price')->values() : collect();
    }

    if (!isset($extraPrices)) {
        $extraPrices = [
            22 => (int) (\Core\Products\Models\Product::where('id', 310)->value('price') ?: 5),
            23 => (int) (\Core\Products\Models\Product::where('id', 323)->value('price') ?: 4),
            221 => (int) (\Core\Products\Models\Product::where('id', 329)->value('price') ?: 4),
            318 => (int) (\Core\Products\Models\Product::where('id', 409)->value('price') ?: 19),
        ];
    }

    if (!isset($mostOrderedBagId) && isset($bags) && $bags->isNotEmpty()) {
        $mostOrderedBagId = \Illuminate\Support\Facades\DB::table('order_items')
            ->whereIn('product_id', $bags->pluck('id'))
            ->groupBy('product_id')
            ->orderByDesc(\Illuminate\Support\Facades\DB::raw('SUM(quantity)'))
            ->value('product_id');
    }

    if (!isset($sampleProducts)) {
        $sampleProducts = [];
        $rawProds = \Core\Products\Models\Product::with(['translations', 'subCategory.translations'])
            ->where('status', 'active')
            ->where('is_package', 0)
            ->whereIn('type', ['clothes'])
            ->get();
        $grouped = [];
        foreach ($rawProds as $p) {
            $arName = trim($p->translate('ar') ? $p->translate('ar')->name : $p->name);
            $enName = trim($p->translate('en') ? $p->translate('en')->name : $arName);
            $normKey = preg_replace('/[إأآا]/u', 'ا', mb_strtolower($arName));
            if (!isset($grouped[$normKey])) {
                $grouped[$normKey] = ['name_ar' => $arName, 'name_en' => $enName, 'wash_iron' => null, 'iron_only' => null, 'dry_clean' => null, 'service_count' => 0];
            }
            $subName = $p->subCategory ? ($p->subCategory->translate('ar')->name ?? $p->subCategory->name) : '';
            if (str_contains($subName, 'كوي') && str_contains($subName, 'غسيل')) {
                $grouped[$normKey]['wash_iron'] = $p->price;
                $grouped[$normKey]['service_count']++;
            } elseif (str_contains($subName, 'كوي')) {
                $grouped[$normKey]['iron_only'] = $p->price;
                $grouped[$normKey]['service_count']++;
            } elseif (str_contains($subName, 'جاف') || str_contains($subName, 'دراي')) {
                $grouped[$normKey]['dry_clean'] = $p->price;
                $grouped[$normKey]['service_count']++;
            }
        }
        $validItems = array_values(array_filter($grouped, fn($item) => $item['wash_iron'] !== null || $item['iron_only'] !== null || $item['dry_clean'] !== null));
        shuffle($validItems);
        usort($validItems, fn($a, $b) => $b['service_count'] <=> $a['service_count']);
        $sampleProducts = array_slice($validItems, 0, 5);
    }

    $freeDeliveryMin = $freeDeliveryMin ?? (int) (\Core\Settings\Models\Setting::where('key', 'free_delivery')->value('value') ?: 100);
    $logoVal = config('app.logo');
    $logoSrc = $logoVal ? (Str::startsWith($logoVal, 'http') ? $logoVal : asset('storage/' . $logoVal)) : asset('assets/logo-main.png');
    $whatsappPhone = setting('whatsapp') ?: '966559098685';
    $appStoreLink = setting('app_store_app') ?: 'https://cleanstation.app.link/?channel=navbar';
    $gPlayLink = setting('g_play_app') ?: 'https://play.google.com/store/apps/details?id=com.googansolutions.cleanstation';

    // Social Media Links
    $twitter = setting('twitter');
    $instagram = setting('instagram');
    $tiktok = setting('tiktok');
    $snapchat = setting('snapchat');
    $facebook = setting('facebook');
    $youtube = setting('youtube');
    $email = setting('email') ?: 'support@cleanstation.app';
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $isRtl ? 'كلين ستيشن — غسيلك... أذكى وأنظف!' : 'Clean Station — Your laundry, smarter and cleaner' }}</title>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@vite(['resources/css/landing.css'])
<style>
:root{
  --navy:#1F3364; --blue:#027BC0; --sky:#68C9E2; --mid:#4675B9; --sky-soft:#EAF6FB;
  --ink:#16222E; --muted:#5D6B78; --line:#E3E8EE; --bg:#FFFFFF; --bg-2:#F5F8FB;
  --green:#3BA55C; --wa:#25D366;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:"IBM Plex Sans Arabic",system-ui,-apple-system,sans-serif;color:var(--ink);background:#FFFFFF;line-height:1.7}
a{color:inherit;text-decoration:none}
img{max-width:100%;display:block}

/* Original Navbar Integration: Logo permanently on the left & larger */
#navbar .h-20{direction:ltr !important}
#navbar .hidden.xl\:flex, #navbar #nav-pill-menu{direction:{{ $isRtl ? 'rtl' : 'ltr' }} !important}
#navbar img.h-10,#navbar .h-10,#navbar a[href*="home"] img{height:58px !important;max-height:58px !important;width:auto !important;object-fit:contain !important;transition:all 0.2s}
@media (max-width:768px){
  #navbar, #navbar .h-20{height:60px !important}
  #navbar img.h-10,#navbar .h-10,#navbar a[href*="home"] img{height:38px !important;max-height:38px !important}
}

.btn{display:inline-flex;align-items:center;gap:8px;font-weight:600;font-size:14px;padding:10px 18px;border-radius:10px;border:1px solid transparent;cursor:pointer;transition:all 0.2s}
.btn-p{background:var(--navy);color:#fff}
.btn-p:hover{background:var(--mid)}
.btn-wa{background:var(--wa);color:#fff}
.btn-o{border-color:var(--line);color:var(--navy);background:#fff}
.btn-o:hover{border-color:var(--blue);color:var(--blue)}
.btn-lang{font-size:13px;padding:6px 14px;border:1px solid var(--line);border-radius:999px;color:var(--navy);background:var(--bg-2);font-weight:600}
.btn-lang:hover{border-color:var(--blue);color:var(--blue);background:#fff}

/* Page Containers */
.wrap{max-width:1100px;margin:0 auto;padding:0 24px}
section{padding:56px 0}
.kicker{display:inline-block;font-size:13px;font-weight:600;color:var(--blue);background:var(--sky-soft);padding:3px 12px;border-radius:999px;margin-bottom:8px}
h1{font-size:42px;line-height:1.3;color:var(--navy);font-weight:700}
h2{font-size:28px;line-height:1.4;color:var(--navy);font-weight:700}
.sub{color:var(--muted);margin-top:6px;font-size:15px}

/* Single-line text wrap helper */
.nw{white-space:nowrap}
p,q,li,.desc,.fit{text-wrap:balance}

/* Platform Badge (Kicker) */
.sa-wrap{margin-bottom:12px}
.kicker.sa{display:inline-flex;align-items:center;gap:10px;margin:0;background:rgba(255,255,255,.85);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);color:var(--muted);border:1px solid #DCE6F0;padding:6px 16px 6px 14px;font-size:14px;font-weight:500;border-radius:999px;box-shadow:0 2px 10px rgba(31,51,100,.06);white-space:nowrap}
.kicker.sa i{width:9px;height:9px;border-radius:50%;background:#0B7A3E;box-shadow:0 0 0 3px rgba(11,122,62,.15);flex:none}
.kicker.sa b{color:var(--navy);font-weight:700}
.kicker.sa em{width:1px;height:14px;background:#C9D5E2;flex:none}

/* Hero Section */
.hero{background:linear-gradient(160deg,#EEF7FC 0%,#fff 60%);padding:100px 0 28px}
.hero-grid{display:grid;grid-template-columns:1fr auto;gap:28px 48px;align-items:center}
.hero h1{font-size:40px;line-height:1.25;color:var(--navy);font-weight:700;margin:0 0 8px}
.hero .lead{font-size:16.5px;color:var(--ink);margin-top:6px;max-width:580px;line-height:1.65}
.facts{display:flex;flex-wrap:wrap;gap:8px 22px;margin-top:14px;max-width:620px}
.facts span{font-size:14px;color:var(--ink);display:inline-flex;gap:8px;align-items:center;font-weight:500}
.facts span::before{content:"";flex:none;width:20px;height:20px;background:var(--blue);-webkit-mask:url("data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20width%3D%2724%27%20height%3D%2724%27%20viewBox%3D%270%200%2024%2024%27%20%3E%3Cpath%20fill%3D%27none%27%20stroke%3D%27black%27%20stroke-linecap%3D%27round%27%20stroke-linejoin%3D%27round%27%20stroke-width%3D%271.5%27%20d%3D%27m5%2014l3.5%203.5L19%206.5%27/%3E%3C/svg%3E") center/contain no-repeat;mask:url("data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20width%3D%2724%27%20height%3D%2724%27%20viewBox%3D%270%200%2024%2024%27%20%3E%3Cpath%20fill%3D%27none%27%20stroke%3D%27black%27%20stroke-linecap%3D%27round%27%20stroke-linejoin%3D%27round%27%20stroke-width%3D%271.5%27%20d%3D%27m5%2014l3.5%203.5L19%206.5%27/%3E%3C/svg%3E") center/contain no-repeat}

.stores{display:flex;gap:12px;margin-top:18px;flex-wrap:wrap;align-items:center}
.store-link{display:inline-block;transition:transform 0.2s ease;border-radius:8px}
.store-link:hover{transform:translateY(-2px)}
.store-badge-img{height:42px;width:auto;border-radius:8px;display:block}

/* Hero Stats Bar: 5 Quality & Performance Metrics */
.stats-bar{grid-column:1 / -1;display:grid;grid-template-columns:repeat(5,1fr);background:#fff;border:1px solid var(--line);border-radius:18px;padding:16px 0;margin-top:24px;box-shadow:0 1px 2px rgba(16,24,40,.04),0 8px 24px rgba(31,51,100,.05)}
.stats-bar > div{display:flex;flex-direction:column;gap:2px;padding:0 20px;border-inline-start:1px solid var(--line);text-align:center}
.stats-bar > div:first-child{border-inline-start:0}
.stats-bar b{font-size:26px;font-weight:700;line-height:1.15;color:var(--navy);font-variant-numeric:tabular-nums;white-space:nowrap;letter-spacing:-.3px}
.stats-bar span{font-size:13px;color:var(--muted);white-space:nowrap}

/* Hero Phone Mockup */
.hero-phone-wrapper{position:relative;display:flex;justify-content:center;align-items:center;perspective:1000px;margin:0 auto}
.hero-phone-mockup{
  position:relative;
  width:265px;
  height:510px;
  background:#0F172A;
  border-radius:42px;
  border:8px solid #1E293B;
  box-shadow:0 20px 50px -12px rgba(15,23,42,0.3), 0 8px 20px -4px rgba(0,0,0,0.12);
  overflow:hidden;
  transform:none;
  transition:transform .3s ease, box-shadow .3s ease;
  z-index:2;
}
.hero-phone-mockup:hover{transform:scale(1.02);box-shadow:0 25px 60px -12px rgba(15,23,42,0.4)}
.phone-dynamic-island{
  position:absolute;
  top:9px;
  left:50%;
  transform:translateX(-50%);
  width:80px;
  height:18px;
  background:#000000;
  border-radius:999px;
  z-index:10;
  box-shadow:inset 0 1px 2px rgba(255,255,255,0.15);
}
.phone-screen{width:100%;height:100%;overflow:hidden;background:#FFFFFF;border-radius:34px}
.phone-screen img{width:100%;height:100%;object-fit:cover;object-position:top center;display:block}

/* Hero Floating Badges */
.hero-badge{
  position:absolute;
  background:rgba(255,255,255,0.95);
  backdrop-filter:blur(10px);
  -webkit-backdrop-filter:blur(10px);
  border:1px solid rgba(227,232,238,0.9);
  border-radius:14px;
  padding:10px 14px;
  display:flex;
  align-items:center;
  gap:10px;
  box-shadow:0 12px 28px rgba(15,23,42,0.12);
  z-index:5;
  animation:floatBadge 4s ease-in-out infinite;
}
@keyframes floatBadge{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-8px)}
}
.badge-time{top:18%;inset-inline-end:-20px}
.badge-service{bottom:22%;inset-inline-start:-20px;animation-delay:2s}
.badge-icon{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:15px;flex-shrink:0}
.icon-time{background:#DCFCE7;color:#16A34A}
.icon-service{background:#E0F2FE;color:#0284C7}
.badge-text{display:flex;flex-direction:column;line-height:1.2;text-align:start}
.badge-text small{font-size:10px;color:#64748B;font-weight:700;letter-spacing:.02em}
.badge-text b{font-size:12.5px;color:#0F172A;font-weight:700}

/* Order Widget Section */
.ow-section{background:var(--bg-2)}
.ow{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:24px}
.ow .card{border:1px solid var(--line);border-radius:18px;padding:20px 22px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.02)}
.ow h3{font-size:18px;color:var(--navy);display:flex;justify-content:space-between;align-items:center;gap:8px}
.ow h3 small{font-size:12px;font-weight:600;color:var(--blue);background:var(--sky-soft);padding:3px 10px;border-radius:999px}
.ow .desc{color:var(--ink);font-size:14px;margin-top:8px}
.ow .fit{color:var(--muted);font-size:13px;margin-top:4px}
.steps{display:flex;gap:6px;margin-top:14px;flex-wrap:wrap}
.steps span{font-size:13px;background:var(--bg-2);padding:4px 12px;border-radius:8px;color:var(--muted);font-weight:500}
.same-price{margin-top:14px;font-size:13px;color:var(--muted);text-align:center}

/* Pricing Section */
.pricing-section{border-top:1px solid var(--line)}
.bags{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:22px}
.bag{border:1px solid var(--line);border-radius:18px;padding:18px 20px;background:#fff;position:relative;display:flex;flex-direction:column;justify-content:space-between}
.bag .tag{position:absolute;top:-10px;inset-inline-end:14px;font-size:11px;font-weight:600;color:#fff;background:var(--blue);padding:2px 10px;border-radius:999px}
.bag h4{font-size:16px;font-weight:700;color:var(--navy)}
.bag .qty{display:block;font-size:12.5px;color:var(--muted);margin-top:2px}
.bag .price{font-size:24px;font-weight:700;color:var(--navy);margin-top:10px;line-height:1.2}
.bag .price small{font-size:13px;font-weight:500;color:var(--muted)}
.bag .ex{display:block;font-size:12.5px;color:var(--muted);margin-top:6px}

.ptbl{margin-top:24px;border:1px solid var(--line);border-radius:18px;overflow:hidden}
.ptbl .cap{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:12px 18px;background:var(--bg-2);font-size:14px;font-weight:600}
.ptbl .cap a{font-size:13px;font-weight:500;color:var(--blue)}
.ptbl table{width:100%;border-collapse:collapse;font-size:14px}
.ptbl th{font-weight:500;color:var(--muted);font-size:12px;text-align:center;padding:10px 8px;border-bottom:1px solid var(--line)}
.ptbl th:first-child,.ptbl td:first-child{text-align:start;padding-inline-start:18px}
.ptbl td{text-align:center;padding:10px 8px;border-bottom:1px solid var(--line)}
.ptbl tr:last-child td{border-bottom:0}
.unit{font-size:12px;color:var(--muted)}
.foot-info{margin-top:14px;font-size:13px;color:var(--muted);text-align:center;display:flex;flex-wrap:wrap;justify-content:center;gap:6px 18px}
.foot-info a{color:var(--blue);font-weight:600}

/* Why Us */
.why-section{background:var(--bg-2);padding:64px 0}
.why-section .kicker{display:inline-block;text-align:start;margin:0 0 10px;width:fit-content;background:var(--sky-soft);color:var(--blue);border-radius:999px;padding:4px 14px;font-size:12.5px;font-weight:600}
.why-section h2{text-align:start;font-size:28px;font-weight:700;color:var(--navy);margin:0 0 10px}
.why-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:24px}
.why{border:1px solid var(--line);border-radius:18px;padding:26px 20px 22px;background:#fff;display:flex;flex-direction:column;align-items:center;text-align:center;transition:transform .2s,box-shadow .2s;box-shadow:0 1px 3px rgba(0,0,0,0.03)}
.why:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,0.06)}
.why .ic{color:var(--blue);margin-bottom:12px;display:flex;justify-content:center;align-items:center;background:none!important;width:auto!important;height:auto!important;border-radius:0!important}
.why h4{color:var(--navy);font-size:16px;font-weight:700;margin:0 0 8px;text-align:center}
.why p{color:var(--muted);font-size:13.5px;line-height:1.55;text-align:center;margin:0}
.chips{display:flex;flex-wrap:wrap;gap:6px;justify-content:center;margin-top:12px}
.chips span{font-size:12px;background:var(--bg-2);color:var(--ink);padding:3px 12px;border-radius:999px;white-space:nowrap}

/* Journey */
.journey, .journey-section{background:#fff}
.jgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:22px}
.j{background:#fff;border-radius:14px;padding:16px 18px;border:1px solid var(--line)}
.j h4{color:var(--navy);font-size:15px;font-weight:700}
.j p{color:var(--muted);font-size:14px;margin-top:4px}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:20px}
.stats div{text-align:center;background:var(--navy);color:#fff;border-radius:14px;padding:14px 6px}
.stats b{font-size:22px;display:block;line-height:1.3}
.stats span{font-size:13px;opacity:.85}

/* App Features */
.app-section{background:#fff;padding:60px 0;border-top:1px solid var(--line)}
.app-section .kicker{display:inline-block;text-align:start;margin:0 0 10px;width:fit-content;background:var(--sky-soft);color:var(--blue);border-radius:999px;padding:4px 14px;font-size:12.5px;font-weight:600}
.app-section h2{text-align:start;font-size:28px;font-weight:700;color:var(--navy);margin:0 0 10px}
.feat-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-top:26px}
.f{background:#fff;border-radius:18px;padding:26px 16px 22px;border:1px solid var(--line);min-height:180px;display:flex;flex-direction:column;align-items:center;text-align:center;transition:transform .2s,box-shadow .2s;box-shadow:0 1px 3px rgba(0,0,0,0.03)}
.f:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,0.06)}
.f .ic{color:var(--blue);margin-bottom:14px;display:flex;justify-content:center;align-items:center;background:none!important;width:auto!important;height:auto!important;border-radius:0!important}
.f h4{color:var(--navy);font-size:16px;font-weight:700;margin:0 0 8px;text-align:center}
.f p{color:var(--muted);font-size:13px;line-height:1.55;text-align:center;margin:0}
.f p b{color:var(--green);font-weight:700}

/* Reviews */
.reviews-section{background:var(--bg-2);padding:60px 0;border-top:1px solid var(--line)}
.rgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:22px}
.r{border:1px solid var(--line);border-radius:18px;padding:18px 20px;background:#fff;display:flex;flex-direction:column;justify-content:space-between}
.r q{font-size:14px;color:var(--ink);line-height:1.6;quotes:none}
.r .who{margin-top:14px;padding-top:10px;border-top:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;font-size:12px}
.r .who b{color:var(--navy)}
.r .who .svc{font-size:12px;font-weight:600;color:var(--blue);background:var(--sky-soft);padding:3px 12px;border-radius:999px;display:inline-block}
.reviews-dots{display:none}



/* Original Home Page Footer (#footer) */
#footer{background-color:#1F3364 !important;color:#fff;padding-top:4rem;padding-bottom:3rem;border-top:1px solid rgba(255,255,255,0.12);font-size:14px}
#footer .max-w-7xl{max-width:1200px;margin:0 auto;padding:0 24px}
#footer .grid{display:grid !important;grid-template-columns:1.3fr 1fr 1fr 1.2fr !important;gap:36px !important;margin-bottom:48px !important;text-align:start !important}
#footer h3{font-size:14px !important;font-weight:800 !important;color:#fff !important;text-transform:uppercase !important;letter-spacing:0.05em !important;margin-bottom:18px !important}
#footer ul{list-style:none !important;padding:0 !important;margin:0 !important}
#footer li{margin:9px 0 !important;font-size:13.5px !important}
#footer a{color:rgba(255,255,255,0.75) !important;transition:color 0.2s !important}
#footer a:hover{color:#fff !important}
#footer p{color:rgba(255,255,255,0.75) !important;font-size:13.5px !important;line-height:1.65 !important}

/* Footer Images and SVGs - Fix Gigantic Icons */
#footer svg{width:16px !important;height:16px !important;max-width:16px !important;max-height:16px !important;flex-shrink:0 !important;display:inline-block !important;fill:currentColor !important}
#footer img.h-10,#footer .h-10,#footer div>img[alt="Logo"],#footer img[width="1503"],#footer .footer-logo{height:64px !important;width:auto !important;max-height:64px !important;max-width:240px !important;object-fit:contain !important;filter:brightness(0) invert(1) !important;-webkit-filter:brightness(0) invert(1) !important;opacity:1 !important;display:block !important}

/* Footer Contact Info (Phone & Email) */
#footer .text-xs{font-size:13px !important}
#footer .text-brand-500,#footer svg.text-brand-500{color:#38BDF8 !important;fill:#38BDF8 !important;width:16px !important;height:16px !important}
#footer .flex.items-center.gap-2{display:flex !important;align-items:center !important;gap:10px !important;margin-bottom:8px !important}
#footer span[dir="ltr"]{direction:ltr !important;display:inline-block !important}

/* Social Buttons */
#footer .flex.flex-nowrap{display:flex !important;flex-wrap:wrap !important;gap:8px !important}
#footer .social-btn-twitter,#footer .social-btn-instagram,#footer .social-btn-tiktok,#footer .social-btn-snapchat,#footer .social-btn-youtube,#footer .social-btn-facebook{width:38px !important;height:38px !important;border-radius:11px !important;background:rgba(255,255,255,0.08) !important;border:1px solid rgba(255,255,255,0.18) !important;display:inline-flex !important;align-items:center !important;justify-content:center !important;color:#fff !important;transition:all 0.2s !important;margin-inline-end:4px}
#footer .social-btn-twitter:hover{background:#0284c7 !important;border-color:#0284c7 !important}
#footer .social-btn-instagram:hover{background:#db2777 !important;border-color:#db2777 !important}
#footer .social-btn-tiktok:hover{background:#000 !important;border-color:#fff !important}
#footer .social-btn-snapchat:hover{background:#eab308 !important;border-color:#eab308 !important;color:#000 !important}
#footer .social-btn-youtube:hover{background:#dc2626 !important;border-color:#dc2626 !important}
#footer .social-btn-facebook:hover{background:#2563eb !important;border-color:#2563eb !important}

/* Newsletter Form */
#footer form{position:relative !important;width:100% !important;max-width:300px !important;margin-bottom:18px !important}
#footer input[type="email"]{width:100% !important;background:rgba(255,255,255,0.08) !important;border:1px solid rgba(255,255,255,0.2) !important;border-radius:12px !important;padding:10px 42px 10px 14px !important;font-size:13.5px !important;color:#fff !important;outline:none !important}
[dir="rtl"] #footer input[type="email"]{padding:10px 14px 10px 42px !important}
#footer input[type="email"]::placeholder{color:rgba(255,255,255,0.5) !important}
#footer input[type="email"]:focus{border-color:#38BDF8 !important;background:rgba(255,255,255,0.12) !important}
#footer button[type="submit"]{position:absolute !important;top:50% !important;transform:translateY(-50%) !important;background:none !important;border:none !important;cursor:pointer !important;padding:6px !important;color:#38BDF8 !important;display:flex !important;align-items:center !important;justify-content:center !important}
[dir="rtl"] #footer button[type="submit"]{left:8px !important;right:auto !important}
[dir="ltr"] #footer button[type="submit"]{right:8px !important;left:auto !important}
#footer button[type="submit"] svg{width:16px !important;height:16px !important;fill:#38BDF8 !important}

/* Footer Bottom */
#footer .border-t{border-top:1px solid rgba(255,255,255,0.12) !important;padding-top:24px !important;margin-top:12px !important;display:flex !important;justify-content:space-between !important;align-items:center !important;flex-wrap:wrap !important;gap:16px !important}
#footer .border-t p{color:rgba(255,255,255,0.65) !important;font-size:13px !important;margin:0 !important}
#footer .store-badge{height:42px !important;width:auto !important;border-radius:8px !important;display:inline-block !important}

/* Footer Responsive */
@media (max-width:992px){
  #footer .grid{grid-template-columns:1fr 1fr !important;gap:32px !important}
}
@media (max-width:600px){
  #footer .grid{grid-template-columns:1fr !important;text-align:center !important;gap:28px !important}
  #footer .space-y-6,#footer .flex-col{align-items:center !important}
  #footer .flex.flex-nowrap{justify-content:center !important}
  #footer .border-t{justify-content:center !important;text-align:center !important}
}

/* FAQ */
.faq{max-width:760px;margin:24px auto 0}
.faq details{border:1px solid var(--line);border-radius:14px;margin-bottom:8px;background:#fff;padding:12px 18px}
.faq summary{font-weight:600;font-size:15px;color:var(--navy);cursor:pointer;list-style:none}
.faq summary::-webkit-details-marker{display:none}
.faq p{margin-top:8px;color:var(--muted);font-size:14px}

/* Final CTA */
.final{background:var(--navy);color:#fff;border-radius:20px;padding:32px 20px;text-align:center;margin-top:36px}
.final h2{color:#fff;font-size:24px}
.final .pts{margin-top:8px;font-size:14px;color:rgba(255,255,255,0.85);opacity:.85;display:flex;flex-wrap:wrap;justify-content:center;gap:4px 16px}
.final .pts span{white-space:nowrap;color:#fff}
.final .stores{justify-content:center;margin-top:16px}
.final .store-badge-img{border:1px solid #fff;border-radius:8px}
.final .sb{border-color:#fff}
.final .contact-line{margin-top:16px;font-size:13px;color:rgba(255,255,255,0.85);opacity:.85}
.final .contact-line a{color:#fff;text-decoration:underline;white-space:nowrap;font-weight:600}

/* Footer */
footer{background:#fff;border-top:1px solid var(--line);padding:44px 0 32px}
.fgrid{display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:32px}
.fgrid h5{font-size:14px;font-weight:600;color:var(--navy);margin-bottom:12px}
.fgrid ul{list-style:none}
.fgrid li{margin-bottom:8px;font-size:13px;color:var(--muted)}
.fgrid a:hover{color:var(--navy)}
.social{display:flex;gap:12px;margin-top:12px;align-items:center}
.social a{width:34px;height:34px;border-radius:8px;background:var(--bg-2);display:grid;place-items:center;color:var(--navy);font-size:16px;transition:all 0.2s}
.social a:hover{background:var(--navy);color:#fff}

/* Sticky Mobile CTA (Original Home Page Style) */
.sticky-mobile-cta{
  display:none;
  position:fixed;
  bottom:0;
  left:0;
  right:0;
  background:rgba(255,255,255,0.98);
  backdrop-filter:blur(14px);
  -webkit-backdrop-filter:blur(14px);
  border-top:1px solid #e2e8f0;
  padding:10px 18px;
  box-shadow:0 -4px 20px rgba(0,0,0,0.08);
  justify-content:space-between;
  align-items:center;
  z-index:9999;
}
.sticky-mobile-cta .cta-content{
  display:flex;
  flex-direction:column;
  text-align:start;
}
.sticky-mobile-cta .stars{
  color:#fbbf24;
  font-size:11px;
  letter-spacing:2px;
  margin-bottom:2px;
  line-height:1;
}
.sticky-mobile-cta-title{
  font-size:13.5px;
  font-weight:800;
  color:#0f172a;
  line-height:1.3;
}
.sticky-mobile-cta-btn{
  background:linear-gradient(135deg,#0ea5e9,#0284c7);
  color:#ffffff !important;
  padding:9px 20px;
  border-radius:999px;
  font-weight:700;
  font-size:13.5px;
  text-decoration:none;
  box-shadow:0 4px 14px rgba(14,165,233,0.4);
  white-space:nowrap;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  transition:transform .2s ease;
}
.sticky-mobile-cta-btn:active{
  transform:scale(0.97);
}

/* Responsive Queries */
@media (max-width: 992px){
  header.site-header nav .links{display:none}
  header.site-header nav .ctas{display:none}
  .burger{display:grid}
  .hero-grid{grid-template-columns:1fr;text-align:center}
  .hero .lead,.facts{margin-inline:auto}
  .facts,.stores{justify-content:center}
  .proof{justify-content:center}
  .ow{grid-template-columns:1fr}
  .bags{grid-template-columns:repeat(2,1fr)}
  .why-grid{grid-template-columns:repeat(2,1fr)}
  .jgrid{grid-template-columns:repeat(2,1fr)}
  .feat-grid{grid-template-columns:repeat(3,1fr);gap:12px}
  .feat-grid .f:last-child{grid-column:auto}
  .rgrid{grid-template-columns:repeat(2,1fr)}
  .stats{grid-template-columns:repeat(2,1fr)}
  .fgrid{grid-template-columns:1fr 1fr}
}

@media (max-width: 600px){
  section{padding:40px 0}
  .hero{padding:84px 0 36px}
  h1{font-size:32px}
  h2{font-size:24px}

  .kicker.sa{font-size:12.5px;gap:8px;padding:5px 12px}
  .kicker.sa em{height:12px}

  /* Facts on mobile */
  .facts{
    display:flex !important;
    flex-direction:column !important;
    gap:8px !important;
    max-width:100% !important;
    margin-top:14px !important;
    text-align:start !important;
  }
  .facts span{
    font-size:13.5px !important;
    line-height:1.45 !important;
    background:none !important;
    border:none !important;
    padding:0 !important;
    box-shadow:none !important;
  }

  /* Stats bar on mobile */
  .stats-bar{
    display:flex !important;
    flex-direction:column !important;
    padding:4px 18px !important;
    border-radius:18px !important;
    margin-top:20px !important;
  }
  .stats-bar > div{
    flex-direction:row-reverse !important;
    justify-content:space-between !important;
    align-items:center !important;
    gap:12px !important;
    padding:13px 0 !important;
    border-inline-start:0 !important;
    border-top:1px solid var(--line) !important;
    text-align:start !important;
  }
  .stats-bar > div:first-child{border-top:0 !important}
  .stats-bar b{font-size:20px !important;letter-spacing:-.2px !important}
  .stats-bar span{font-size:13.5px !important;color:var(--ink) !important;white-space:nowrap !important}

  /* Hero phone on mobile */
  .hero-phone-mockup{width:230px;height:470px;border-radius:36px;border-width:7px;transform:none}
  .phone-screen{border-radius:29px}
  .hero-badge{display:none}

  .store-badge-img{height:40px}

  /* Bags 2 per row on mobile */
  .bags{
    grid-template-columns:repeat(2, 1fr) !important;
    gap:10px !important;
  }
  .bag{
    padding:14px 10px !important;
    border-radius:14px !important;
  }
  .bag .tag{
    top:-9px !important;
    inset-inline-end:8px !important;
    font-size:9.5px !important;
    padding:2px 7px !important;
  }
  .bag h4{
    font-size:13.5px !important;
    line-height:1.3 !important;
  }
  .bag .qty{
    font-size:11px !important;
    margin-top:2px !important;
  }
  .bag .price{
    font-size:18px !important;
    margin-top:8px !important;
  }
  .bag .price small{
    font-size:11px !important;
  }
  .bag .ex{
    font-size:10px !important;
    margin-top:4px !important;
    line-height:1.3 !important;
  }

  /* Why Us 2 per row on mobile */
  .why-grid{
    grid-template-columns:repeat(2, 1fr) !important;
    gap:10px !important;
  }
  .why{
    padding:14px 12px !important;
    border-radius:14px !important;
  }
  .why .ic, .f .ic{
    width:auto !important;
    height:auto !important;
    font-size:inherit !important;
    margin-bottom:8px !important;
  }
  .why .ic svg.hi, .f .ic svg.hi{
    width:24px !important;
    height:24px !important;
  }
  .why h4{
    font-size:13.5px !important;
    line-height:1.35 !important;
  }
  .why p{
    font-size:12px !important;
    line-height:1.45 !important;
    margin-top:4px !important;
  }
  .chips{
    gap:4px !important;
    margin-top:6px !important;
  }
  .chips span{
    font-size:10.5px !important;
    padding:2px 6px !important;
  }

  /* Journey steps 2 per row on mobile */
  .jgrid{
    grid-template-columns:repeat(2, 1fr) !important;
    gap:10px !important;
  }
  .j{
    padding:14px 12px !important;
    border-radius:14px !important;
  }
  .j h4{
    font-size:13.5px !important;
    line-height:1.35 !important;
  }
  .j p{
    font-size:12px !important;
    line-height:1.45 !important;
    margin-top:4px !important;
  }

  .feat-grid{grid-template-columns:repeat(2,1fr);gap:10px}
  .feat-grid .f:last-child{grid-column:span 2}

  /* Reviews slider on mobile */
  .rgrid{
    display:flex !important;
    overflow-x:auto !important;
    scroll-snap-type:x mandatory !important;
    gap:14px !important;
    padding:10px 4px 14px !important;
    scrollbar-width:none !important;
    -ms-overflow-style:none !important;
    -webkit-overflow-scrolling:touch !important;
  }
  .rgrid::-webkit-scrollbar{display:none}
  .rgrid .r{
    flex:0 0 86% !important;
    min-width:86% !important;
    scroll-snap-align:center !important;
    box-shadow:0 4px 14px rgba(0,0,0,0.04) !important;
  }
  .reviews-dots{
    display:flex !important;
    justify-content:center !important;
    align-items:center !important;
    gap:6px !important;
    margin-top:12px !important;
  }
  .reviews-dots .dot{
    width:8px;
    height:8px;
    border-radius:999px;
    background:#CBD5E1;
    transition:all .25s ease;
    cursor:pointer;
  }
  .reviews-dots .dot.active{
    width:22px;
    background:var(--blue);
  }

  /* Final CTA on mobile */
  .final{padding:26px 16px;border-radius:18px}
  .final h2{font-size:18px !important}
  .final .pts{font-size:13px;gap:6px 12px}
  .fgrid{grid-template-columns:1fr}
}

@media (max-width: 768px){
  .sticky-mobile-cta{display:flex !important}
  body{padding-bottom:72px !important}
}
</style>
</head>
<body>

  <!-- Original Home Page Navbar -->
  @include('layouts.partials.navbar')

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="wrap hero-grid">
      <div>
        <!-- Platform Badge (Kicker) -->
        <div class="sa-wrap">
          <span class="kicker sa">
            <i></i>
            <b>{{ $isRtl ? 'كلين ستيشن' : 'Clean Station' }}</b>
            <em></em>
            {{ $isRtl ? 'منصة سعودية للعناية بالغسيل' : 'Saudi Laundry Care Platform' }}
          </span>
        </div>

        @if($isRtl)
          <h1>غسيلك... أذكى وأنظف!</h1>
          <p class="lead">غسيل وكوي ودراي كلين لملابسك ومفروشاتك وسجادك وأحذيتك، نستلمها من بابك ونرجعها لك في الوقت اللي تختاره</p>
          <div class="facts">
            <span>تختار وقت الاستلام والتسليم</span>
            <span>أسعار واضحة قبل الطلب</span>
            <span>طلب كل عميل يُغسل لحاله</span>
            <span>توصيل مجاني للطلبات بـ{{ $freeDeliveryMin }} ريال وما فوق</span>
          </div>
        @else
          <h1><span class="nw">Your laundry,</span> <span class="nw">smarter and cleaner</span></h1>
          <p class="lead">Wash & iron, ironing and dry cleaning for your clothes, bedding, carpets and shoes, picked up from your door and returned at the time you choose</p>
          <div class="facts">
            <span>You choose pickup and delivery times</span>
            <span>Clear prices before you order</span>
            <span>Every customer's order washed separately</span>
            <span>Free delivery on orders of SAR {{ $freeDeliveryMin }} and above</span>
          </div>
        @endif

        <!-- Official App Store & Google Play Badges -->
        <div class="stores">
          <a href="{{ $gPlayLink }}" target="_blank" rel="noopener" class="store-link" aria-label="Google Play">
            <img src="{{ asset($isRtl ? 'assets/store-badges/google-play-ar.png' : 'assets/store-badges/google-play.svg') }}" alt="Google Play" class="store-badge-img">
          </a>
          <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="store-link" aria-label="App Store">
            <img src="{{ asset($isRtl ? 'assets/store-badges/app-store-ar.svg' : 'assets/store-badges/app-store.svg') }}" alt="App Store" class="store-badge-img">
          </a>
        </div>
      </div>

      <!-- Hero App Preview in Realistic Smartphone Mockup Chassis -->
      <div class="hero-phone-wrapper">
        <div class="hero-phone-mockup">
          <div class="phone-dynamic-island"></div>
          <div class="phone-screen">
            @if(!empty($heroImageUrl))
              <img src="{{ $heroImageUrl }}" onerror="this.onerror=null;this.src='{{ asset('assets/images/app-screen.png') }}';" alt="{{ $isRtl ? 'تطبيق كلين ستيشن' : 'Clean Station App' }}">
            @else
              <img src="{{ asset('assets/images/app-screen.png') }}" alt="{{ $isRtl ? 'تطبيق كلين ستيشن' : 'Clean Station App' }}">
            @endif
          </div>
        </div>
      </div>

      <!-- Stats Bar (Downloads, Support + Quality Indicators) -->
      <div class="stats-bar">
        <div><b>{{ $isRtl ? '+15 ألف' : '15K+' }}</b><span>{{ $isRtl ? 'تحميل للتطبيق' : 'App downloads' }}</span></div>
        <div><b>24/7</b><span>{{ $isRtl ? 'خدمة عملاء' : 'Customer service' }}</span></div>
        <div><b>98%</b><span><span class="nw">{{ $isRtl ? 'استلام وتسليم في الموعد' : 'On-time pickup & delivery' }}</span></span></div>
        <div><b>0.05%</b><span>{{ $isRtl ? 'نسبة الشكاوى' : 'Complaint rate' }}</span></div>
        <div><b>0.00%</b><span><span class="nw">{{ $isRtl ? 'قطع ناقصة أو زائدة' : 'Missing or extra items' }}</span></span></div>
      </div>
    </div>
  </section>

  <!-- Two Ways to Order (Order Widget) -->
  <section class="ow-section" id="order-modes">
    <div class="wrap">
      <h2>{{ $isRtl ? 'طريقتين للطلب من التطبيق' : 'Two ways to order in the app' }}</h2>
      <div class="ow">
        <!-- 1. Quick Order -->
        <div class="card">
          <h3>
            <span>{{ $isRtl ? 'طلب سريع' : 'Quick order' }}</span>
            <small>{{ $isRtl ? 'بنقرة واحدة' : 'One tap' }}</small>
          </h3>
          <p class="desc">{{ $isRtl ? 'حدد الموقع والوقت فقط، ونعدّ القطع ونحسبها بنفس أسعار القائمة' : 'Set your location and time, and we count and price your items at list prices' }}</p>
          <p class="fit">{{ $isRtl ? 'مناسب لو مستعجل أو الكمية كبيرة' : "Ideal when you're in a hurry or have lots of items" }}</p>
          <div class="steps">
            <span>{{ $isRtl ? 'الموقع' : 'Location' }}</span>
            <span>{{ $isRtl ? 'الوقت' : 'Time' }}</span>
            <span>{{ $isRtl ? 'التأكيد' : 'Confirm' }}</span>
          </div>
        </div>

        <!-- 2. Detailed Order -->
        <div class="card">
          <h3>
            <span>{{ $isRtl ? 'طلب مفصل' : 'Detailed order' }}</span>
            <small>{{ $isRtl ? 'تشوف الإجمالي مقدماً' : 'See the total upfront' }}</small>
          </h3>
          <p class="desc">{{ $isRtl ? 'اختر قطعك وشوف الإجمالي قبل ما تطلب' : 'Pick your items and see the total before you order' }}</p>
          <p class="fit">{{ $isRtl ? 'مناسب لو تبي تضبط ميزانيتك' : 'Ideal if you want to manage your budget' }}</p>
          <div class="steps">
            <span>{{ $isRtl ? 'اختر القطع' : 'Pick items' }}</span>
            <span>{{ $isRtl ? 'الموقع' : 'Location' }}</span>
            <span>{{ $isRtl ? 'الوقت' : 'Time' }}</span>
          </div>
        </div>
      </div>
      <p class="same-price">{{ $isRtl ? 'الأسعار نفسها في الطريقتين، وكلها واضحة في التطبيق' : 'Same prices both ways, all clearly shown in the app' }}</p>
    </div>
  </section>

  <!-- Prices Section (Economy Bags + Per Piece) -->
  <section class="pricing-section" id="pricing">
    <div class="wrap">
      <span class="kicker">{{ $isRtl ? 'الأسعار' : 'Pricing' }}</span>
      <h2>{{ $isRtl ? 'الحقائب الاقتصادية' : 'Value laundry bags' }}</h2>
      <p class="sub">{{ $isRtl ? 'عبّ الحقيبة بملابسك وادفع سعر ثابت يوفّر عليك، والقطع الزايدة بسعر رمزي' : 'Fill the bag and pay one fixed price that saves you more, with extra items at a small fee' }}</p>

      <!-- Dynamic Bags from Backend -->
      <div class="bags">
        @forelse($bags as $bag)
          @php
            $bagName = $bag->translate($lang) ? $bag->translate($lang)->name : $bag->name;
            $cleanTitle = trim(preg_replace('/[-–]\s*\d+\s*(قطعة|قطع|pieces?|items?)/iu', '', $bagName));
            $isMostPopular = ($bag->id == $mostOrderedBagId);
            $extraPrice = $extraPrices[$bag->id] ?? null;

            // Subtitle: Piece count + service type as in Image 2
            if ($bag->id == 23 || str_contains($bagName, 'الكوي فقط') || (str_contains($bagName, 'الكوي') && !str_contains($bagName, 'الغسيل'))) {
              $subText = $isRtl ? '25 قطعة · كوي فقط' : '25 Pieces · Iron only';
              if (!$extraPrice) $extraPrice = 4;
            } elseif ($bag->id == 221 || str_contains($bagName, 'الطي') || str_contains($bagName, 'Fold')) {
              $subText = $isRtl ? '25 قطعة · بدون كوي' : '25 Pieces · No iron';
              if (!$extraPrice) $extraPrice = 4;
            } elseif ($bag->id == 22 || (str_contains($bagName, 'الغسيل') && str_contains($bagName, 'الكوي'))) {
              $subText = $isRtl ? '25 قطعة · غسيل وكوي' : '25 Pieces · Wash & iron';
              if (!$extraPrice) $extraPrice = 5;
            } elseif ($bag->id == 318 || str_contains($bagName, 'جاف') || str_contains($bagName, 'Dry')) {
              $subText = $isRtl ? '10 قطع · غسيل جاف' : '10 Pieces · Dry clean';
              if (!$extraPrice) $extraPrice = 19;
            } else {
              $subText = '';
              if (preg_match('/(\d+)\s*(قطعة|قطع|piece|pieces|items)/iu', $bagName, $m)) {
                $subText = $m[0];
              }
            }
          @endphp
          <div class="bag">
            @if($isMostPopular)
              <span class="tag">{{ $isRtl ? 'الأكثر طلباً' : 'Most popular' }}</span>
            @endif
            <h4>{{ $cleanTitle }}</h4>
            @if($subText)
              <span class="qty">{{ $subText }}</span>
            @endif
            <div class="price">
              @if($isRtl)
                {{ round($bag->price) }} <small>ريال</small>
              @else
                <small>SAR</small> {{ round($bag->price) }}
              @endif
            </div>
            @if($extraPrice)
              <span class="ex">{{ $isRtl ? "القطعة الإضافية {$extraPrice} ريال" : "Extra piece SAR {$extraPrice}" }}</span>
            @endif
          </div>
        @empty
          <p class="desc">{{ $isRtl ? 'لا توجد حقائب متاحة حالياً' : 'No bags available at the moment' }}</p>
        @endforelse
      </div>

      <!-- Dynamic Per-Piece Table from Backend (5 Sample Items) -->
      <div class="ptbl">
        <div class="cap">
          <span>{{ $isRtl ? 'أو بالقطعة، حسب الخدمة' : 'Or per item, by service' }}</span>
          <a href="{{ route('pricing') }}">{{ $isRtl ? 'قائمة الأسعار كاملة ←' : 'Full price list →' }}</a>
        </div>
        <table>
          <thead>
            <tr>
              <th>{{ $isRtl ? 'القطعة' : 'Item' }}</th>
              <th>{{ $isRtl ? 'غسيل وكوي' : 'Wash & iron' }}</th>
              <th>{{ $isRtl ? 'كوي فقط' : 'Iron only' }}</th>
              <th>{{ $isRtl ? 'دراي كلين' : 'Dry clean' }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($sampleProducts as $sp)
              <tr>
                <td>{{ $isRtl ? $sp['name_ar'] : $sp['name_en'] }}</td>
                <td>
                  @if($sp['wash_iron'] !== null)
                    @if($isRtl) {{ $sp['wash_iron'] }} <span class="unit">ريال</span> @else <span class="unit">SAR</span> {{ $sp['wash_iron'] }} @endif
                  @else
                    —
                  @endif
                </td>
                <td>
                  @if($sp['iron_only'] !== null)
                    @if($isRtl) {{ $sp['iron_only'] }} <span class="unit">ريال</span> @else <span class="unit">SAR</span> {{ $sp['iron_only'] }} @endif
                  @else
                    —
                  @endif
                </td>
                <td>
                  @if($sp['dry_clean'] !== null)
                    @if($isRtl) {{ $sp['dry_clean'] }} <span class="unit">ريال</span> @else <span class="unit">SAR</span> {{ $sp['dry_clean'] }} @endif
                  @else
                    —
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4">{{ $isRtl ? 'لا توجد بيانات أسعار' : 'No pricing data available' }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <p class="foot-info">
        <span>{{ $isRtl ? 'الطلب من تطبيق كلين ستيشن' : 'Order in the Clean Station app' }}</span>
        <span>{{ $isRtl ? "توصيل مجاني للطلبات بـ{$freeDeliveryMin} ريال وما فوق" : "Free delivery on orders of SAR {$freeDeliveryMin} and above" }}</span>
        <a href="{{ $appStoreLink }}" target="_blank" rel="noopener">{{ $isRtl ? 'حمّل التطبيق' : 'Download the app' }}</a>
      </p>
    </div>
  </section>

  <!-- Why Clean Station? -->
  <section class="why-section" id="why-us">
    <div class="wrap">
      <div>
        <span class="kicker">{{ $isRtl ? 'ليش كلين ستيشن؟' : 'Why Clean Station?' }}</span>
        <h2>{{ $isRtl ? 'كلهم يغسلون... بس كلين ستيشن غير الكل' : 'Everyone washes... but Clean Station is unlike the rest' }}</h2>
      </div>
      <div class="why-grid">
        <div class="why">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M3 14v-4c0-3.771 0-5.657 1.172-6.828S7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172S21 6.229 21 10v4c0 3.771 0 5.657-1.172 6.828S16.771 22 13 22h-2c-3.771 0-5.657 0-6.828-1.172S3 17.771 3 14"></path><path d="M17 13a5 5 0 1 1-10 0a5 5 0 0 1 10 0"></path><path d="M7 13q2.5-2 5 0t5 0M7.125 6H7m.25 0a.25.25 0 1 1-.5 0a.25.25 0 0 1 .5 0"></path></g></svg>
          </div>
          <h4>{{ $isRtl ? 'طلبك يُغسل لحاله' : 'Washed on its own' }}</h4>
          <p>{{ $isRtl ? 'طلب كل عميل يُغسل لحاله، وما يختلط مع غيره' : "Every customer's order is washed separately, never mixed with others" }}</p>
        </div>
        <div class="why">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><path d="m15 2l.539 2.392a5.39 5.39 0 0 0 4.07 4.07L22 9l-2.392.539a5.39 5.39 0 0 0-4.07 4.07L15 16l-.539-2.392a5.39 5.39 0 0 0-4.07-4.07L8 9l2.392-.539a5.39 5.39 0 0 0 4.07-4.07zM7 12l.385 1.708a3.85 3.85 0 0 0 2.907 2.907L12 17l-1.708.385a3.85 3.85 0 0 0-2.907 2.907L7 22l-.385-1.708a3.85 3.85 0 0 0-2.907-2.907L2 17l1.708-.385a3.85 3.85 0 0 0 2.907-2.907z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.5"></path></svg>
          </div>
          <h4>{{ $isRtl ? 'تفضيلاتك عند الطلب' : 'Your preferences' }}</h4>
          <p>{{ $isRtl ? 'تحدد اللي تحبه، ونطبقه على كل طلب' : 'Choose what you like, and we apply it every time' }}</p>
          <div class="chips">
            <span>{{ $isRtl ? 'النشا' : 'Starch' }}</span>
            <span>{{ $isRtl ? 'معطّرنا الخاص' : 'Signature fragrance' }}</span>
          </div>
        </div>
        <div class="why">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18.99 19H19m-.01 0c-.622.617-1.75.464-2.542.464c-.972 0-1.44.19-2.133.883C13.725 20.937 12.934 22 12 22s-1.725-1.063-2.315-1.653c-.694-.693-1.162-.883-2.133-.883c-.791 0-1.92.154-2.543-.464c-.627-.622-.473-1.756-.473-2.552c0-1.007-.22-1.47-.937-2.186C2.533 13.196 2 12.662 2 12s.533-1.196 1.6-2.262c.64-.64.936-1.274.936-2.186c0-.791-.154-1.92.464-2.543c.622-.627 1.756-.473 2.552-.473c.912 0 1.546-.297 2.186-.937C10.804 2.533 11.338 2 12 2s1.196.533 2.262 1.6c.64.64 1.274.936 2.186.936c.791 0 1.92-.154 2.543.464c.627.622.473 1.756.473 2.552c0 1.007.22 1.47.937 2.186C21.467 10.804 22 11.338 22 12s-.533 1.196-1.6 2.262c-.716.717-.936 1.18-.936 2.186c0 .796.154 1.93-.473 2.552Z"></path><path d="M9 12.893s1.2.652 1.8 1.607c0 0 1.8-3.75 4.2-5" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>
          </div>
          <h4>{{ $isRtl ? 'فحص قبل التسليم' : 'Pre-delivery check' }}</h4>
          <p>{{ $isRtl ? 'كي بالبخار، وفحص كل قطعة قبل التغليف' : 'Steam ironing and quality check before packing' }}</p>
        </div>
        <div class="why">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><path d="M3 11.99v2.51c0 3.3 0 4.95 1.025 5.975S6.7 21.5 10 21.5h4c3.3 0 4.95 0 5.975-1.025S21 17.8 21 14.5v-2.51c0-1.682 0-2.522-.356-3.25s-1.02-1.244-2.346-2.276l-2-1.555C14.233 3.303 13.2 2.5 12 2.5s-2.233.803-4.298 2.409l-2 1.555C4.375 7.496 3.712 8.012 3.356 8.74S3 10.308 3 11.99" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg>
          </div>
          <h4>{{ $isRtl ? 'كل غسيل بيتك' : 'All home laundry' }}</h4>
          <p>{{ $isRtl ? 'من مكان واحد، وبنفس التطبيق' : 'From one place, in one app' }}</p>
          <div class="chips">
            <span>{{ $isRtl ? 'ملابس' : 'Clothes' }}</span>
            <span>{{ $isRtl ? 'مفروشات السرير' : 'Bedding' }}</span>
            <span>{{ $isRtl ? 'بطانيات' : 'Blankets' }}</span>
            <span>{{ $isRtl ? 'سجاد' : 'Carpets' }}</span>
            <span>{{ $isRtl ? 'أحذية' : 'Shoes' }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Laundry Journey (6 Steps + Counters) -->
  <section class="journey journey-section" id="services">
    <div class="wrap">
      <span class="kicker">{{ $isRtl ? 'خطوات العمل' : 'Work steps' }}</span>
      <h2>{{ $isRtl ? 'رحلة العناية المتكاملة' : 'Complete care journey' }}</h2>
      <div class="jgrid">
        <div class="j">
          <h4>{{ $isRtl ? '1 · الطلب الذكي' : '1 · Smart order' }}</h4>
          <p>{{ $isRtl ? 'حدد الموقع ووقت الاستلام والتسليم من التطبيق' : 'Set location and pickup & delivery times from the app' }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '2 · جهّز غسيلك' : '2 · Prepare laundry' }}</h4>
          <p>{{ $isRtl ? 'سلّمه للمندوب بيدك، أو علّقه على الباب لو تفضّل' : 'Hand it to the driver or hang it on the door if you prefer' }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '3 · الاستلام' : '3 · Pickup' }}</h4>
          <p>{{ $isRtl ? 'مندوبنا الرسمي يستلم في الوقت اللي حددته' : 'Our official driver collects it at the time you chose' }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '4 · الغسيل والمعالجة' : '4 · Washing & treatment' }}</h4>
          <p>{{ $isRtl ? 'غسيل منفصل 100%، وطلب كل عميل يُغسل لحاله' : "100% separate washing, every customer's order on its own" }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '5 · الكوي والفرز' : '5 · Ironing & sorting' }}</h4>
          <p>{{ $isRtl ? 'كي بالبخار، وفحص الجودة، وتغليف مرتب' : 'Steam ironing, quality check and neat packaging' }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '6 · ترجع لك' : '6 · Back to you' }}</h4>
          <p>{{ $isRtl ? 'في الوقت اللي اخترته، بيدك أو معلّقة على الباب' : 'At the time you chose, in person or hung on your door' }}</p>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats">
        <div>
          <b>{{ $isRtl ? '+15 ألف' : '15K+' }}</b>
          <span>{{ $isRtl ? 'تحميل للتطبيق' : 'App downloads' }}</span>
        </div>
        <div>
          <b>{{ $isRtl ? '21–24 ساعة' : '21–24 hrs' }}</b>
          <span>{{ $isRtl ? 'أغلب مواعيد التسليم' : 'Most delivery slots' }}</span>
        </div>
        <div>
          <b>24/7</b>
          <span>{{ $isRtl ? 'خدمة عملاء' : 'Customer service' }}</span>
        </div>
        <div>
          <b>100%</b>
          <span>{{ $isRtl ? 'غسيل منفصل' : 'Separate washing' }}</span>
        </div>
      </div>
    </div>
  </section>

  <!-- App Features -->
  <section class="app-section" id="app">
    <div class="wrap">
      <div>
        <span class="kicker">{{ $isRtl ? 'تطبيق كلين ستيشن' : 'Clean Station App' }}</span>
        <h2>{{ $isRtl ? 'كل شي من جوالك' : 'Everything from your phone' }}</h2>
      </div>
      <div class="feat-grid">
        <div class="f">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M14 3H5a2 2 0 1 0 0 4h13c0-.93 0-1.395-.102-1.776a3 3 0 0 0-2.121-2.122C15.395 3 14.93 3 14 3"></path><path d="M3 5v10c0 2.828 0 4.243.879 5.121C4.757 21 6.172 21 9 21h6c2.828 0 4.243 0 5.121-.879C21 19.243 21 17.828 21 15v-2c0-2.828 0-4.243-.879-5.121C19.243 7 17.828 7 15 7H7"></path><path d="M21 12h-2c-.465 0-.698 0-.888.051a1.5 1.5 0 0 0-1.06 1.06C17 13.303 17 13.536 17 14s0 .697.051.888a1.5 1.5 0 0 0 1.06 1.06c.191.052.424.052.889.052h2"></path></g></svg>
          </div>
          <h4>{{ $isRtl ? 'المحفظة الذكية' : 'Smart wallet' }}</h4>
          <p>{{ $isRtl ? 'ادفع 440 ريال واحصل على رصيد 500، ' : 'Pay SAR 440 and get SAR 500 credit, ' }}<b>{{ $isRtl ? 'وفّر حتى 13%' : 'save up to 13%' }}</b></p>
        </div>
        <div class="f">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M3 20.5c.284-3.694 3.3-6.78 7-6.962q.469-.023 1-.038l.995.066a7.5 7.5 0 0 1 2.005.412m4 1.522v6m3-3h-6"></path><circle cx="11" cy="6.5" r="4"></circle></g></svg>
          </div>
          <h4>{{ $isRtl ? 'ادعُ صديقك' : 'Invite a friend' }}</h4>
          <p>{{ $isRtl ? '30 ريال لك إذا صديقك سوّى أول طلب' : 'Get SAR 30 when your friend places their first order' }}</p>
        </div>
        <div class="f">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><path d="m13.728 3.444l1.76 3.549c.24.494.88.968 1.42 1.058l3.189.535c2.04.343 2.52 1.835 1.05 3.307l-2.48 2.5c-.42.423-.65 1.24-.52 1.825l.71 3.095c.56 2.45-.73 3.397-2.88 2.117l-2.99-1.785c-.54-.322-1.43-.322-1.98 0L8.019 21.43c-2.14 1.28-3.44.322-2.88-2.117l.71-3.095c.13-.585-.1-1.402-.52-1.825l-2.48-2.5C1.39 10.42 1.86 8.929 3.899 8.586l3.19-.535c.53-.09 1.17-.564 1.41-1.058l1.76-3.549c.96-1.925 2.52-1.925 3.47 0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg>
          </div>
          <h4>{{ $isRtl ? 'نقاط الولاء' : 'Loyalty points' }}</h4>
          <p>{{ $isRtl ? 'كل ريال = نقطة،' : 'Every SAR = 1 pt,' }}<br>{{ $isRtl ? 'وكل 1000 نقطة =' : 'and every 1000 pts =' }}<br><b>{{ $isRtl ? '10 ريال خصم من طلبك' : 'SAR 10 off your order' }}</b></p>
        </div>
        <div class="f">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M21 7v5M3 7v10.161c0 1.383 1.946 2.205 5.837 3.848C10.4 21.67 11.182 22 12 22V11.355M15 19s.875 0 1.75 2c0 0 2.78-5 5.25-6"></path><path d="M8.326 9.691L5.405 8.278C3.802 7.502 3 7.114 3 6.5s.802-1.002 2.405-1.778l2.92-1.413C10.13 2.436 11.03 2 12 2s1.871.436 3.674 1.309l2.921 1.413C20.198 5.498 21 5.886 21 6.5s-.802 1.002-2.405 1.778l-2.92 1.413C13.87 10.564 12.97 11 12 11s-1.871-.436-3.674-1.309M6 12l2 1m9-9L7 9"></path></g></svg>
          </div>
          <h4>{{ $isRtl ? 'تتبع حالة الطلب' : 'Order tracking' }}</h4>
          <p>{{ $isRtl ? 'تعرف وين وصل طلبك خطوة بخطوة، من الاستلام حق التسليم' : 'Track where your order is step by step, from pickup to delivery' }}</p>
        </div>
        <div class="f">
          <div class="ic">
            <svg aria-hidden="true" class="hi" height="28" viewbox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="1.5" cy="1.5" r="1.5" stroke-linecap="round" stroke-linejoin="round" transform="matrix(1 0 0 -1 16 8)"></circle><path d="M2.774 11.144c-1.003 1.12-1.024 2.81-.104 4a34 34 0 0 0 6.186 6.186c1.19.92 2.88.899 4-.104a92 92 0 0 0 8.516-8.698a1.95 1.95 0 0 0 .47-1.094c.164-1.796.503-6.97-.902-8.374s-6.578-1.066-8.374-.901a1.95 1.95 0 0 0-1.094.47a92 92 0 0 0-8.698 8.515Z"></path><path d="m7 14l3 3" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>
          </div>
          <h4>{{ $isRtl ? 'شفافية الأسعار' : 'Price transparency' }}</h4>
          <p>{{ $isRtl ? 'تعرف تكلفة كل قطعة قبل الطلب' : 'See the cost of every item before you order' }}</p>
        </div>
      </div>
      <div class="stores" style="justify-content:center;margin-top:28px">
        <a href="{{ $gPlayLink }}" target="_blank" rel="noopener" class="store-link" aria-label="Google Play">
          <img src="{{ asset($isRtl ? 'assets/store-badges/google-play-ar.png' : 'assets/store-badges/google-play.svg') }}" alt="Google Play" class="store-badge-img">
        </a>
        <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="store-link" aria-label="App Store">
          <img src="{{ asset($isRtl ? 'assets/store-badges/app-store-ar.svg' : 'assets/store-badges/app-store.svg') }}" alt="App Store" class="store-badge-img">
        </a>
      </div>
    </div>
  </section>

  <!-- Customer Reviews -->
  <section class="reviews-section">
    <div class="wrap">
      <span class="kicker">{{ $isRtl ? 'آراء وتجارب' : 'Customer reviews' }}</span>
      <h2>{{ $isRtl ? 'ماذا قال عملاؤنا؟' : 'What our customers say' }}</h2>
      <div class="rgrid" id="reviewsSlider">
        <div class="r">
          <q>{{ $isRtl ? 'نظافة الملابس روعة والريحة تجنن والتطبيق سهل جدا في الطلب وما ياخذ وقت صراحة انصح الكل يجربهم بدون تردد' : 'The clothes come back spotless and smell amazing, and ordering in the app is quick and easy. I recommend them to everyone' }}</q>
          <div class="who">
            <b>{{ $isRtl ? 'سارة ابراهيم' : 'Sarah Ibrahim' }}</b>
            <span class="svc">{{ $isRtl ? 'غسيل وكوي' : 'Wash & iron' }}</span>
          </div>
        </div>
        <div class="r">
          <q>{{ $isRtl ? 'اكثر شي جذبني دقة المواعيد في الاستلام والتسليم وهذا شي نادر بصراحة والمناديب واضح انهم مدربين ومو عشوائيين ابدا في التعامل' : 'What won me over is how punctual pickup and delivery are, which is rare. The drivers are clearly well trained' }}</q>
          <div class="who">
            <b>{{ $isRtl ? 'د. سلطان' : 'Dr. Sultan' }}</b>
            <span class="svc">{{ $isRtl ? 'الاستلام والتسليم' : 'Pickup & delivery' }}</span>
          </div>
        </div>
        <div class="r">
          <q>{{ $isRtl ? 'تعامل احترافي مع الاقمشة الحساسة والقطع الراقية والكوي عندهم فنان صراحة' : 'Professional with delicate fabrics and premium pieces, and their ironing is an art' }}</q>
          <div class="who">
            <b>{{ $isRtl ? 'هند علي' : 'Hind Ali' }}</b>
            <span class="svc">{{ $isRtl ? 'دراي كلين' : 'Dry cleaning' }}</span>
          </div>
        </div>
        <div class="r">
          <q>{{ $isRtl ? 'ريحة البطانيات تجنن وتفتح النفس' : 'The blankets smell wonderful' }}</q>
          <div class="who">
            <b>{{ $isRtl ? 'لولوة' : 'Lulwa' }}</b>
            <span class="svc">{{ $isRtl ? 'مفروشات' : 'Bedding' }}</span>
          </div>
        </div>
        <div class="r">
          <q>{{ $isRtl ? 'خدمة تبيض الوجه والمناديب مو عشوائيين في استلام الملابس وهذا اللي خلاني اثق فيهم' : "Excellent service, and the drivers are careful when collecting clothes. That's what made me trust them" }}</q>
          <div class="who">
            <b>{{ $isRtl ? 'مساعد خالد' : 'Musaed Khalid' }}</b>
            <span class="svc">{{ $isRtl ? 'غسيل وكوي' : 'Wash & iron' }}</span>
          </div>
        </div>
        <div class="r">
          <q>{{ $isRtl ? 'الشغل مرتب ونظفوا الاحذية بشكل ممتاز بس تمنيت لو كان فيه خيارات اكثر لتغليف الاحذية بدل الاكياس البلاستيك العادية لكن بشكل عام جودة التنظيف ممتازة' : "Neat work and the shoes came out great. I'd like more packaging options than plain plastic bags, but overall the cleaning quality is excellent" }}</q>
          <div class="who">
            <b>{{ $isRtl ? 'ماجد' : 'Majed' }}</b>
            <span class="svc">{{ $isRtl ? 'أحذية' : 'Shoes' }}</span>
          </div>
        </div>
      </div>
      <div class="reviews-dots" id="reviewsDots"></div>
    </div>
  </section>

  <!-- FAQ & Final Call to Action -->
  <section id="faq">
    <div class="wrap">
      <h2 style="text-align:center">{{ $isRtl ? 'قبل ما تطلب' : 'Before you order' }}</h2>
      <div class="faq">
        @if($isRtl)
          <details open>
            <summary>كيف أطلب؟</summary>
            <p>الطلب من تطبيق كلين ستيشن فقط: طلب سريع بنقرة واحدة تحدد فيه الموقع والوقت، أو طلب مفصل بالقطعة، أو حقيبة اقتصادية توفّر عليك</p>
          </details>
          <details>
            <summary>متى يرجع غسيلي؟</summary>
            <p>حسب الوقت اللي تختاره في التطبيق للاستلام والتسليم، ونجيك في وقتك، وأغلب المواعيد المتاحة خلال 21 إلى 24 ساعة</p>
          </details>
          <details>
            <summary>هل يختلف السعر بين الطلب السريع والمفصل؟</summary>
            <p>لا، نفس الأسعار المعلنة في التطبيق، والطلب السريع يُحسب بنفس أسعار القطع</p>
          </details>
          <details>
            <summary>متى يكون التوصيل مجاني؟</summary>
            <p>للطلبات بـ{{ $freeDeliveryMin }} ريال وما فوق</p>
          </details>
          <details>
            <summary>لازم أكون موجود وقت الاستلام؟</summary>
            <p>لا، تقدر تسلّم المندوب بنفسك، أو تعلّق الكيس على الباب ويستلمه بدون ما يزعجك</p>
          </details>
          <details>
            <summary>أقدر أطلب عن طريق الواتساب؟</summary>
            <p>لا، الطلب من التطبيق فقط، والواتساب للتواصل مع خدمة العملاء وللملاحظات والاستفسارات</p>
          </details>
        @else
          <details open>
            <summary>How do I order?</summary>
            <p>Orders are placed in the Clean Station app only: a one-tap quick order where you set your location and time, a detailed order by item, or a value laundry bag that saves you more</p>
          </details>
          <details>
            <summary>When will my laundry come back?</summary>
            <p>At the pickup and delivery times you choose in the app, and we come on your schedule, with most available slots within 21 to 24 hours</p>
          </details>
          <details>
            <summary>Do quick and detailed orders cost the same?</summary>
            <p>Yes, both use the same prices listed in the app, and quick orders are charged at the same item prices</p>
          </details>
          <details>
            <summary>When is delivery free?</summary>
            <p>On orders of SAR {{ $freeDeliveryMin }} and above</p>
          </details>
          <details>
            <summary>Do I need to be home for pickup?</summary>
            <p>No, you can hand the bag to the driver, or hang it on your door and we'll collect it without disturbing you</p>
          </details>
          <details>
            <summary>Can I order on WhatsApp?</summary>
            <p>No, orders are placed in the app only, and WhatsApp is for customer service, feedback and enquiries</p>
          </details>
        @endif
      </div>

      <!-- Final Banner -->
      <div class="final">
        <h2>{{ $isRtl ? 'سلّمنا غسيلك… ويرجع لك في وقتك' : 'Hand us your laundry, get it back on your time' }}</h2>
        <div class="pts">
          <span>{{ $isRtl ? 'تختار وقت الاستلام والتسليم' : 'You choose pickup and delivery times' }}</span>
          <span>{{ $isRtl ? 'طلب كل عميل يُغسل لحاله' : "Every customer's order washed separately" }}</span>
          <span>{{ $isRtl ? "توصيل مجاني للطلبات بـ{$freeDeliveryMin} ريال وما فوق" : "Free delivery on orders of SAR {$freeDeliveryMin} and above" }}</span>
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
          <a href="https://wa.me/{{ $whatsappPhone }}?text={{ urlencode($isRtl ? 'استفسار عن خدمة كلين ستيشن' : 'Enquiry about Clean Station services') }}" target="_blank" rel="noopener">
            {{ $isRtl ? 'تواصل مع خدمة العملاء عبر واتساب' : 'Contact customer service on WhatsApp' }}
          </a>
        </p>
      </div>
    </div>
  </section>




  <!-- Original Home Page Footer -->
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

  <!-- Reviews Slider Dots Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var slider = document.getElementById('reviewsSlider');
      var dotsContainer = document.getElementById('reviewsDots');
      if (slider && dotsContainer) {
        var cards = slider.querySelectorAll('.r');
        dotsContainer.innerHTML = '';
        cards.forEach(function(card, index) {
          var dot = document.createElement('span');
          dot.className = 'dot' + (index === 0 ? ' active' : '');
          dot.addEventListener('click', function() {
            card.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
          });
          dotsContainer.appendChild(dot);
        });

        var scrollTimer;
        slider.addEventListener('scroll', function() {
          clearTimeout(scrollTimer);
          scrollTimer = setTimeout(function() {
            var sliderRect = slider.getBoundingClientRect();
            var sliderCenter = sliderRect.left + sliderRect.width / 2;
            var closestIndex = 0;
            var minDiff = Infinity;
            cards.forEach(function(card, i) {
              var cardRect = card.getBoundingClientRect();
              var cardCenter = cardRect.left + cardRect.width / 2;
              var diff = Math.abs(sliderCenter - cardCenter);
              if (diff < minDiff) {
                minDiff = diff;
                closestIndex = i;
              }
            });
            var dots = dotsContainer.querySelectorAll('.dot');
            dots.forEach(function(d, idx) {
              d.classList.toggle('active', idx === closestIndex);
            });
          }, 40);
        }, { passive: true });
      }
    });
  </script>

</body>
</html>

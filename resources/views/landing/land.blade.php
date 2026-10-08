@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
    use Illuminate\Support\Str;

    $lang = LaravelLocalization::getCurrentLocale() ?: app()->getLocale() ?: 'ar';
    $isRtl = ($lang === 'ar');
    $dir = LaravelLocalization::getCurrentLocaleDirection() ?: ($isRtl ? 'rtl' : 'ltr');
    $otherLocale = $isRtl ? 'en' : 'ar';
    $otherLocaleText = $isRtl ? 'English' : 'العربية';
    $otherLocaleUrl = LaravelLocalization::getLocalizedURL($otherLocale, route('land', [], false), [], true);

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
@media (max-width:600px){
  #navbar img.h-10,#navbar .h-10,#navbar a[href*="home"] img{height:48px !important;max-height:48px !important}
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

/* Hero Section */
.hero{background:linear-gradient(160deg,#EEF7FC 0%,#fff 60%);padding:130px 0 56px}
.hero-grid{display:grid;grid-template-columns:1.15fr .85fr;gap:40px;align-items:center}
.hero .lead{font-size:17px;color:var(--ink);margin-top:14px;max-width:540px;line-height:1.7}
.facts{display:flex;flex-wrap:wrap;gap:8px 22px;margin-top:20px;max-width:560px}
.facts span{font-size:13px;color:var(--ink);display:inline-flex;align-items:center;gap:6px;font-weight:500}
.facts span::before{content:"✓";color:var(--green);font-weight:700}

.stores{display:flex;gap:10px;margin-top:24px;flex-wrap:wrap}
.store{display:inline-flex;align-items:center;background:#000;color:#fff;border-radius:10px;padding:8px 14px;min-width:140px;box-shadow:0 3px 10px rgba(0,0,0,0.08);transition:transform 0.2s}
.store:hover{transform:translateY(-2px)}
.store .sb{display:flex;align-items:center;gap:10px}
.store svg{width:22px;height:22px;flex-shrink:0}
.store .t{display:flex;flex-direction:column;line-height:1.2}
.store .t small{font-size:9.5px;opacity:.8;font-weight:500}
.store .t b{font-size:14px;font-weight:700}

.proof{display:flex;gap:28px;margin-top:24px;border-top:1px solid var(--line);padding-top:18px}
.proof b{font-size:22px;color:var(--navy);display:block;line-height:1.2}
.proof span{font-size:12px;color:var(--muted)}

.hero-img{display:flex;justify-content:center}
.hero-img img{max-width:320px;width:100%;height:auto;border-radius:24px;box-shadow:0 10px 30px rgba(31,51,100,0.1);background:#fff}

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
.why-section{background:var(--bg-2)}
.why-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:22px}
.why{border:1px solid var(--line);border-radius:18px;padding:18px 20px;background:#fff}
.why .ic{width:38px;height:38px;border-radius:10px;background:var(--sky-soft);display:grid;place-items:center;font-size:18px;margin-bottom:10px}
.why h4{color:var(--navy);font-size:15px;font-weight:600}
.why p{color:var(--muted);font-size:13px;margin-top:4px}
.chips{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}
.chips span{font-size:12px;background:var(--bg-2);color:var(--ink);padding:2px 10px;border-radius:999px;white-space:nowrap}

/* Journey */
.journey-section{background:#fff}
.jgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:22px}
.j{background:#fff;border-radius:18px;padding:18px 20px;border:1px solid var(--line)}
.j h4{color:var(--navy);font-size:15px;font-weight:600}
.j p{color:var(--muted);font-size:14px;margin-top:4px}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:24px}
.stats div{text-align:center;background:var(--navy);color:#fff;border-radius:16px;padding:16px 8px}
.stats b{font-size:22px;display:block;line-height:1.3}
.stats span{font-size:12px;opacity:.85}

/* App Features */
.app-section{background:#fff;padding:60px 0;border-top:1px solid var(--line)}
.app-section .kicker{display:inline-block;text-align:center;margin:0 auto 10px;width:fit-content;background:var(--sky-soft);color:var(--blue);border-radius:999px;padding:4px 14px;font-size:12.5px;font-weight:600}
.app-section h2{text-align:center;font-size:32px;font-weight:800;color:var(--navy);margin:0 0 10px}
.feat-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-top:26px}
.f{background:#fff;border-radius:18px;padding:24px 14px 22px;border:1px solid var(--line);min-height:180px;display:flex;flex-direction:column;align-items:center;text-align:center;transition:transform .2s,box-shadow .2s;box-shadow:0 1px 3px rgba(0,0,0,0.03)}
.f:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,0.06)}
.f .ic{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;font-size:20px;margin:0 auto 14px}
.f.ic-wallet .ic{background:#FDE7EE}
.f.ic-friend .ic{background:#FFF1EC}
.f.ic-loyalty .ic{background:#FEF9E7}
.f.ic-track .ic{background:#FDE7EE}
.f.ic-pricing .ic{background:#EAF6FB}
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

/* Brand Band (Navy Pill Banner) */
.brand-band-section{background:#fff;padding:32px 0 44px}
.brand-band{background:var(--navy);border-radius:24px;padding:26px 44px;display:flex;align-items:center;justify-content:center;gap:26px;box-shadow:0 8px 28px rgba(31,51,100,0.15)}
.brand-band img{height:72px;width:auto;object-fit:contain;flex-shrink:0}
.brand-band .bb-text{text-align:start}
.brand-band p{color:#FFFFFF !important;font-size:24px !important;font-weight:800 !important;line-height:1.3 !important;margin:0 !important;letter-spacing:-0.01em}
.brand-band small{display:block !important;color:rgba(255,255,255,0.9) !important;font-size:16px !important;font-weight:500 !important;margin-top:6px !important;line-height:1.4 !important}

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
.final{text-align:center;padding:48px 24px;background:linear-gradient(160deg,#EAF6FB 0%,#fff 80%);border-radius:24px;border:1px solid var(--line);margin-top:40px}
.final .pts{display:flex;gap:8px 22px;justify-content:center;margin:14px 0 22px;flex-wrap:wrap}
.final .pts span{font-size:13px;color:var(--ink);display:inline-flex;align-items:center;gap:6px;font-weight:500}
.final .pts span::before{content:"✓";color:var(--green);font-weight:700}
.final .contact-line{margin-top:16px;font-size:13px;color:var(--muted)}
.final .contact-line a{color:var(--wa);font-weight:600}

/* Gift Strip */
.gift-strip{display:flex;justify-content:space-between;align-items:center;gap:12px;background:#FFF0F4;border:1px solid #FAD1DC;border-radius:14px;padding:12px 20px}
.gift-strip p{color:#8A2846;font-size:14px;font-weight:600}

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

/* Mobile Sticky Bar */
.sticky{display:none;position:fixed;bottom:0;inset-inline:0;background:#fff;padding:10px 16px;border-top:1px solid var(--line);gap:8px;z-index:40;box-shadow:0 -4px 12px rgba(0,0,0,0.05)}

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
  .hero{padding:110px 0 44px}
  h1{font-size:32px}
  h2{font-size:24px}
  .bags{grid-template-columns:1fr}
  .why-grid{grid-template-columns:1fr}
  .jgrid{grid-template-columns:1fr}
  .feat-grid{grid-template-columns:repeat(2,1fr);gap:10px}
  .feat-grid .f:last-child{grid-column:span 2}
  .rgrid{grid-template-columns:1fr}
  .gift-strip{flex-direction:column;text-align:center}
  .brand-band{flex-direction:column;text-align:center;padding:20px 20px;gap:14px}
  .brand-band .bb-text{text-align:center}
  .brand-band p{font-size:20px !important}
  .brand-band small{font-size:14.5px !important}
  .brand-band img{height:58px}
  .fgrid{grid-template-columns:1fr}
  .sticky{display:flex}
  body{padding-bottom:60px}
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

        <!-- App Store & Google Play Badges -->
        <div class="stores">
          <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="store" aria-label="App Store">
            <span class="sb">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/></svg>
              <span class="t"><small>{{ $isRtl ? 'حمّله من' : 'Download on the' }}</small><b>App Store</b></span>
            </span>
          </a>
          <a href="{{ $gPlayLink }}" target="_blank" rel="noopener" class="store" aria-label="Google Play">
            <span class="sb">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#00C4FF" d="M1.337.924a1.486 1.486 0 0 0-.112.568v21.017c0 .217.045.419.124.6l12.195-12.12z"/><path fill="#00E676" d="M13.544 10.989l3.258-3.238L3.45.195a1.466 1.466 0 0 0-.946-.179z"/><path fill="#FFC400" d="M22.018 13.298l-3.919 2.218-3.515-3.493 3.543-3.521 3.891 2.202a1.49 1.49 0 0 1 0 2.594z"/><path fill="#FF3A44" d="M13.544 13.056l-11 10.933c.298.036.612-.016.906-.183l13.324-7.54z"/></svg>
              <span class="t"><small>{{ $isRtl ? 'احصل عليه من' : 'GET IT ON' }}</small><b>Google Play</b></span>
            </span>
          </a>
        </div>

        <!-- Social Proof Stats -->
        <div class="proof">
          <div>
            <b>{{ $isRtl ? '+15 ألف' : '15K+' }}</b>
            <span>{{ $isRtl ? 'تحميل للتطبيق' : 'App downloads' }}</span>
          </div>
          <div>
            <b>24/7</b>
            <span>{{ $isRtl ? 'خدمة عملاء' : 'Customer service' }}</span>
          </div>
        </div>
      </div>

      <!-- Hero App Preview (Exact Backend Image from Home Page) -->
      <div class="hero-img">
        @if(!empty($heroImageUrl))
          <img src="{{ $heroImageUrl }}" alt="{{ $isRtl ? 'تطبيق كلين ستيشن' : 'Clean Station App' }}">
        @else
          <img src="{{ asset('assets/logo-main.png') }}" alt="Clean Station App">
        @endif
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
      <span class="kicker">{{ $isRtl ? 'ليش كلين ستيشن؟' : 'Why Clean Station?' }}</span>
      <h2>{{ $isRtl ? 'الكل يغسل… والفرق بالجودة والاهتمام' : 'Everyone washes. We care more' }}</h2>
      <div class="why-grid">
        <div class="why">
          <div class="ic">🧺</div>
          <h4>{{ $isRtl ? 'طلبك يُغسل لحاله' : 'Washed on its own' }}</h4>
          <p>{{ $isRtl ? 'طلب كل عميل يُغسل لحاله، وما يختلط مع غيره' : "Every customer's order is washed separately, never mixed with others" }}</p>
        </div>
        <div class="why">
          <div class="ic">✨</div>
          <h4>{{ $isRtl ? 'تفضيلاتك عند الطلب' : 'Your preferences' }}</h4>
          <p>{{ $isRtl ? 'تحدد اللي تحبه، ونطبقه على كل طلب' : 'Choose what you like when you order, and we apply it every time' }}</p>
          <div class="chips">
            <span>{{ $isRtl ? 'النشا' : 'Starch' }}</span>
            <span>{{ $isRtl ? 'معطّرنا الخاص' : 'Signature fragrance' }}</span>
          </div>
        </div>
        <div class="why">
          <div class="ic">🔍</div>
          <h4>{{ $isRtl ? 'فحص قبل التسليم' : 'Quality checked' }}</h4>
          <p>{{ $isRtl ? 'كي بالبخار، وفحص كل قطعة قبل التغليف' : 'Steam ironing and a check of every item before packing' }}</p>
        </div>
        <div class="why">
          <div class="ic">🏠</div>
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
  <section class="journey-section" id="services">
    <div class="wrap">
      <span class="kicker">{{ $isRtl ? 'رحلة العناية بغسيلك' : 'Our care journey' }}</span>
      <h2>{{ $isRtl ? 'من بابك إلى بابك، خطوة بخطوة' : 'Door to door, step by step' }}</h2>
      <div class="jgrid">
        <div class="j">
          <h4>{{ $isRtl ? '1 · الطلب' : '1 · Order' }}</h4>
          <p>{{ $isRtl ? 'طلب سريع بنقرة واحدة أو مفصل بالقطعة' : 'Quick one-tap or detailed item order' }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '2 · التجهيز' : '2 · Prepare' }}</h4>
          <p>{{ $isRtl ? 'حط غسيلك في أي كيس متوفر عندك' : 'Put your laundry in any bag you have' }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '3 · الاستلام' : '3 · Pickup' }}</h4>
          <p>{{ $isRtl ? 'مندوبنا الرسمي يستلم في الوقت اللي حددته' : 'Our driver collects it at the time you chose' }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '4 · الغسيل والمعالجة' : '4 · Washing' }}</h4>
          <p>{{ $isRtl ? 'غسيل منفصل 100%، وطلب كل عميل يُغسل لحاله' : "100% separate washing, every customer's order on its own" }}</p>
        </div>
        <div class="j">
          <h4>{{ $isRtl ? '5 · الكوي والفرز' : '5 · Ironing & sorting' }}</h4>
          <p>{{ $isRtl ? 'كي بالبخار، وفحص الجودة، وتغليف مرتب' : 'Steam ironing, quality check and neat packing' }}</p>
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
      <span class="kicker">{{ $isRtl ? 'تطبيق كلين ستيشن' : 'Clean Station app' }}</span>
      <h2>{{ $isRtl ? 'كل شي من جوالك' : 'Everything from your phone' }}</h2>
      <div class="feat-grid">
        <div class="f ic-wallet">
          <div class="ic">👛</div>
          <h4>{{ $isRtl ? 'المحفظة الذكية' : 'Smart wallet' }}</h4>
          <p>
            @if($isRtl) ادفع 440 ريال واحصل على رصيد 500، <b style="color:var(--green)">وفّر حق 13%</b>
            @else Pay SAR 440 and get SAR 500 credit, <b style="color:var(--green)">save up to 13%</b> @endif
          </p>
        </div>
        <div class="f ic-friend">
          <div class="ic">🎁</div>
          <h4>{{ $isRtl ? 'ادعُ صديقك' : 'Invite a friend' }}</h4>
          <p>{{ $isRtl ? '30 ريال لك إذا صديقك سوّى أول طلب' : 'Get SAR 30 when your friend places their first order' }}</p>
        </div>
        <div class="f ic-loyalty">
          <div class="ic">⭐</div>
          <h4>{{ $isRtl ? 'نقاط الولاء' : 'Loyalty points' }}</h4>
          <p>{{ $isRtl ? 'كل ريال = نقطة، واستبدلها بخدمات مجانية' : 'Every riyal earns a point you can redeem for free services' }}</p>
        </div>
        <div class="f ic-track">
          <div class="ic">📍</div>
          <h4>{{ $isRtl ? 'تتبع حالة الطلب' : 'Order status' }}</h4>
          <p>{{ $isRtl ? 'تعرف وين وصل طلبك خطوة بخطوة، من الاستلام حق التسليم' : 'Track where your order is step by step, from pickup to delivery' }}</p>
        </div>
        <div class="f ic-pricing">
          <div class="ic">🏷️</div>
          <h4>{{ $isRtl ? 'شفافية الأسعار' : 'Transparent pricing' }}</h4>
          <p>{{ $isRtl ? 'تعرف تكلفة كل قطعة قبل الطلب' : 'See the cost of every item before you order' }}</p>
        </div>
      </div>
      <div class="stores" style="justify-content:center;margin-top:28px">
        <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="store" aria-label="App Store">
          <span class="sb">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/></svg>
            <span class="t"><small>{{ $isRtl ? 'حمّله من' : 'Download on the' }}</small><b>App Store</b></span>
          </span>
        </a>
        <a href="{{ $gPlayLink }}" target="_blank" rel="noopener" class="store" aria-label="Google Play">
          <span class="sb">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#00C4FF" d="M1.337.924a1.486 1.486 0 0 0-.112.568v21.017c0 .217.045.419.124.6l12.195-12.12z"/><path fill="#00E676" d="M13.544 10.989l3.258-3.238L3.45.195a1.466 1.466 0 0 0-.946-.179z"/><path fill="#FFC400" d="M22.018 13.298l-3.919 2.218-3.515-3.493 3.543-3.521 3.891 2.202a1.49 1.49 0 0 1 0 2.594z"/><path fill="#FF3A44" d="M13.544 13.056l-11 10.933c.298.036.612-.016.906-.183l13.324-7.54z"/></svg>
            <span class="t"><small>{{ $isRtl ? 'احصل عليه من' : 'GET IT ON' }}</small><b>Google Play</b></span>
          </span>
        </a>
      </div>
    </div>
  </section>

  <!-- Customer Reviews -->
  <section class="reviews-section">
    <div class="wrap">
      <span class="kicker">{{ $isRtl ? 'آراء وتجارب' : 'Customer reviews' }}</span>
      <h2>{{ $isRtl ? 'ماذا قال عملاؤنا؟' : 'What our customers say' }}</h2>
      <div class="rgrid">
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
          <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="store" aria-label="App Store">
            <span class="sb">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/></svg>
              <span class="t"><small>{{ $isRtl ? 'حمّله من' : 'Download on the' }}</small><b>App Store</b></span>
            </span>
          </a>
          <a href="{{ $gPlayLink }}" target="_blank" rel="noopener" class="store" aria-label="Google Play">
            <span class="sb">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#00C4FF" d="M1.337.924a1.486 1.486 0 0 0-.112.568v21.017c0 .217.045.419.124.6l12.195-12.12z"/><path fill="#00E676" d="M13.544 10.989l3.258-3.238L3.45.195a1.466 1.466 0 0 0-.946-.179z"/><path fill="#FFC400" d="M22.018 13.298l-3.919 2.218-3.515-3.493 3.543-3.521 3.891 2.202a1.49 1.49 0 0 1 0 2.594z"/><path fill="#FF3A44" d="M13.544 13.056l-11 10.933c.298.036.612-.016.906-.183l13.324-7.54z"/></svg>
              <span class="t"><small>{{ $isRtl ? 'احصل عليه من' : 'GET IT ON' }}</small><b>Google Play</b></span>
            </span>
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

  <!-- Flower & Gifts Strip -->
  <section class="gift-strip-section">
    <div class="wrap">
      <div class="gift-strip">
        <p>{{ $isRtl ? '✨ جديد: باقات ورود وفازات فاخرة بتوصيل كلين ستيشن' : '✨ New: flower bouquets and luxury vases delivered by Clean Station' }}</p>
        <a href="{{ route('gifts') }}" class="btn btn-o" style="color:#A33A5B;border-color:#F5DDE3">
          {{ $isRtl ? 'تصفح الهدايا ←' : 'Browse gifts →' }}
        </a>
      </div>
    </div>
  </section>

  <!-- Brand Band (Navy Banner) -->
  <section class="brand-band-section">
    <div class="wrap">
      <div class="brand-band">
        <img src="{{ asset('assets/clean-station-badge.png') }}" alt="Clean Station - كلين ستيشن">
        <div class="bb-text">
          <p>{{ $isRtl ? 'تطبيق كلين ستيشن' : 'Clean Station app' }}</p>
          <small>{{ $isRtl ? 'منصة سعودية للعناية بالغسيل' : 'A Saudi laundry care platform' }}</small>
        </div>
      </div>
    </div>
  </section>

  <!-- Original Home Page Footer -->
  @include('layouts.partials.footer')

  <!-- Sticky Mobile Bar -->
  <div class="sticky">
    <a href="https://wa.me/{{ $whatsappPhone }}?text={{ urlencode($isRtl ? 'أبغى أطلب غسيل' : 'Hello, I want to request laundry service') }}" target="_blank" rel="noopener" class="btn btn-o">
      {{ $isRtl ? 'تواصل معنا' : 'Contact us' }}
    </a>
    <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="btn btn-p">
      {{ $isRtl ? 'حمّل التطبيق' : 'Download the app' }}
    </a>
  </div>

</body>
</html>

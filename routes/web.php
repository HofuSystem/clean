<?php

use App\Http\Controllers\Client\AddressController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PageController;
use Core\Orders\Services\OrderTransactionsService;
use Core\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Client\ScheduleController;
use Carbon\Carbon;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderTransaction;
use Core\Settings\Helpers\ToolHelper;

// ============================================================
// Legacy 301 Redirects — CS-01 / CS-05
// One-hop redirects; no chain through localization middleware.
// ============================================================

// .html extension removals
Route::get('/ar/services.html',            fn() => redirect('https://cleanstation.app/ar/services', 301));
Route::get('/en/services.html',            fn() => redirect('https://cleanstation.app/en/services', 301));
Route::get('/ar/blogs/{slug}.html',        fn($slug) => redirect('https://cleanstation.app/ar/blogs/' . $slug, 301))->where('slug', '[^/]+');
Route::get('/en/blogs/{slug}.html',        fn($slug) => redirect('https://cleanstation.app/en/blogs/' . $slug, 301))->where('slug', '[^/]+');

// Old service slugs → canonical service slugs
Route::get('/ar/services/mens-laundry',         fn() => redirect('https://cleanstation.app/ar/services/wash-and-iron', 301));
Route::get('/en/services/mens-laundry',         fn() => redirect('https://cleanstation.app/en/services/wash-and-iron', 301));
Route::get('/ar/services/womens-laundry',       fn() => redirect('https://cleanstation.app/ar/services/wash-and-iron', 301));
Route::get('/en/services/womens-laundry',       fn() => redirect('https://cleanstation.app/en/services/wash-and-iron', 301));
Route::get('/ar/services/carpets-furnishings',  fn() => redirect('https://cleanstation.app/ar/services/carpet-upholstery-cleaning', 301));
Route::get('/en/services/carpets-furnishings',  fn() => redirect('https://cleanstation.app/en/services/carpet-upholstery-cleaning', 301));
Route::get('/ar/services/medical-military',     fn() => redirect('https://cleanstation.app/ar/b2b', 301));
Route::get('/en/services/medical-military',     fn() => redirect('https://cleanstation.app/en/b2b', 301));

// Old blog slugs → canonical blog slugs
// Blog 1
Route::redirect('/ar/blogs/the-sparkle-you-deserve-why-a-professional-cleaning-service-is-a-game-changer', '/ar/blogs/laundry-pickup-delivery-riyadh', 301);
Route::redirect('/en/blogs/the-sparkle-you-deserve-why-a-professional-cleaning-service-is-a-game-changer', '/en/blogs/laundry-pickup-delivery-riyadh', 301);
Route::redirect('/blogs/the-sparkle-you-deserve-why-a-professional-cleaning-service-is-a-game-changer', '/ar/blogs/laundry-pickup-delivery-riyadh', 301);

// Blog 13
Route::redirect('/ar/blogs/best-underground-water-tank-cleaning-company', '/ar/blogs/laundry-fabric-care-prices-riyadh', 301);
Route::redirect('/en/blogs/best-underground-water-tank-cleaning-company', '/en/blogs/laundry-fabric-care-prices-riyadh', 301);
Route::redirect('/blogs/best-underground-water-tank-cleaning-company', '/ar/blogs/laundry-fabric-care-prices-riyadh', 301);

Route::get('/ar/blogs/termite-control-saudi-arabia',                fn() => redirect('https://cleanstation.app/ar/blogs/clean-station-app-features', 301));
Route::get('/en/blogs/termite-control-saudi-arabia',                fn() => redirect('https://cleanstation.app/en/blogs/clean-station-app-features', 301));
Route::get('/blogs/termite-control-saudi-arabia',                   fn() => redirect('https://cleanstation.app/ar/blogs/clean-station-app-features', 301));

// Blog 10
Route::redirect('/ar/blogs/air-conditioner-cleaning-importance', '/ar/blogs/on-time-laundry-pickup-delivery-riyadh', 301);
Route::redirect('/en/blogs/air-conditioner-cleaning-importance', '/en/blogs/on-time-laundry-pickup-delivery-riyadh', 301);
Route::redirect('/blogs/air-conditioner-cleaning-importance', '/ar/blogs/on-time-laundry-pickup-delivery-riyadh', 301);

// Intermediate slugs (prevent 404 for interim visits)
Route::redirect('/ar/blogs/pickup-delivery-on-time', '/ar/blogs/on-time-laundry-pickup-delivery-riyadh', 301);
Route::redirect('/en/blogs/pickup-delivery-on-time', '/en/blogs/on-time-laundry-pickup-delivery-riyadh', 301);
Route::redirect('/blogs/pickup-delivery-on-time', '/ar/blogs/on-time-laundry-pickup-delivery-riyadh', 301);

Route::redirect('/ar/blogs/laundry-prices-riyadh', '/ar/blogs/laundry-fabric-care-prices-riyadh', 301);
Route::redirect('/en/blogs/laundry-prices-riyadh', '/en/blogs/laundry-fabric-care-prices-riyadh', 301);
Route::redirect('/blogs/laundry-prices-riyadh', '/ar/blogs/laundry-fabric-care-prices-riyadh', 301);

// /about-us → /why-us
Route::get('/ar/about-us', fn() => redirect('https://cleanstation.app/ar/why-us', 301));
Route::get('/en/about-us', fn() => redirect('https://cleanstation.app/en/why-us', 301));

// ============================================================

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {

        Route::get('', [PageController::class, 'home'])->name('home');
        Route::get('/b2b', [PageController::class, 'b2b'])->name('b2b');
        Route::get('/blogs', [PageController::class, 'blog'])->name('blog');
        Route::get('/blogs/{slug}', [PageController::class, 'blogPost'])->name('blogs-single');
        Route::get('/services', [PageController::class, 'services'])->name('services');
        Route::get('/services/{slug}', [PageController::class, 'servicePost'])->name('services.single');
        Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
        Route::get('/riyadh', [PageController::class, 'coverage'])->name('coverage');
        Route::get('/contact-us', [PageController::class, 'contactUs'])->name('contact');
        Route::post('/contact-us', [PageController::class, 'contactUsRequest'])->name('contact');
        Route::get('/faq', [PageController::class, 'faq'])->name('faq');
        Route::get('/why-us', [PageController::class, 'whyUs'])->name('why-us');
        Route::get('/app-features', [PageController::class, 'appFeatures'])->name('app.features');
        Route::get('/testimonials', [PageController::class, 'testimonials'])->name('testimonials');


        Route::get('/terms', [PageController::class, 'terms'])->name('terms');
        Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
        Route::get('/payment-gateway', [PageController::class, 'paymentGateway'])->name('payment-gateway');
        Route::view('/social', 'social')->name('social');
        Route::view('/allInfo', 'allInfo')->name('allInfo');
        Route::get('sitemap.xml', [PageController::class, 'siteMap']);
        Route::post('/newsletter', function () {
            $data = request()->validate([
                'email' => 'required|email|max:255',
            ]);
            DB::table('news_letters')
                ->updateOrInsert(
                    ['email' => $data['email']],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            return redirect()->back()->with('success', trans('Thank you for subscribing to our newsletter'));
        })->name('newsletter');

        // New Landing Page (Localized)
        Route::get('/land', [PageController::class, 'land'])->name('land');

        // Flowers & Gifts Page (الورود والهدايا)
        Route::get('/gifts', [PageController::class, 'gifts'])->name('gifts');
    }
);

// ============================================================
// New Landing Page Design Preview (Friendly Aliases)
// ============================================================
Route::get('/land/ar', function () {
    return redirect(LaravelLocalization::getLocalizedURL('ar', '/land', [], true));
});

Route::get('/land/en', function () {
    return redirect(LaravelLocalization::getLocalizedURL('en', '/land', [], true));
});

Route::get('/gifts/ar', function () {
    return redirect(LaravelLocalization::getLocalizedURL('ar', '/gifts', [], true));
});

Route::get('/gifts/en', function () {
    return redirect(LaravelLocalization::getLocalizedURL('en', '/gifts', [], true));
});

Route::get('/cleanstation-site-3.html', function () {
    return redirect(LaravelLocalization::getLocalizedURL(app()->getLocale() ?: 'ar', '/gifts', [], true));
});

Route::get('/cleanstation-site-3', function () {
    return redirect(LaravelLocalization::getLocalizedURL(app()->getLocale() ?: 'ar', '/gifts', [], true));
});

Route::get('/cleanstation-homepage-edits.html', function () {
    return redirect(LaravelLocalization::getLocalizedURL('ar', '/land', [], true));
});

Route::get('/cleanstation-homepage-en.html', function () {
    return redirect(LaravelLocalization::getLocalizedURL('en', '/land', [], true));
});

Route::get('/api-docs', function () {
    $username = env('API_DOCS_USERNAME', 'admin');
    $password = env('API_DOCS_PASSWORD', 'cleanstation');

    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== $username || $_SERVER['PHP_AUTH_PW'] !== $password) {
        header('WWW-Authenticate: Basic realm="CleanStation API Docs"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You are not authorized to view the API documentation.';
        exit;
    }
    return view('api-docs');
})->name('api-docs');

Route::get('/docs.openapi', function () {
    $username = env('API_DOCS_USERNAME', 'admin');
    $password = env('API_DOCS_PASSWORD', 'cleanstation');

    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== $username || $_SERVER['PHP_AUTH_PW'] !== $password) {
        header('WWW-Authenticate: Basic realm="CleanStation API Docs"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You are not authorized to view the API documentation.';
        exit;
    }

    $path = storage_path('app/private/scribe/openapi.yaml');
    if (!file_exists($path)) {
        abort(404, 'OpenAPI spec not found.');
    }

    $content = file_get_contents($path);
    $appUrl = config('app.url');
    // Replace the hardcoded server URL dynamically
    $content = preg_replace('/(servers:\s*-\s*url:\s*)[\'"].*?[\'"]/', '$1\'' . $appUrl . '\'', $content);
    // Fallback replace any other instances
    $content = str_replace('http://localhost:8000', $appUrl, $content);

    return response($content, 200, [
        'Content-Type' => 'text/yaml'
    ]);
})->name('docs.openapi');

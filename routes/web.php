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

Route::group(
[
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
],
        function () {

        Route::get('', [PageController::class , 'home'])->name('home');
        Route::get('/b2b', [PageController::class , 'b2b'])->name('b2b');
        Route::get('/blogs', [PageController::class , 'blog'])->name('blog');
        Route::get('/blogs/{slug}', [PageController::class , 'blogPost'])->name('blogs-single');
        Route::get('/services', [PageController::class , 'services'])->name('services');
        Route::get('/contact-us', [PageController::class , 'contactUs'])->name('contact');
        Route::post('/contact-us', [PageController::class , 'contactUsRequest'])->name('contact');
        Route::get('/faq', [PageController::class , 'faq'])->name('faq');
        Route::get('/why-us', [PageController::class , 'whyUs'])->name('why-us');
        Route::get('/app-features', [PageController::class , 'appFeatures'])->name('app.features');
        Route::get('/testimonials', [PageController::class , 'testimonials'])->name('testimonials');


        Route::get('/terms', [PageController::class , 'terms'])->name('terms');
        Route::get('/privacy', [PageController::class , 'privacy'])->name('privacy');
        Route::get('/payment-gateway', [PageController::class , 'paymentGateway'])->name('payment-gateway');
        Route::view('/social', 'social')->name('social');
        Route::view('/allInfo', 'allInfo')->name('allInfo');
        Route::get('sitemap.xml', [PageController::class , 'siteMap']);
        Route::post('/newsletter', function () {
            DB::table('news_letters')
                ->updateOrInsert(
            ['email' => request('email')],
            ['updated_at' => now(), 'created_at' => now()]
            );
            return redirect()->back()->with('success', 'Thank you for subscribing to our newsletter');
        }
        )->name('newsletter');



    });
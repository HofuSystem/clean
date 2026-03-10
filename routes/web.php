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

        Route::get('', [PageController::class, 'home'])->name('home');
        Route::get('/b2b', [PageController::class, 'b2b'])->name('b2b');
        Route::get('/blogs', [PageController::class, 'blog'])->name('blog');
        Route::get('/blogs/{slug}', [PageController::class, 'blogPost'])->name('blogs-single');
        Route::get('/services', [PageController::class, 'services'])->name('services');
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
            DB::table('news_letters')
                ->updateOrInsert(
                    ['email' => request('email')],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            return redirect()->back()->with('success', 'Thank you for subscribing to our newsletter');
        })->name('newsletter');

        // Authentication routes
        Route::post('/register', [AuthController::class, 'registerStore'])->name('client.register.store');
        Route::group(['prefix' => '', 'middleware' => 'guest', 'as' => 'client.'], function () {
            // Route::get('/register', [AuthController::class, 'register'])->name('register');
            Route::get('/login', [AuthController::class, 'login'])->name('login');
            Route::post('/login', [AuthController::class, 'loginStore'])->name('login.store');
        });
        Route::group(['prefix' => 'business', 'as' => 'client.', 'middleware' => 'auth'], function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/clientsOrders', [DashboardController::class, 'clientsOrders'])->name('clientsOrders');
            Route::get('/clientsOrders/data', [DashboardController::class, 'clientsOrdersData'])->name('clientsOrders.data');
            Route::get('/monthly-invoices', [DashboardController::class, 'monthlyInvoices'])->name('monthly-invoices');
            Route::get('/monthly-invoices/{year}/{month}', [DashboardController::class, 'monthlyInvoiceDetails'])->name('monthly-invoice-details');
            Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
                Route::get('', [DashboardController::class, 'orders'])->name('index');
                Route::get('/data', [DashboardController::class, 'ordersData'])->name('data');
                Route::get('/{id}', [DashboardController::class, 'showOrder'])->name('show');
                Route::get('/{id}/invoice', [DashboardController::class, 'invoice'])->name('invoice');
                Route::post('', [DashboardController::class, 'orderStore'])->name('store');
                Route::post('/get-dates-times', [DashboardController::class, 'getDatesTimes'])->name('get-dates-times');
            });
            Route::group(['prefix' => 'schedule', 'as' => 'schedule.'], function () {
                Route::get('', [ScheduleController::class, 'index'])->name('index');
                Route::post('', [ScheduleController::class, 'store'])->name('store');
                Route::delete('/{id}', [ScheduleController::class, 'destroy'])->name('delete');
            });
            Route::get('/points', [DashboardController::class, 'points'])->name('points');
            Route::group(['prefix' => 'contracts', 'as' => 'contracts.'], function () {
                Route::get('', [DashboardController::class, 'contract'])->name('contract');
                Route::get('customer-prices', [DashboardController::class, 'customerPrices'])->name('customer-prices');
                Route::get('customer-prices/search', [DashboardController::class, 'searchProducts'])->name('customer-prices.search');
                Route::post('customer-prices', [DashboardController::class, 'customerPricesStore'])->name('customer-prices.store');
                Route::put('customer-prices/{priceId}', [DashboardController::class, 'customerPricesUpdate'])->name('customer-prices.update');
                Route::delete('customer-prices/{priceId}', [DashboardController::class, 'customerPricesDelete'])->name('customer-prices.delete');
            });
            Route::group(['prefix' => 'address', 'as' => 'address.'], function () {
                Route::get('/', [AddressController::class, 'address'])->name('index');
                Route::post('/', [AddressController::class, 'store'])->name('store');
                Route::post('/{id}', [AddressController::class, 'update'])->name('update');
                Route::delete('/{id}', [AddressController::class, 'delete'])->name('delete');
            });
            Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
                Route::get('/', [DashboardController::class, 'updateProfile'])->name('update-profile');
                Route::post('/', [DashboardController::class, 'updateProfileStore'])->name('update-profile.store');
                Route::post('/password', [DashboardController::class, 'updatePassword'])->name('update-password');
            });
        });
    }
);

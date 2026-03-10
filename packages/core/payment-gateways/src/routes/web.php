<?php

use Core\PaymentGateways\Controllers\FrontEnd\PaymentGatewayController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        
        Route::get('/payment-gateway/{transaction_id}', [PaymentGatewayController::class, 'paymentGateway'])
        ->name('payment-gateway.web');
        
        Route::post('/payment-gateway/{transaction_id}/execute-embedded', [PaymentGatewayController::class, 'executeEmbeddedPayment'])
        ->name('payment-gateway.web.execute-embedded');
        
        Route::get('/payment-gateway/{transaction_id}/callback', [PaymentGatewayController::class, 'paymentCallback'])
        ->name('payment-gateway.web.callback');
    }
);

<?php

use Core\Settings\Controllers\Dashboard\SettingsController;
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

Route::group(['middleware' => 'web' ],function () {
    Route::group(
        [
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
        ],
        function () {

            Route::group(['prefix' => 'admin', 'as' => 'dashboard.'], function () {
                Route::group(['middleware' => ['auth','active','checkPermission']], function () {
                    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
                        Route::get('',[SettingsController::class,'settings'])->name('settings');
                        Route::post('',[SettingsController::class,'settingsSave'])->name('settings');
                        Route::post('delivery',[SettingsController::class,'deliveryCharges'])->name('update-delivery-charges');
                    });

                });
            });

        }
    );

});


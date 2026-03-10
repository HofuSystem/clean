<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Categories\Controllers\Dashboard\CategoriesController;
use Core\Categories\Controllers\Dashboard\CategoryDatesController;
use Core\Categories\Controllers\Dashboard\CategoryTimesController;
use Core\Categories\Controllers\Dashboard\CategoryOffersController;
use Core\Categories\Controllers\Dashboard\CategoryTypesController;
use Core\Categories\Controllers\Dashboard\CategorySettingsController;
use Core\Categories\Controllers\Dashboard\SlidersController;
use Core\Categories\Controllers\Dashboard\CategoryDateTimesController;

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

        Route::group(['prefix' => 'admin', 'as' => 'dashboard.'], function () {
            Route::group(['middleware' => ['auth','active','checkPermission']], function () {
                //{{ new_routes}}



            });
        });
    }
);





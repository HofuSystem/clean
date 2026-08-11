<?php

use Core\Info\Controllers\Api\CitiesController;
use Core\Info\Controllers\Api\CoverageNotificationController;
use Core\Info\Controllers\Api\DistrictsController;
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
 //{{ new_routes}}
Route::group([], function () {
    Route::group(['prefix' => 'cities'], function () {
        Route::get('', [CitiesController::class, 'list']);
    });
    Route::group(['prefix' => 'districts'], function () {
        Route::get('', [DistrictsController::class, 'list']);
    });
    Route::get('city/{id}/districts', [CitiesController::class, 'districts']);
    Route::post('coverage-notifications/subscribe', [CoverageNotificationController::class, 'subscribe'])->middleware(['auth:sanctum', 'active']);
});


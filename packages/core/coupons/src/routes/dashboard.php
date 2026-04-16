<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Coupons\Controllers\Dashboard\CouponsController;
use Core\Coupons\Controllers\Dashboard\GiftsController;


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

                Route::group(['prefix' => 'coupons', 'as' => 'coupons.' ], function () {
                    Route::get('', [CouponsController::class,'index'])->name('index');
                    Route::post('', [CouponsController::class,'dataTable'])->name('index');
                    Route::get('create', [CouponsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CouponsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CouponsController::class,'importView'])->name('import');
                    Route::post('import', [CouponsController::class,'import'])->name('import');
                    Route::get('export', [CouponsController::class,'export'])->name('export');
                    Route::get('{id}', [CouponsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CouponsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CouponsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CouponsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CouponsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CouponsController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'gifts', 'as' => 'gifts.' ], function () {
                    Route::get('', [GiftsController::class,'index'])->name('index');
                    Route::post('', [GiftsController::class,'dataTable'])->name('index');
                    Route::get('create', [GiftsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [GiftsController::class,'storeOrUpdate'])->name('create');
                    Route::get('{id}/edit', [GiftsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [GiftsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [GiftsController::class,'delete'])->name('delete');
                    Route::put('{id}/restore', [GiftsController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}




            });
        });
    }
);





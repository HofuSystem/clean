<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Products\Controllers\Dashboard\ProductsController;
use Core\Products\Controllers\Dashboard\ProductSettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
|*/
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {

        Route::group(['prefix' => 'admin', 'as' => 'dashboard.'], function () {
            Route::group(['middleware' => ['auth','active','checkPermission']], function () {

                Route::group(['prefix' => 'products', 'as' => 'products.' ], function () {

                    Route::get('best_sales', [ProductsController::class, 'bestSales'])->name('best_sales');
                    Route::get('best_sales/export', [ProductsController::class,'salesExport'])->name('salesExport');

                    Route::get('', [ProductsController::class,'index'])->name('index');
                    Route::post('', [ProductsController::class,'dataTable'])->name('index');
                    Route::get('create', [ProductsController::class,'create'])->name('create');
                    Route::post('create', [ProductsController::class,'store'])->name('create');
                    Route::get('import', [ProductsController::class,'importView'])->name('import');
                    Route::post('import', [ProductsController::class,'import'])->name('import');
                    Route::get('export', [ProductsController::class,'export'])->name('export');
                    Route::get('{id}', [ProductsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [ProductsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ProductsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ProductsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [ProductsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [ProductsController::class,'restore'])->name('restore');
                    Route::post('{id}/associate-settings', [ProductsController::class, 'associateSettings'])->name('associate-settings');
                    Route::delete('{id}/delete-setting/{setting_id}', [ProductsController::class, 'deleteSetting'])->name('delete-setting');
                });
                
                Route::group(['prefix' => 'product-settings', 'as' => 'product-settings.' ], function () {
                    Route::get('', [ProductSettingsController::class,'index'])->name('index');
                    Route::post('', [ProductSettingsController::class,'dataTable'])->name('index');
                    Route::get('create', [ProductSettingsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [ProductSettingsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [ProductSettingsController::class,'importView'])->name('import');
                    Route::post('import', [ProductSettingsController::class,'import'])->name('import');
                    Route::get('export', [ProductSettingsController::class,'export'])->name('export');
                    Route::get('{id}', [ProductSettingsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [ProductSettingsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ProductSettingsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ProductSettingsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [ProductSettingsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [ProductSettingsController::class,'restore'])->name('restore');
                });
                
                Route::group(['prefix' => 'product-sub-settings', 'as' => 'product-sub-settings.' ], function () {
                    Route::get('', [ProductSettingsController::class,'index'])->name('index');
                    Route::post('', [ProductSettingsController::class,'dataTable'])->name('index');
                    Route::get('create', [ProductSettingsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [ProductSettingsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [ProductSettingsController::class,'importView'])->name('import');
                    Route::post('import', [ProductSettingsController::class,'import'])->name('import');
                    Route::get('export', [ProductSettingsController::class,'export'])->name('export');
                    Route::get('{id}', [ProductSettingsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [ProductSettingsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ProductSettingsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ProductSettingsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [ProductSettingsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [ProductSettingsController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}
            });
        });
    }
);





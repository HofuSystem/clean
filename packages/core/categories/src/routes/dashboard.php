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
use Core\Categories\Controllers\Dashboard\PricesController;

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

                Route::group(['prefix' => 'categories', 'as' => 'categories.' ], function () {
                    Route::get('', [CategoriesController::class,'index'])->name('index');
                    Route::post('', [CategoriesController::class,'dataTable'])->name('index');
                    Route::get('create', [CategoriesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategoriesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategoriesController::class,'importView'])->name('import');
                    Route::post('import', [CategoriesController::class,'import'])->name('import');
                    Route::get('export', [CategoriesController::class,'export'])->name('export');
                    Route::get('{id}', [CategoriesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategoriesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategoriesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategoriesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategoriesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CategoriesController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'sub-categories', 'as' => 'sub-categories.' ], function () {
                    Route::get('', [CategoriesController::class,'index'])->name('index');
                    Route::post('', [CategoriesController::class,'dataTable'])->name('index');
                    Route::get('create', [CategoriesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategoriesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategoriesController::class,'importView'])->name('import');
                    Route::post('import', [CategoriesController::class,'import'])->name('import');
                    Route::get('export', [CategoriesController::class,'export'])->name('export');
                    Route::get('{id}', [CategoriesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategoriesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategoriesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategoriesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategoriesController::class,'comment'])->name('comment');
                    Route::get('{id}/duplicate', [CategoriesController::class,'duplicate'])->name('duplicate');
                    Route::post('{id}/duplicate', [CategoriesController::class,'duplicateAction'])->name('duplicate');
                    Route::put('{id}/restore', [CategoriesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'services', 'as' => 'services.' ], function () {
                    Route::get('', [CategoriesController::class,'index'])->name('index');
                    Route::post('', [CategoriesController::class,'dataTable'])->name('index');
                    Route::get('create', [CategoriesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategoriesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategoriesController::class,'importView'])->name('import');
                    Route::post('import', [CategoriesController::class,'import'])->name('import');
                    Route::get('export', [CategoriesController::class,'export'])->name('export');
                    Route::get('{id}', [CategoriesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategoriesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategoriesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategoriesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategoriesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CategoriesController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'sub-services', 'as' => 'sub-services.' ], function () {
                    Route::get('', [CategoriesController::class,'index'])->name('index');
                    Route::post('', [CategoriesController::class,'dataTable'])->name('index');
                    Route::get('create', [CategoriesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategoriesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategoriesController::class,'importView'])->name('import');
                    Route::post('import', [CategoriesController::class,'import'])->name('import');
                    Route::get('export', [CategoriesController::class,'export'])->name('export');
                    Route::get('{id}', [CategoriesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategoriesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategoriesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategoriesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategoriesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CategoriesController::class,'restore'])->name('restore');
                });



                Route::group(['prefix' => 'category-date-times', 'as' => 'category-date-times.' ], function () {
                    Route::get('', [CategoryDateTimesController::class,'index'])->name('index');
                    Route::post('', [CategoryDateTimesController::class,'dataTable'])->name('index');
                    Route::get('create', [CategoryDateTimesController::class,'create'])->name('create');
                    Route::post('create', [CategoryDateTimesController::class,'store'])->name('create');
                    Route::get('{type}/{date}/edit', [CategoryDateTimesController::class,'edit'])->name('edit');
                    Route::post('{type}/{date}/edit', [CategoryDateTimesController::class,'update'])->name('edit');
                    Route::delete('{type}/{date}/delete', [CategoryDateTimesController::class,'delete'])->name('delete');
                    Route::post('duplicate', [CategoryDateTimesController::class,'duplicate'])->name('duplicate');
                    Route::put('{id}/restore', [CategoryDateTimesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'category-offers', 'as' => 'category-offers.' ], function () {
                    Route::get('', [CategoryOffersController::class,'index'])->name('index');
                    Route::post('', [CategoryOffersController::class,'dataTable'])->name('index');
                    Route::get('create', [CategoryOffersController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategoryOffersController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategoryOffersController::class,'importView'])->name('import');
                    Route::post('import', [CategoryOffersController::class,'import'])->name('import');
                    Route::get('export', [CategoryOffersController::class,'export'])->name('export');
                    Route::get('{id}', [CategoryOffersController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategoryOffersController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategoryOffersController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategoryOffersController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategoryOffersController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CategoryOffersController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'category-types', 'as' => 'category-types.' ], function () {
                    Route::get('', [CategoryTypesController::class,'index'])->name('index');
                    Route::post('', [CategoryTypesController::class,'dataTable'])->name('index');
                    Route::get('create', [CategoryTypesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategoryTypesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategoryTypesController::class,'importView'])->name('import');
                    Route::post('import', [CategoryTypesController::class,'import'])->name('import');
                    Route::get('export', [CategoryTypesController::class,'export'])->name('export');
                    Route::get('{id}', [CategoryTypesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategoryTypesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategoryTypesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategoryTypesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategoryTypesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CategoryTypesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'additional-features', 'as' => 'additional-features.' ], function () {
                    Route::get('', [CategorySettingsController::class,'index'])->name('index');
                    Route::post('', [CategorySettingsController::class,'dataTable'])->name('index');
                    Route::get('create', [CategorySettingsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategorySettingsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategorySettingsController::class,'importView'])->name('import');
                    Route::post('import', [CategorySettingsController::class,'import'])->name('import');
                    Route::get('export', [CategorySettingsController::class,'export'])->name('export');
                    Route::get('{id}', [CategorySettingsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategorySettingsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategorySettingsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategorySettingsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategorySettingsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CategorySettingsController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'category-settings', 'as' => 'category-settings.' ], function () {
                    Route::get('', [CategorySettingsController::class,'index'])->name('index');
                    Route::post('', [CategorySettingsController::class,'dataTable'])->name('index');
                    Route::get('create', [CategorySettingsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategorySettingsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategorySettingsController::class,'importView'])->name('import');
                    Route::post('import', [CategorySettingsController::class,'import'])->name('import');
                    Route::get('export', [CategorySettingsController::class,'export'])->name('export');
                    Route::get('{id}', [CategorySettingsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategorySettingsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategorySettingsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategorySettingsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategorySettingsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CategorySettingsController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'category-sub-settings', 'as' => 'category-sub-settings.' ], function () {
                    Route::get('', [CategorySettingsController::class,'index'])->name('index');
                    Route::post('', [CategorySettingsController::class,'dataTable'])->name('index');
                    Route::get('create', [CategorySettingsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CategorySettingsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CategorySettingsController::class,'importView'])->name('import');
                    Route::post('import', [CategorySettingsController::class,'import'])->name('import');
                    Route::get('export', [CategorySettingsController::class,'export'])->name('export');
                    Route::get('{id}', [CategorySettingsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CategorySettingsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CategorySettingsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CategorySettingsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CategorySettingsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CategorySettingsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'sliders', 'as' => 'sliders.' ], function () {
                    Route::get('', [SlidersController::class,'index'])->name('index');
                    Route::post('', [SlidersController::class,'dataTable'])->name('index');
                    Route::get('create', [SlidersController::class,'createOrEdit'])->name('create');
                    Route::post('create', [SlidersController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [SlidersController::class,'importView'])->name('import');
                    Route::post('import', [SlidersController::class,'import'])->name('import');
                    Route::get('export', [SlidersController::class,'export'])->name('export');
                    Route::get('{id}', [SlidersController::class,'show'])->name('show');
                    Route::get('{id}/edit', [SlidersController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [SlidersController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [SlidersController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [SlidersController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [SlidersController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'prices', 'as' => 'prices.' ], function () {
                    Route::get('', [PricesController::class,'index'])->name('index');
                    Route::post('', [PricesController::class,'dataTable'])->name('index');
                    Route::get('create', [PricesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [PricesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [PricesController::class,'importView'])->name('import');
                    Route::post('import', [PricesController::class,'import'])->name('import');
                    Route::get('export', [PricesController::class,'export'])->name('export');
                    Route::get('{id}', [PricesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [PricesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [PricesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [PricesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [PricesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [PricesController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}



            });
        });
    }
);





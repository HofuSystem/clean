<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Info\Controllers\Dashboard\CountriesController;
use Core\Info\Controllers\Dashboard\CitiesController;
use Core\Info\Controllers\Dashboard\DistrictsController;
use Core\Info\Controllers\Dashboard\NationalitiesController;
use Core\Info\Controllers\Dashboard\FavsController;
use Core\Info\Controllers\Dashboard\MapPointsController;

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
            Route::group(['middleware' => ['auth','active']], function () {


                Route::group(['prefix' => 'countries', 'as' => 'countries.' ], function () {
                    Route::get('', [CountriesController::class,'index'])->name('index');
                    Route::post('', [CountriesController::class,'dataTable'])->name('index');
                    Route::get('create', [CountriesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CountriesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CountriesController::class,'importView'])->name('import');
                    Route::post('import', [CountriesController::class,'import'])->name('import');
                    Route::get('export', [CountriesController::class,'export'])->name('export');
                    Route::get('{id}', [CountriesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CountriesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CountriesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CountriesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CountriesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CountriesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'cities', 'as' => 'cities.' ], function () {
                    Route::get('', [CitiesController::class,'index'])->name('index');
                    Route::post('', [CitiesController::class,'dataTable'])->name('index');
                    Route::get('create', [CitiesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CitiesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CitiesController::class,'importView'])->name('import');
                    Route::post('import', [CitiesController::class,'import'])->name('import');
                    Route::get('export', [CitiesController::class,'export'])->name('export');
                    Route::get('{id}', [CitiesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CitiesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CitiesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CitiesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CitiesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CitiesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'districts', 'as' => 'districts.' ], function () {
                    Route::get('', [DistrictsController::class,'index'])->name('index');
                    Route::post('', [DistrictsController::class,'dataTable'])->name('index');
                    Route::get('map-points', [DistrictsController::class,'mapPoints'])->name('map-points');
                    Route::get('create', [DistrictsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [DistrictsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [DistrictsController::class,'importView'])->name('import');
                    Route::post('import', [DistrictsController::class,'import'])->name('import');
                    Route::get('export', [DistrictsController::class,'export'])->name('export');
                    Route::get('{id}', [DistrictsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [DistrictsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [DistrictsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [DistrictsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [DistrictsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [DistrictsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'nationalities', 'as' => 'nationalities.' ], function () {
                    Route::get('', [NationalitiesController::class,'index'])->name('index');
                    Route::post('', [NationalitiesController::class,'dataTable'])->name('index');
                    Route::get('create', [NationalitiesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [NationalitiesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [NationalitiesController::class,'importView'])->name('import');
                    Route::post('import', [NationalitiesController::class,'import'])->name('import');
                    Route::get('export', [NationalitiesController::class,'export'])->name('export');
                    Route::get('{id}', [NationalitiesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [NationalitiesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [NationalitiesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [NationalitiesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [NationalitiesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [NationalitiesController::class,'restore'])->name('restore');
                });

                //{{ new_routes}}
            });
        });
    }
);





<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Workers\Controllers\Dashboard\WorkersController;
use Core\Workers\Controllers\Dashboard\WorkerDaysController;

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


                Route::group(['prefix' => 'workers', 'as' => 'workers.' ], function () {
                    Route::get('', [WorkersController::class,'index'])->name('index');
                    Route::post('', [WorkersController::class,'dataTable'])->name('index');
                    Route::get('create', [WorkersController::class,'createOrEdit'])->name('create');
                    Route::post('create', [WorkersController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [WorkersController::class,'importView'])->name('import');
                    Route::post('import', [WorkersController::class,'import'])->name('import');
                    Route::get('export', [WorkersController::class,'export'])->name('export');
                    Route::get('{id}', [WorkersController::class,'show'])->name('show');
                    Route::get('{id}/edit', [WorkersController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [WorkersController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [WorkersController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [WorkersController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [WorkersController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'worker-days', 'as' => 'worker-days.' ], function () {
                    Route::get('', [WorkerDaysController::class,'index'])->name('index');
                    Route::post('', [WorkerDaysController::class,'dataTable'])->name('index');
                    Route::get('create', [WorkerDaysController::class,'createOrEdit'])->name('create');
                    Route::post('create', [WorkerDaysController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [WorkerDaysController::class,'importView'])->name('import');
                    Route::post('import', [WorkerDaysController::class,'import'])->name('import');
                    Route::get('export', [WorkerDaysController::class,'export'])->name('export');
                    Route::get('{id}', [WorkerDaysController::class,'show'])->name('show');
                    Route::get('{id}/edit', [WorkerDaysController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [WorkerDaysController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [WorkerDaysController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [WorkerDaysController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [WorkerDaysController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}

            });
        });
    }
);





<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Blog\Controllers\Dashboard\BlogsController;
use Core\Blog\Controllers\Dashboard\BlogCategoriesController;

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

                Route::group(['prefix' => 'blogs', 'as' => 'blogs.' ], function () {
                    Route::get('', [BlogsController::class,'index'])->name('index');
                    Route::post('', [BlogsController::class,'dataTable'])->name('index');
                    Route::get('create', [BlogsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [BlogsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [BlogsController::class,'importView'])->name('import');
                    Route::post('import', [BlogsController::class,'import'])->name('import');
                    Route::get('export', [BlogsController::class,'export'])->name('export');
                    Route::get('{id}', [BlogsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [BlogsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [BlogsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [BlogsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [BlogsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [BlogsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'blog-categories', 'as' => 'blog-categories.' ], function () {
                    Route::get('', [BlogCategoriesController::class,'index'])->name('index');
                    Route::post('', [BlogCategoriesController::class,'dataTable'])->name('index');
                    Route::get('create', [BlogCategoriesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [BlogCategoriesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [BlogCategoriesController::class,'importView'])->name('import');
                    Route::post('import', [BlogCategoriesController::class,'import'])->name('import');
                    Route::get('export', [BlogCategoriesController::class,'export'])->name('export');
                    Route::get('{id}', [BlogCategoriesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [BlogCategoriesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [BlogCategoriesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [BlogCategoriesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [BlogCategoriesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [BlogCategoriesController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}



            });
        });
    }
);





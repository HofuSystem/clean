<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\CMS\Controllers\Dashboard\CategoriesController;
use Core\CMS\Controllers\Dashboard\CmsPagesController;
use Core\CMS\Controllers\Dashboard\CmsPageDetailsController;

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

                /* Route::get('cms-page/{slug}', [CmsPageController::class, 'index'])->name('cms.page');
                Route::get('cms-page/create/{slug}', [CmsPageController::class, 'create'])->name('cms.page.create');
                Route::post('cms-page/store/{slug}', [CmsPageController::class, 'store'])->name('cms.page.create');
                Route::get('cms-page/show/{slug}/{id?}', [CmsPageController::class, 'show'])->name('cms.page.show');
                Route::patch('cms-page/edit/{slug}/{id}', [CmsPageController::class, 'update'])->name('cms.page.edit');
                Route::get('cms-page/delete/{slug}/{id}', [CmsPageController::class, 'destroy'])->name('cms.page.delete'); */

                /* Route::group(['prefix' => 'cms-pages', 'as' => 'cms-pages.' ], function () {
                    Route::get('/{slug}', [CmsPagesController::class,'index'])->name('index');
                    Route::post('', [CmsPagesController::class,'dataTable'])->name('index');
                    Route::get('create/{slug}', [CmsPagesController::class,'createOrEdit'])->name('create');
                    Route::post('store/{slug}', [CmsPagesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CmsPagesController::class,'importView'])->name('import');
                    Route::post('import', [CmsPagesController::class,'import'])->name('import');
                    Route::get('export', [CmsPagesController::class,'export'])->name('export');
                    Route::get('show/{slug}/{id?}', [CmsPagesController::class,'show'])->name('show');
                    Route::get('/edit/{slug}/{id}', [CmsPagesController::class,'createOrEdit'])->name('edit');
                    Route::put('update/{slug}/{id}', [CmsPagesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('delete/{slug}/{id}', [CmsPagesController::class,'delete'])->name('delete');
                    Route::post('comment/{slug}/{id}', [CmsPagesController::class,'comment'])->name('comment');
                }); */

                Route::group(['prefix' => 'cms-page-details/{slug}', 'as' => 'cms-page-details.' ], function () {
                    Route::get('', [CmsPageDetailsController::class,'index'])->name('index');
                    Route::post('', [CmsPageDetailsController::class,'dataTable'])->name('index');
                    Route::get('create', [CmsPageDetailsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CmsPageDetailsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CmsPageDetailsController::class,'importView'])->name('import');
                    Route::post('import', [CmsPageDetailsController::class,'import'])->name('import');
                    Route::get('export', [CmsPageDetailsController::class,'export'])->name('export');
                    Route::get('{id}', [CmsPageDetailsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [CmsPageDetailsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CmsPageDetailsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CmsPageDetailsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CmsPageDetailsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CmsPageDetailsController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}
            });
        });
    }
);





<?php

use Core\Admin\Controllers\Dashboard\HomeController;
use Core\Admin\Controllers\Dashboard\NavController;
use Core\Admin\Controllers\Dashboard\TranslationController;
use Core\Admin\Controllers\Dashboard\UsersAnalysisController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Admin\Controllers\Dashboard\CmsPageDetailsController;
use Core\Admin\Controllers\Dashboard\RouteRecordsController;
use Core\Admin\Controllers\Dashboard\RoutesRecordsController;


use Core\Admin\Controllers\Dashboard\ActivityLogController;
use Core\Admin\Controllers\Dashboard\OrderQuantitiesReportController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
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
            // Image Uploader Utility
            Route::group(['middleware' => ['auth', 'active']], function () {
                Route::get('/image-uploader', [\Core\Admin\Controllers\Dashboard\ImageUploadController::class, 'index'])->name('image-uploader.index');
                Route::post('/image-uploader', [\Core\Admin\Controllers\Dashboard\ImageUploadController::class, 'upload'])->name('image-uploader.upload');
                Route::delete('/image-uploader/{image}', [\Core\Admin\Controllers\Dashboard\ImageUploadController::class, 'destroy'])->name('image-uploader.destroy');
            });

            Route::group(['middleware' => ['auth', 'active', 'checkPermission']], function () {
                Route::get('routes-analysis', [RouteRecordsController::class, 'index'])->name('routes-analysis.index');

                //home route
                Route::get('dashboard', [HomeController::class, 'index'])->name('index');
                Route::get('analysis', [HomeController::class, 'analysis'])->name('analysis');
                
                //users analysis route
                Route::get('users-analysis', [UsersAnalysisController::class, 'index'])->name('users-analysis');


                // Order Quantities Report
                Route::group(['prefix' => 'order-quantities-report', 'as' => 'order-quantities-report.'], function () {
                    Route::get('', [OrderQuantitiesReportController::class, 'index'])->name('index');
                    Route::get('select-clients', [OrderQuantitiesReportController::class, 'selectClients'])->name('select-clients');
                    Route::get('select-companies', [OrderQuantitiesReportController::class, 'selectCompanies'])->name('select-companies');
                    Route::get('export', [OrderQuantitiesReportController::class, 'export'])->name('export');
                });


                //nav routes
                Route::group(['prefix' => 'nav-bar', 'as' => 'nav-bar.'], function () {
                    Route::group(['prefix' => '{slug}', 'as' => 'nav.'], function () {
                        Route::get('', [NavController::class, 'index'])->name('index');
                        Route::post('', [NavController::class, 'save'])->name('save');
                    });
                });

                //Translation Routes
                Route::group(['prefix' => 'translation', 'as' => 'translation.'], function () {
                    Route::get('', [TranslationController::class, 'index'])->name('index');
                    Route::post('', [TranslationController::class, 'store'])->name('create');
                    Route::post('storeMultiple', [TranslationController::class, 'storeMultiple'])->name('storeMultiple');
                    Route::delete('destroy', [TranslationController::class, 'destroy'])->name('destroy');
                });
                Route::group(['prefix' => 'routes-records', 'as' => 'routes-records.'], function () {
                    Route::get('', [RoutesRecordsController::class, 'index'])->name('index');
                    Route::post('', [RoutesRecordsController::class, 'dataTable'])->name('index');
                    Route::get('create', [RoutesRecordsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [RoutesRecordsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [RoutesRecordsController::class, 'importView'])->name('import');
                    Route::post('import', [RoutesRecordsController::class, 'import'])->name('import');
                    Route::get('export', [RoutesRecordsController::class, 'export'])->name('export');
                    Route::get('{id}', [RoutesRecordsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [RoutesRecordsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [RoutesRecordsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [RoutesRecordsController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [RoutesRecordsController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [RoutesRecordsController::class, 'restore'])->name('restore');
                });



                // Activity Log Routes
                Route::group(['prefix' => 'activity-log', 'as' => 'activity-log.'], function () {
                    Route::get('', [ActivityLogController::class, 'index'])->name('index');
                    Route::get('model-history', [ActivityLogController::class, 'modelHistory'])->name('model-history');
                    Route::get('{id}', [ActivityLogController::class, 'show'])->name('show');
                    Route::put('{id}/restore', [ActivityLogController::class, 'restore'])->name('restore');
                });

                //{{ new_routes }}

            });
        });
    }
);

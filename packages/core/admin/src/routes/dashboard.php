<?php

use Core\Admin\Controllers\Dashboard\HomeController;
use Core\Admin\Controllers\Dashboard\NavController;
use Core\Admin\Controllers\Dashboard\TranslationController;
use Core\Admin\Controllers\Dashboard\UsersAnalysisController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Admin\Controllers\Dashboard\CmsPageDetailsController;
use Core\Admin\Controllers\Dashboard\OrderInvociesController;
use Core\Admin\Controllers\Dashboard\RouteRecordsController;
use Core\Admin\Controllers\Dashboard\RoutesRecordsController;
use Core\Admin\Controllers\Dashboard\DetailedAnalysisController;
use Core\Admin\Controllers\Dashboard\FixedCostController;
use Core\Admin\Controllers\Dashboard\ActivityLogController;

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
            Route::group(['middleware' => ['auth', 'active', 'checkPermission']], function () {
                Route::get('routes-analysis', [RouteRecordsController::class, 'index'])->name('routes-analysis.index');

                //home route
                Route::get('dashboard', [HomeController::class, 'index'])->name('index');
                Route::get('analysis', [HomeController::class, 'analysis'])->name('analysis');
                
                //users analysis route
                Route::get('users-analysis', [UsersAnalysisController::class, 'index'])->name('users-analysis');
                Route::get('detailed-analysis', [DetailedAnalysisController::class, 'index'])->name('detailed-analysis');
                Route::post('detailed-analysis/fixed-cost', [DetailedAnalysisController::class, 'storeFixedCost'])->name('detailed-analysis.store-fixed-cost');
                Route::get('detailed-analysis/order-transactions', [DetailedAnalysisController::class, 'getOrderTransactions'])->name('detailed-analysis.order-transactions');
                Route::get('detailed-analysis/order-transactions/export', [DetailedAnalysisController::class, 'exportOrderTransactions'])->name('detailed-analysis.order-transactions.export');

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

                // Fixed Costs Routes
                Route::group(['prefix' => 'fixed-costs', 'as' => 'fixed-costs.'], function () {
                    Route::get('', [FixedCostController::class, 'index'])->name('index');
                    Route::get('create', [FixedCostController::class, 'create'])->name('create');
                    Route::post('', [FixedCostController::class, 'store'])->name('store');
                    Route::get('{fixedCost}', [FixedCostController::class, 'show'])->name('show');
                    Route::get('{fixedCost}/edit', [FixedCostController::class, 'edit'])->name('edit');
                    Route::put('{fixedCost}', [FixedCostController::class, 'update'])->name('update');
                    Route::delete('{fixedCost}', [FixedCostController::class, 'destroy'])->name('destroy');
                    Route::put('{fixedCost}/restore', [FixedCostController::class, 'restore'])->name('restore');
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

<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Wallet\Controllers\Dashboard\WalletPackagesController;
use Core\Wallet\Controllers\Dashboard\WalletTransactionsController;

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


                Route::group(['prefix' => 'wallet-packages', 'as' => 'wallet-packages.' ], function () {
                    Route::get('', [WalletPackagesController::class,'index'])->name('index');
                    Route::post('', [WalletPackagesController::class,'dataTable'])->name('index');
                    Route::get('create', [WalletPackagesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [WalletPackagesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [WalletPackagesController::class,'importView'])->name('import');
                    Route::post('import', [WalletPackagesController::class,'import'])->name('import');
                    Route::get('export', [WalletPackagesController::class,'export'])->name('export');
                    Route::get('{id}', [WalletPackagesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [WalletPackagesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [WalletPackagesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [WalletPackagesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [WalletPackagesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [WalletPackagesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'wallet-transactions', 'as' => 'wallet-transactions.' ], function () {
                    Route::get('', [WalletTransactionsController::class,'index'])->name('index');
                    Route::post('', [WalletTransactionsController::class,'dataTable'])->name('index');
                    Route::get('create', [WalletTransactionsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [WalletTransactionsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [WalletTransactionsController::class,'importView'])->name('import');
                    Route::post('import', [WalletTransactionsController::class,'import'])->name('import');
                    Route::get('export', [WalletTransactionsController::class,'export'])->name('export');
                    Route::get('{id}', [WalletTransactionsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [WalletTransactionsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [WalletTransactionsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [WalletTransactionsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [WalletTransactionsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [WalletTransactionsController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}

            });
        });
    }
);





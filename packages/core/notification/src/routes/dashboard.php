<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Notification\Controllers\Dashboard\NotificationsController;
use Core\Notification\Controllers\Dashboard\BannerNotificationsController;

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


                Route::group(['prefix' => 'notifications', 'as' => 'notifications.' ], function () {
                    Route::get('', [NotificationsController::class,'index'])->name('index');
                    Route::post('', [NotificationsController::class,'dataTable'])->name('index');
                    Route::post('getusers', [NotificationsController::class,'getUsers'])->name('getusers');

                    Route::get('create', [NotificationsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [NotificationsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [NotificationsController::class,'importView'])->name('import');
                    Route::post('import', [NotificationsController::class,'import'])->name('import');
                    Route::get('export', [NotificationsController::class,'export'])->name('export');
                    Route::get('{id}', [NotificationsController::class,'show'])->name('show');
                    Route::post('{id}/getSentToUsers', [NotificationsController::class,'getSentToUsers'])->name('getSentToUsers');
                    Route::get('{id}/edit', [NotificationsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [NotificationsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [NotificationsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [NotificationsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [NotificationsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'banner-notifications', 'as' => 'banner-notifications.' ], function () {
                    Route::get('', [BannerNotificationsController::class,'index'])->name('index');
                    Route::post('', [BannerNotificationsController::class,'dataTable'])->name('index');
                    Route::get('create', [BannerNotificationsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [BannerNotificationsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [BannerNotificationsController::class,'importView'])->name('import');
                    Route::post('import', [BannerNotificationsController::class,'import'])->name('import');
                    Route::get('export', [BannerNotificationsController::class,'export'])->name('export');
                    Route::get('{id}', [BannerNotificationsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [BannerNotificationsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [BannerNotificationsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [BannerNotificationsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [BannerNotificationsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [BannerNotificationsController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}

            });
        });
    }
);





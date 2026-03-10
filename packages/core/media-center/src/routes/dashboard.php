<?php

use Core\MediaCenter\Controllers\Dashboard\MediaCenterController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {

        Route::group(['prefix' => 'admin', 'as' => 'dashboard.'], function () {
            Route::group(['middleware' => []], function () {

                // Settings Routes
                Route::group([
                    'prefix' => 'media-center',
                    'as' => 'media-center.'
                    // 'middleware' => 'password.confirm'
                ], function () {
                    Route::get('/list', [MediaCenterController::class, 'listAll'])->name('list');
                    Route::post('/add-new', [MediaCenterController::class, 'addNew'])->name('add-new');
                    Route::delete('/delete', [MediaCenterController::class, 'delete'])->name('delete');
                });
            });
        });
    }
);

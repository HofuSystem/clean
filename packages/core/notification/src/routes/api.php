<?php

use Core\Notification\Controllers\Api\NotificationController;
use Core\Notification\Controllers\Api\NotificationEventsController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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


// Notification
Route::group([
    'prefix' => 'notifications',
    'middleware' => ['auth:sanctum', 'active']
], function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/{id}', [NotificationController::class, 'show']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);

    Route::post('allow_notify', [NotificationController::class, 'allow_notify']);
    Route::post('events', [NotificationEventsController::class, 'recordEvent']);
});

Route::group([
    'prefix' => 'client/notifications',
    'middleware' => ['auth:sanctum', 'active']
], function () {
    Route::post('events', [NotificationEventsController::class, 'recordEvent']);
});

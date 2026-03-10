<?php

use Core\Users\Controllers\Api\AddressesController;
use Core\Users\Controllers\Api\AuthenticationController;
use Core\Users\Controllers\Api\Driver\AuthController;
use Core\Users\Controllers\Api\FavsController;
use Core\Users\Controllers\Api\PointsController;
use Core\Users\Controllers\Api\Technical\AuthController as TechnicalAuthController;
use Core\Users\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


//all users routes
Route::group([
    'middleware' => ['auth:sanctum', 'active']
], function () {
    Route::post('update_fcm', [UserController::class, 'updateFcm']);
    Route::post('delete_account', [UserController::class, 'deleteAccount']);
});

//all client routes
Route::group([
    'prefix' => 'client',
], function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/login_new', [AuthenticationController::class, 'loginNew']);
    Route::post('/verify', [AuthenticationController::class, 'verify']);
    Route::post('/send_code', [AuthenticationController::class, 'sendCode']);

});

Route::group([
    'prefix' => 'client',
    'middleware' => ['auth:sanctum', 'role:client', 'active']
], function () {

    //all client routes on points
    Route::group(['prefix' => 'points', 'as' => 'points.'], function () {
        Route::get('/', [PointsController::class, 'index']);
    });
    //all client routes on fav
    Route::group(['prefix' => 'fav', 'as' => 'fav.'], function () {
        Route::get('/', [FavsController::class, 'index']);
        Route::post('/', [FavsController::class, 'store']);
    });

    Route::get('/profile', [AuthenticationController::class, 'profile']);
    Route::get('/referral', [AuthenticationController::class, 'referral']);
    Route::post('/referral/update', [AuthenticationController::class, 'referralUpdate']);
    Route::post('/edit_profile', [AuthenticationController::class, 'editProfile']);
    Route::post('/update_qr_code', [AuthenticationController::class, 'updateQrCode']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::apiResource('addresses', AddressesController::class);

});

//all driver routes
Route::group([
    'prefix' => 'driver',
], function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group([
    'prefix' => 'driver',
    'middleware' => ['auth:sanctum', 'role:driver', 'active']
], function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/edit_profile', [AuthController::class, 'edit_profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

//all technical routes
Route::group([
    'prefix' => 'technical',
], function () {
    Route::post('/login', [TechnicalAuthController::class, 'login']);
});

Route::group([
    'prefix' => 'technical',
    'middleware' => ['auth:sanctum', 'role:technical', 'active']
], function () {
    Route::get('/profile', [TechnicalAuthController::class, 'profile']);
    Route::post('/edit_profile', [TechnicalAuthController::class, 'edit_profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

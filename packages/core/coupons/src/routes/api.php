<?php

use Core\Coupons\Controllers\Api\CouponsController;
use Core\Coupons\Controllers\Api\GiftsController;
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

Route::group([
    'middleware' => ['auth:sanctum','active'] ,
], function () {
    Route::get('coupon',[CouponsController::class,'get'])->name('coupons.get');
    Route::get('matching-gift',[GiftsController::class,'getMatchingGift'])->name('gifts.matching');
    Route::post('matching-gift', [GiftsController::class, 'attachGift'])->name('gifts.attach');
    Route::get('my-gifts', [GiftsController::class, 'myGifts'])->name('gifts.my_gifts');
});
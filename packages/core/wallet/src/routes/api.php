<?php

use Illuminate\Support\Facades\Route;
use Core\Wallet\Controllers\Api\WalletController;
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

//all client routes
Route::group([
    'middleware'=>['auth:sanctum','active'] 
],function(){

    //all client routes on wallet
    Route::group(['prefix'=>'wallet' , 'as' => 'wallet.' ],function(){
        Route::get('history',[WalletController::class,'history']);
        Route::get('packages',[WalletController::class,'packages']);
        Route::post('charge',[WalletController::class,'charge']);
        Route::post('charge/v2',[WalletController::class,'chargeV2']);
        Route::post('withdraw',[WalletController::class,'withdraw']);
    });

});


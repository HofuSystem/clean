<?php

use Core\Orders\Controllers\Api\Client\CartsController;
use Core\Orders\Controllers\Api\Client\OrdersController;
use Core\Orders\Controllers\Api\Client\ReportReasonController;
use Core\Users\Controllers\Api\Driver\OrderController  as DriverOrderController;
use Core\Users\Controllers\Api\Technical\OrderController  as TechnicalOrderController;
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

//all client routes on carts
Route::group(['prefix' => 'report_reasons', 'middleware'    =>  ['auth:sanctum',  'active']], function () {
    Route::get('', [ReportReasonController::class, 'list']);
});
//all client routes
Route::group([
    'prefix'        =>  'client',
    'middleware'    =>  ['auth:sanctum', 'role:client', 'active']
], function () {

    //all client routes on carts
    Route::group(['prefix' => 'cart'], function () {
        Route::post('', [CartsController::class, 'save']);
    });

    //all client routes on orders
    Route::group(['prefix' => 'orders'], function () {
        Route::get("", [OrdersController::class, 'myOrders']);
        Route::post("update-order", [OrdersController::class, 'updateOrder']);
        Route::post("", [OrdersController::class, 'createOrder']);
        Route::get("{id}", [OrdersController::class, 'myOrder']);
        Route::post("update_status/{id}", [OrdersController::class, 'updateStatus']);
    });
    Route::group(['prefix' => 'pay_fastorder/{id}'], function () {
        Route::post("", [OrdersController::class, 'payFastOrder']);
        Route::post("v2", [OrdersController::class, 'payFastOrderV2']);
    });
});
Route::group([
    'prefix'        =>  'driver',
    'middleware'    =>  ['auth:sanctum', 'role:driver', 'active']
], function () {
    Route::group([
        'prefix'        =>  'orders',
    ], function () {
        Route::get('', [DriverOrderController::class,'index']);
        Route::get('{order_id}', [DriverOrderController::class,'show']);
        Route::post('receiving_driver_accept/{order_id}', [DriverOrderController::class,'receiving_driver_accept']);
        Route::post('delivered_to_admin/{order_id}', [DriverOrderController::class,'delivered_to_admin']);
        Route::post('ready_to_delivered/{order_id}', [DriverOrderController::class,'ready_to_delivered']);
        Route::post('in_the_way/{order_id}', [DriverOrderController::class,'in_the_way']);
        Route::post('delivered/{order_id}', [DriverOrderController::class,'delivered']);
        Route::post('finished/{order_id}', [DriverOrderController::class,'finished']);
        Route::post('get_money_method/{order_id}', [DriverOrderController::class,'get_money_method']);
        Route::post('report/{order_id}', [DriverOrderController::class,'order_report']);
        Route::post('add_description/{order_id}', [DriverOrderController::class,'add_description']);

    });
});
Route::group([
    'prefix'        =>  'technical',
    'middleware'    =>  ['auth:sanctum', 'role:technical', 'active']
], function () {
    Route::group([
        'prefix'        =>  'orders',
    ], function () {
        // order
        Route::get('', [TechnicalOrderController::class,'index']);
        Route::get('{order_id}', [TechnicalOrderController::class,'show']);
        Route::post('technical_accept/{order_id}', [TechnicalOrderController::class,'technical_accept']);
        Route::post('in_the_way/{order_id}', [TechnicalOrderController::class,'in_the_way']);
        Route::post('started/{order_id}', [TechnicalOrderController::class,'started']);
        Route::post('finished/{order_id}', [TechnicalOrderController::class,'finished']);
        Route::post('get_money_method/{order_id}', [TechnicalOrderController::class,'get_money_method']);
        Route::post('report/{order_id}', [TechnicalOrderController::class,'order_report']);

    });
});

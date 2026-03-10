<?php

use Core\Categories\Controllers\Api\CategoriesController;
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
    'middleware'=>['auth:sanctum','active'] 
],function(){

    //all client routes
    Route::group([
        'prefix' => 'clothes',
    ], function () {
        Route::get('index', [CategoriesController::class, 'index']);
        Route::get('details/{id}', [CategoriesController::class, 'details']);
        Route::get('package_details/{id}', [CategoriesController::class, 'packageDetails']);
    });
    
    //all client routes
    Route::group([
        'prefix' => 'services',
    ], function () {
        Route::get('index', [CategoriesController::class, 'servicesIndex']);
        Route::get('details/{id}', [CategoriesController::class, 'servicesDetails']);
    });
    
    //all client routes
    Route::group([
        'prefix' => 'home-maid',
    ], function () {
        Route::get('index', [CategoriesController::class, 'homeMaidIndex']);
        Route::get('details/{id}', [CategoriesController::class, 'homeMaidDetails']);
    
        Route::get('flexible_order_details', [CategoriesController::class, 'flexibleOrderDetails']);
        Route::get('scheduled_order_details', [CategoriesController::class, 'scheduledOrderDetails']);
        Route::get('monthly_packages_order_details', [CategoriesController::class, 'monthlyPackagesOrderDetails']);
        Route::get('sale_order_details/{sale_id}', [CategoriesController::class, 'saleOrderDetails']);
    });
    
    Route::group(['prefix' => 'car-host', 'as' => 'car-host.'], function () {
        Route::get('index', [CategoriesController::class, 'hostIndex']);
        Route::get('details/{service_id}', [CategoriesController::class, 'hostDetails']);
    
        Route::get('host_order_details/{service_id}', [CategoriesController::class, 'hostOrderDetails']);
        Route::get('care_order_details/{service_id}', [CategoriesController::class, 'careOrderDetails']);
        Route::get('selfcare_order_details/{service_id}', [CategoriesController::class, 'selfCareOrderDetails']);
        Route::get('sale_order_details/{sale_id}', [CategoriesController::class, 'saleHostOrderDetails']);
    });

});    


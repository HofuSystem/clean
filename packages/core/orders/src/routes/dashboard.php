<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Orders\Controllers\Dashboard\OrdersController;
use Core\Orders\Controllers\Dashboard\CartsController;
use Core\Orders\Controllers\Dashboard\OrderItemsController;
use Core\Orders\Controllers\Dashboard\OrderItemQtyUpdatesController;
use Core\Orders\Controllers\Dashboard\OrderRepresentativesController;
use Core\Orders\Controllers\Dashboard\OrderTypesOfDatasController;
use Core\Orders\Controllers\Dashboard\ReportReasonsController;
use Core\Orders\Controllers\Dashboard\OrderReportsController;
use Core\Orders\Controllers\Dashboard\OrderInvociesController;
use Core\Orders\Controllers\Dashboard\OrderInvoicesController;
use Core\Orders\Controllers\Dashboard\DeliveryPricesController;
use Core\Orders\Controllers\Dashboard\OrderSchedulesController;
use Core\Orders\Controllers\Dashboard\OrderTransactionsController;

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


                Route::group(['prefix' => 'carts', 'as' => 'carts.' ], function () {
                    Route::get('', [CartsController::class,'index'])->name('index');
                    Route::post('', [CartsController::class,'dataTable'])->name('index');
                    Route::get('create', [CartsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [CartsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [CartsController::class,'importView'])->name('import');
                    Route::post('import', [CartsController::class,'import'])->name('import');
                    Route::get('export', [CartsController::class,'export'])->name('export');
                    Route::get('{id}', [CartsController::class,'show'])->name('show');
                    Route::get('{id}/create-order', [CartsController::class,'createOrder'])->name('create-order');
                    Route::get('{id}/edit', [CartsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CartsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CartsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [CartsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [CartsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'orders', 'as' => 'orders.' ], function () {
                    Route::get('', [OrdersController::class,'index'])->name('index');
                    Route::post('', [OrdersController::class,'dataTable'])->name('index');
                    Route::get('create', [OrdersController::class,'create'])->name('create');
                    Route::post('create', [OrdersController::class,'store'])->name('create');
                    Route::get('import', [OrdersController::class,'importView'])->name('import');
                    Route::post('import', [OrdersController::class,'import'])->name('import');
                    Route::get('export', [OrdersController::class,'export'])->name('export');
                    Route::get('get-date-time', [OrdersController::class,'getDateTimes'])->name('get-date-times');
                    Route::post('assign-representatives', [OrdersController::class,'assignRepresentatives'])->name('assign-representatives');
                    Route::post('assign-operators', [OrdersController::class,'assignOperators'])->name('assign-operators');
                    Route::post('apply-coupon', [OrdersController::class,'applyCoupon'])->name('apply-coupon');
                    Route::post('change-pay-type', [OrdersController::class,'changePayType'])->name('change-pay-type');
                    Route::post('update-delivery-price', [OrdersController::class,'updateDeliveryPrice'])->name('update-delivery-price');
                    Route::post('update-total-provider-invoice', [OrdersController::class,'updateTotalProviderInvoice'])->name('update-total-provider-invoice');
                  

                    Route::get('{id}', [OrdersController::class,'show'])->name('show');
                    Route::get('edit/{id}', [OrdersController::class,'edit'])->name('edit');
                    Route::post('edit/{id}', [OrdersController::class,'update'])->name('edit');
                    Route::delete('delete/{id}', [OrdersController::class,'delete'])->name('delete');
                    Route::post('comment/{id}', [OrdersController::class,'comment'])->name('comment');
                    Route::get('invoice/{id}', [OrdersController::class,'invoice'])->name('invoice');
                    Route::post('change-status/{id}', [OrdersController::class,'changeStatus'])->name('change-status');
                    Route::post('issue-status/{id}', [OrdersController::class,'issueStatus'])->name('issue-status');
                    Route::post('return-order-continue/{id}', [OrdersController::class,'returnOrderContinue'])->name('return-order-continue');

                    // Nested item routes
                    Route::group(['prefix'=>'{id}/item/{itemId}' ,'as'=>'item.'],function(){
                        Route::post('update-item', [OrdersController::class,'updateItem'])->name('edit');
                        Route::delete('delete', [OrdersController::class,'destroyItem'])->name('destroy');
                    });
                });

                Route::group(['prefix' => 'order-items', 'as' => 'order-items.' ], function () {
                    Route::get('', [OrderItemsController::class,'index'])->name('index');
                    Route::post('', [OrderItemsController::class,'dataTable'])->name('index');
                    Route::get('create', [OrderItemsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [OrderItemsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [OrderItemsController::class,'importView'])->name('import');
                    Route::post('import', [OrderItemsController::class,'import'])->name('import');
                    Route::get('export', [OrderItemsController::class,'export'])->name('export');
                    Route::get('{id}', [OrderItemsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [OrderItemsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [OrderItemsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [OrderItemsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [OrderItemsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [OrderItemsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'order-representatives', 'as' => 'order-representatives.' ], function () {
                    Route::get('analysis', [OrderRepresentativesController::class,'analysis'])->name('analysis');
                    Route::get('collectiveAnalysis', [OrderRepresentativesController::class,'collectiveAnalysis'])->name('collectiveAnalysis');
                    Route::get('', [OrderRepresentativesController::class,'index'])->name('index');
                    Route::post('', [OrderRepresentativesController::class,'dataTable'])->name('index');
                    Route::get('create', [OrderRepresentativesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [OrderRepresentativesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [OrderRepresentativesController::class,'importView'])->name('import');
                    Route::post('import', [OrderRepresentativesController::class,'import'])->name('import');
                    Route::get('export', [OrderRepresentativesController::class,'export'])->name('export');
                    Route::get('{id}', [OrderRepresentativesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [OrderRepresentativesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [OrderRepresentativesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [OrderRepresentativesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [OrderRepresentativesController::class,'comment'])->name('comment');
                    Route::post('{id}/notify', [OrderRepresentativesController::class,'notify'])->name('notify');
                    Route::put('{id}/restore', [OrderRepresentativesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'order-types-of-datas', 'as' => 'order-types-of-datas.' ], function () {
                    Route::get('', [OrderTypesOfDatasController::class,'index'])->name('index');
                    Route::post('', [OrderTypesOfDatasController::class,'dataTable'])->name('index');
                    Route::get('create', [OrderTypesOfDatasController::class,'createOrEdit'])->name('create');
                    Route::post('create', [OrderTypesOfDatasController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [OrderTypesOfDatasController::class,'importView'])->name('import');
                    Route::post('import', [OrderTypesOfDatasController::class,'import'])->name('import');
                    Route::get('export', [OrderTypesOfDatasController::class,'export'])->name('export');
                    Route::get('{id}', [OrderTypesOfDatasController::class,'show'])->name('show');
                    Route::get('{id}/edit', [OrderTypesOfDatasController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [OrderTypesOfDatasController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [OrderTypesOfDatasController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [OrderTypesOfDatasController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [OrderTypesOfDatasController::class,'restore'])->name('restore');
                });



                Route::group(['prefix' => 'order-item-qty-updates', 'as' => 'order-item-qty-updates.' ], function () {
                    Route::get('', [OrderItemQtyUpdatesController::class,'index'])->name('index');
                    Route::post('', [OrderItemQtyUpdatesController::class,'dataTable'])->name('index');
                    Route::get('create', [OrderItemQtyUpdatesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [OrderItemQtyUpdatesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [OrderItemQtyUpdatesController::class,'importView'])->name('import');
                    Route::post('import', [OrderItemQtyUpdatesController::class,'import'])->name('import');
                    Route::get('export', [OrderItemQtyUpdatesController::class,'export'])->name('export');
                    Route::get('{id}', [OrderItemQtyUpdatesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [OrderItemQtyUpdatesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [OrderItemQtyUpdatesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [OrderItemQtyUpdatesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [OrderItemQtyUpdatesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [OrderItemQtyUpdatesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'report-reasons', 'as' => 'report-reasons.' ], function () {
                    Route::get('', [ReportReasonsController::class,'index'])->name('index');
                    Route::post('', [ReportReasonsController::class,'dataTable'])->name('index');
                    Route::get('create', [ReportReasonsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [ReportReasonsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [ReportReasonsController::class,'importView'])->name('import');
                    Route::post('import', [ReportReasonsController::class,'import'])->name('import');
                    Route::get('export', [ReportReasonsController::class,'export'])->name('export');
                    Route::get('{id}', [ReportReasonsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [ReportReasonsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ReportReasonsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ReportReasonsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [ReportReasonsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [ReportReasonsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'order-reports', 'as' => 'order-reports.' ], function () {
                    Route::get('', [OrderReportsController::class,'index'])->name('index');
                    Route::post('', [OrderReportsController::class,'dataTable'])->name('index');
                    Route::get('create', [OrderReportsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [OrderReportsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [OrderReportsController::class,'importView'])->name('import');
                    Route::post('import', [OrderReportsController::class,'import'])->name('import');
                    Route::get('export', [OrderReportsController::class,'export'])->name('export');
                    Route::get('{id}', [OrderReportsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [OrderReportsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [OrderReportsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [OrderReportsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [OrderReportsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [OrderReportsController::class,'restore'])->name('restore');
                });



                Route::group(['prefix' => 'delivery-prices', 'as' => 'delivery-prices.' ], function () {
                    Route::get('', [DeliveryPricesController::class,'index'])->name('index');
                    Route::post('', [DeliveryPricesController::class,'dataTable'])->name('index');
                    Route::get('create', [DeliveryPricesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [DeliveryPricesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [DeliveryPricesController::class,'importView'])->name('import');
                    Route::post('import', [DeliveryPricesController::class,'import'])->name('import');
                    Route::get('export', [DeliveryPricesController::class,'export'])->name('export');
                    Route::get('{id}', [DeliveryPricesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [DeliveryPricesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [DeliveryPricesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [DeliveryPricesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [DeliveryPricesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [DeliveryPricesController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'order-schedules', 'as' => 'order-schedules.' ], function () {
                    Route::get('', [OrderSchedulesController::class,'index'])->name('index');
                    Route::post('', [OrderSchedulesController::class,'dataTable'])->name('index');
                    Route::get('create', [OrderSchedulesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [OrderSchedulesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [OrderSchedulesController::class,'importView'])->name('import');
                    Route::post('import', [OrderSchedulesController::class,'import'])->name('import');
                    Route::get('export', [OrderSchedulesController::class,'export'])->name('export');
                    Route::get('{id}', [OrderSchedulesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [OrderSchedulesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [OrderSchedulesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [OrderSchedulesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [OrderSchedulesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [OrderSchedulesController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'order-transactions', 'as' => 'order-transactions.' ], function () {
                    Route::get('', [OrderTransactionsController::class,'index'])->name('index');
                    Route::post('', [OrderTransactionsController::class,'dataTable'])->name('index');
                    Route::get('create', [OrderTransactionsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [OrderTransactionsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [OrderTransactionsController::class,'importView'])->name('import');
                    Route::post('import', [OrderTransactionsController::class,'import'])->name('import');
                    Route::get('export', [OrderTransactionsController::class,'export'])->name('export');
                    Route::get('{id}', [OrderTransactionsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [OrderTransactionsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [OrderTransactionsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [OrderTransactionsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [OrderTransactionsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [OrderTransactionsController::class,'restore'])->name('restore');
                });
                //{{ new_routes}}



            });
        });
    }
);





<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\B2B\Controllers\FrontEnd\BranchesController;
use Core\B2B\Controllers\FrontEnd\AuthController;
use Core\B2B\Controllers\FrontEnd\DashboardController;
use Core\B2B\Controllers\FrontEnd\ScheduleController;
use Core\B2B\Controllers\FrontEnd\EmployeeController;



Route::group(
[
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
],
    function () {


        // Authentication routes
        Route::post('/register', [AuthController::class , 'registerStore'])->name('client.register.store');
        Route::group(['prefix' => '', 'middleware' => 'guest', 'as' => 'client.'], function () {
            // Route::get('/register', [AuthController::class, 'register'])->name('register');
            Route::get('/login', [AuthController::class , 'login'])->name('login');
            Route::post('/login', [AuthController::class , 'loginStore'])->name('login.store');
        }
        );
        Route::group(['prefix' => 'business', 'as' => 'client.', 'middleware' => ['auth', 'is_b2b_client']], function () {
            Route::post('/logout', [AuthController::class , 'logout'])->name('logout');
            Route::get('/dashboard', [DashboardController::class , 'dashboard'])->name('dashboard');
            Route::get('/analytics', [DashboardController::class , 'analytics'])->name('analytics');
            Route::get('/clientsOrders', [DashboardController::class , 'clientsOrders'])->name('clientsOrders');
            Route::get('/clientsOrders/data', [DashboardController::class , 'clientsOrdersData'])->name('clientsOrders.data');
            Route::get('/monthly-invoices', [DashboardController::class , 'monthlyInvoices'])->name('monthly-invoices');
            Route::get('/monthly-invoices/{year}/{month}', [DashboardController::class , 'monthlyInvoiceDetails'])->name('monthly-invoice-details');
            Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
                    Route::get('', [DashboardController::class , 'orders'])->name('index');
                    Route::get('/data', [DashboardController::class , 'ordersData'])->name('data');
                    Route::get('/{id}', [DashboardController::class , 'showOrder'])->name('show');
                    Route::get('/{id}/invoice', [DashboardController::class , 'invoice'])->name('invoice');
                    Route::post('', [DashboardController::class , 'orderStore'])->name('store');
                    Route::post('/get-dates-times', [DashboardController::class , 'getDatesTimes'])->name('get-dates-times');
                }
                );
                Route::group(['prefix' => 'schedule', 'as' => 'schedule.'], function () {
                    Route::get('', [ScheduleController::class , 'index'])->name('index');
                    Route::post('', [ScheduleController::class , 'store'])->name('store');
                    Route::delete('/{id}', [ScheduleController::class , 'destroy'])->name('delete');
                }
                );
                Route::get('/points', [DashboardController::class , 'points'])->name('points');
                Route::group(['prefix' => 'contracts', 'as' => 'contracts.'], function () {
                    Route::get('', [DashboardController::class , 'contract'])->name('contract');
                    Route::get('customer-prices', [DashboardController::class , 'customerPrices'])->name('customer-prices');
                    Route::get('customer-prices/search', [DashboardController::class , 'searchProducts'])->name('customer-prices.search');
                    Route::post('customer-prices/bulk', [DashboardController::class , 'customerPricesBulkStore'])->name('customer-prices.bulk');
                    Route::post('customer-prices', [DashboardController::class , 'customerPricesStore'])->name('customer-prices.store');
                    Route::put('customer-prices/{priceId}', [DashboardController::class , 'customerPricesUpdate'])->name('customer-prices.update');
                    Route::delete('customer-prices/{priceId}', [DashboardController::class , 'customerPricesDelete'])->name('customer-prices.delete');
                }
                );
                Route::group(['prefix' => 'branches', 'as' => 'branches.'], function () {
                    Route::get('/', [BranchesController::class , 'branches'])->name('index');
                    Route::post('/', [BranchesController::class , 'store'])->name('store');
                    Route::post('/{id}', [BranchesController::class , 'update'])->name('update');
                    Route::delete('/{id}', [BranchesController::class , 'delete'])->name('delete');
                }
                );
                Route::group(['prefix' => 'employees', 'as' => 'employees.'], function () {
                    Route::get('/', [EmployeeController::class, 'index'])->name('index');
                    Route::post('/', [EmployeeController::class, 'store'])->name('store');
                    Route::put('/{id}', [EmployeeController::class, 'update'])->name('update');
                    Route::post('/{id}/password', [EmployeeController::class, 'updatePassword'])->name('update-password');
                    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('delete');
                });
                Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
                    Route::get('/', [DashboardController::class , 'updateProfile'])->name('update-profile');
                    Route::post('/', [DashboardController::class , 'updateProfileStore'])->name('update-profile.store');
                    Route::post('/password', [DashboardController::class , 'updatePassword'])->name('update-password');
                }
                );
            }
            );
        }
);
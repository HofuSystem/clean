<?php

use Illuminate\Support\Facades\Route;
use Core\Financials\Controllers\Dashboard\FinancialAnalysisController;
use Core\Financials\Controllers\Dashboard\DetailedAnalysisController;
use Core\Financials\Controllers\Dashboard\FixedCostController;
use Core\Financials\Controllers\Dashboard\ElectronicInvoicesController;
use Core\Financials\Controllers\Dashboard\OrderInvoicesController;
use Core\Financials\Controllers\Dashboard\PurchaseItemsController;
use Core\Financials\Controllers\Dashboard\PurchaseProvidersController;
use Core\Financials\Controllers\Dashboard\PurchasesController;
use Core\Financials\Controllers\Dashboard\FinancialsController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::group(['prefix' => 'admin', 'as' => 'dashboard.'], function () {
            Route::group(['middleware' => ['auth', 'active', 'checkPermission']], function () {
                Route::get('financial-analysis', [FinancialAnalysisController::class, 'index'])->name('financial-analysis');
                Route::get('financial-analysis/export-monthly', [FinancialAnalysisController::class, 'exportMonthly'])->name('financial-analysis.export-monthly');
                Route::get('financial-analysis/export-daily', [FinancialAnalysisController::class, 'exportDaily'])->name('financial-analysis.export-daily');
                Route::get('financial-analysis/{year}/{month}/daily', [FinancialAnalysisController::class, 'daily'])->name('financial-analysis.daily');
                Route::post('financial-analysis/store-daily', [FinancialAnalysisController::class, 'storeDaily'])->name('financial-analysis.store-daily');

                Route::get('detailed-analysis', [DetailedAnalysisController::class, 'index'])->name('detailed-analysis');
                Route::post('detailed-analysis/fixed-cost', [DetailedAnalysisController::class, 'storeFixedCost'])->name('detailed-analysis.store-fixed-cost');
                Route::get('detailed-analysis/order-transactions', [DetailedAnalysisController::class, 'getOrderTransactions'])->name('detailed-analysis.order-transactions');
                Route::get('detailed-analysis/order-transactions/export', [DetailedAnalysisController::class, 'exportOrderTransactions'])->name('detailed-analysis.order-transactions.export');

                // Fixed Costs Routes
                Route::group(['prefix' => 'fixed-costs', 'as' => 'fixed-costs.'], function () {
                    Route::get('', [FixedCostController::class, 'index'])->name('index');
                    Route::get('create', [FixedCostController::class, 'create'])->name('create');
                    Route::post('', [FixedCostController::class, 'store'])->name('store');
                    Route::get('{fixedCost}', [FixedCostController::class, 'show'])->name('show');
                    Route::get('{fixedCost}/edit', [FixedCostController::class, 'edit'])->name('edit');
                    Route::put('{fixedCost}', [FixedCostController::class, 'update'])->name('update');
                    Route::delete('{fixedCost}', [FixedCostController::class, 'destroy'])->name('destroy');
                    Route::put('{fixedCost}/restore', [FixedCostController::class, 'restore'])->name('restore');
                });


                // Electronic Invoices Routes
                Route::group(['prefix' => 'electronic-invoices', 'as' => 'electronic-invoices.' ], function () {
                    Route::get('', [ElectronicInvoicesController::class, 'index'])->name('index');
                    Route::get('data-table', [ElectronicInvoicesController::class, 'dataTable'])->name('data-table');
                    Route::get('declaration', [ElectronicInvoicesController::class, 'declaration'])->name('declaration');
                    Route::get('{id}', [ElectronicInvoicesController::class, 'show'])->name('show');
                    Route::get('{id}/download', [ElectronicInvoicesController::class, 'downloadPdf'])->name('download');
                    Route::get('{orderId}/generate', [ElectronicInvoicesController::class, 'generate'])->name('generate');
                });

                // Order Invoices Routes
                Route::group(['prefix' => 'order-invoices', 'as' => 'order-invoices.' ], function () {
                    Route::get('', [OrderInvoicesController::class, 'index'])->name('index');
                    Route::post('', [OrderInvoicesController::class, 'dataTable'])->name('index');
                    Route::get('create', [OrderInvoicesController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [OrderInvoicesController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [OrderInvoicesController::class, 'importView'])->name('import');
                    Route::post('import', [OrderInvoicesController::class, 'import'])->name('import');
                    Route::get('export', [OrderInvoicesController::class, 'export'])->name('export');
                    Route::get('{id}', [OrderInvoicesController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [OrderInvoicesController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [OrderInvoicesController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [OrderInvoicesController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [OrderInvoicesController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [OrderInvoicesController::class, 'restore'])->name('restore');
                });

                // Purchase Providers Routes
                Route::group(['prefix' => 'purchase-providers', 'as' => 'purchase-providers.' ], function () {
                    Route::get('', [PurchaseProvidersController::class, 'index'])->name('index');
                    Route::post('', [PurchaseProvidersController::class, 'dataTable'])->name('index');
                    Route::get('create', [PurchaseProvidersController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [PurchaseProvidersController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [PurchaseProvidersController::class, 'importView'])->name('import');
                    Route::post('import', [PurchaseProvidersController::class, 'import'])->name('import');
                    Route::get('export', [PurchaseProvidersController::class, 'export'])->name('export');
                    Route::get('{id}', [PurchaseProvidersController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [PurchaseProvidersController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [PurchaseProvidersController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [PurchaseProvidersController::class, 'delete'])->name('delete');
                    Route::put('{id}/restore', [PurchaseProvidersController::class, 'restore'])->name('restore');
                });

                // Purchase Items Routes
                Route::group(['prefix' => 'purchase-items', 'as' => 'purchase-items.' ], function () {
                    Route::get('', [PurchaseItemsController::class, 'index'])->name('index');
                    Route::post('', [PurchaseItemsController::class, 'dataTable'])->name('index');
                    Route::get('create', [PurchaseItemsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [PurchaseItemsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [PurchaseItemsController::class, 'importView'])->name('import');
                    Route::post('import', [PurchaseItemsController::class, 'import'])->name('import');
                    Route::get('export', [PurchaseItemsController::class, 'export'])->name('export');
                    Route::get('{id}', [PurchaseItemsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [PurchaseItemsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [PurchaseItemsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [PurchaseItemsController::class, 'delete'])->name('delete');
                    Route::put('{id}/restore', [PurchaseItemsController::class, 'restore'])->name('restore');
                });

                // Purchases Routes
                Route::group(['prefix' => 'purchases', 'as' => 'purchases.' ], function () {
                    Route::get('', [PurchasesController::class, 'index'])->name('index');
                    Route::post('', [PurchasesController::class, 'dataTable'])->name('index');
                    Route::get('create', [PurchasesController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [PurchasesController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [PurchasesController::class, 'importView'])->name('import');
                    Route::post('import', [PurchasesController::class, 'import'])->name('import');
                    Route::get('export', [PurchasesController::class, 'export'])->name('export');
                    Route::get('{id}', [PurchasesController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [PurchasesController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [PurchasesController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [PurchasesController::class, 'delete'])->name('delete');
                    Route::put('{id}/restore', [PurchasesController::class, 'restore'])->name('restore');
                });

                // Financials Routes
                Route::group(['prefix' => 'financials', 'as' => 'financials.' ], function () {
                    Route::get('', [FinancialsController::class, 'index'])->name('index');
                    Route::post('', [FinancialsController::class, 'dataTable'])->name('index');
                    Route::get('create', [FinancialsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [FinancialsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('{id}/edit', [FinancialsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [FinancialsController::class, 'storeOrUpdate'])->name('edit');
                    Route::get('{id}/show', [FinancialsController::class, 'show'])->name('show');
                    Route::delete('{id}/delete', [FinancialsController::class, 'delete'])->name('delete');
                    Route::put('{id}/restore', [FinancialsController::class, 'restore'])->name('restore');
                });
            });
        });
    }
);

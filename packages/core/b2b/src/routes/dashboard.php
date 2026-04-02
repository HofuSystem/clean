<?php

use Core\B2B\Controllers\Dashboard\ContractsController;
use Core\B2B\Controllers\Dashboard\ContractsCustomerPricesController;
use Core\B2B\Controllers\Dashboard\ContractsPricesController;
use Core\B2B\Controllers\Dashboard\CompaniesController;
use Core\B2B\Controllers\Dashboard\CompanyBranchesController;
use Core\B2B\Controllers\Dashboard\CompanyEmployeesController;
use Core\B2B\Controllers\Dashboard\CompanyPermissionsController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Info\Controllers\Dashboard\CountriesController;
use Core\Info\Controllers\Dashboard\CitiesController;
use Core\Info\Controllers\Dashboard\DistrictsController;
use Core\Info\Controllers\Dashboard\NationalitiesController;
use Core\Info\Controllers\Dashboard\FavsController;
use Core\Info\Controllers\Dashboard\MapPointsController;

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
            Route::group(['middleware' => ['auth','active']], function () {
                Route::group(['prefix' => 'contracts', 'as' => 'contracts.' ], function () {
                    Route::get('', [ContractsController::class,'index'])->name('index');
                    Route::post('', [ContractsController::class,'dataTable'])->name('index');
                    Route::get('create', [ContractsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [ContractsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [ContractsController::class,'importView'])->name('import');
                    Route::post('import', [ContractsController::class,'import'])->name('import');
                    Route::get('export', [ContractsController::class,'export'])->name('export');
                    Route::get('qr-codes/form', [ContractsController::class,'qrCodesForm'])->name('qr-codes.form');
                    Route::post('qr-codes/generate', [ContractsController::class,'generateQrCodes'])->name('qr-codes.generate');
                    Route::get('{id}', [ContractsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [ContractsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ContractsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ContractsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [ContractsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [ContractsController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'contracts-prices', 'as' => 'contracts-prices.' ], function () {
                    Route::get('', [ContractsPricesController::class,'index'])->name('index');
                    Route::post('', [ContractsPricesController::class,'dataTable'])->name('index');
                    Route::get('create', [ContractsPricesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [ContractsPricesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [ContractsPricesController::class,'importView'])->name('import');
                    Route::post('import', [ContractsPricesController::class,'import'])->name('import');
                    Route::get('export', [ContractsPricesController::class,'export'])->name('export');
                    Route::get('{id}', [ContractsPricesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [ContractsPricesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ContractsPricesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ContractsPricesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [ContractsPricesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [ContractsPricesController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'contracts-customer-prices', 'as' => 'contracts-customer-prices.' ], function () {
                    Route::get('', [ContractsCustomerPricesController::class,'index'])->name('index');
                    Route::post('', [ContractsCustomerPricesController::class,'dataTable'])->name('index');
                    Route::get('create', [ContractsCustomerPricesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [ContractsCustomerPricesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [ContractsCustomerPricesController::class,'importView'])->name('import');
                    Route::post('import', [ContractsCustomerPricesController::class,'import'])->name('import');
                    Route::get('export', [ContractsCustomerPricesController::class,'export'])->name('export');
                    Route::get('{id}', [ContractsCustomerPricesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [ContractsCustomerPricesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ContractsCustomerPricesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ContractsCustomerPricesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [ContractsCustomerPricesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [ContractsCustomerPricesController::class,'restore'])->name('restore');
                });

            

                Route::group(['prefix' => 'companies', 'as' => 'companies.'], function () {
                    Route::get('search-users', [CompaniesController::class, 'searchUsers'])->name('search-users');
                    Route::get('', [CompaniesController::class, 'index'])->name('index');
                    Route::post('', [CompaniesController::class, 'dataTable'])->name('index');
                    Route::get('create', [CompaniesController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [CompaniesController::class, 'storeOrUpdate'])->name('create');
                    Route::get('{id}', [CompaniesController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [CompaniesController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CompaniesController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CompaniesController::class, 'delete'])->name('delete');
                    Route::put('{id}/restore', [CompaniesController::class, 'restore'])->name('restore');
                });

                Route::group(['prefix' => 'company-branches', 'as' => 'company-branches.'], function () {
                    Route::post('', [CompanyBranchesController::class, 'dataTable'])->name('index');
                    Route::post('create', [CompanyBranchesController::class, 'storeOrUpdate'])->name('create');
                    Route::get('{id}/edit', [CompanyBranchesController::class, 'storeOrUpdate'])->name('edit');
                    Route::put('{id}/edit', [CompanyBranchesController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CompanyBranchesController::class, 'delete'])->name('delete');
                });

                Route::group(['prefix' => 'company-permissions', 'as' => 'company-permissions.'], function () {
                    Route::get('', [CompanyPermissionsController::class, 'index'])->name('index');
                    Route::post('', [CompanyPermissionsController::class, 'dataTable'])->name('index');
                    Route::get('create', [CompanyPermissionsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [CompanyPermissionsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('{id}/edit', [CompanyPermissionsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CompanyPermissionsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CompanyPermissionsController::class, 'delete'])->name('delete');
                });

                Route::group(['prefix' => 'company-employees', 'as' => 'company-employees.'], function () {
                    Route::get('', [CompanyEmployeesController::class, 'index'])->name('index');
                    Route::post('', [CompanyEmployeesController::class, 'dataTable'])->name('index');
                    Route::get('create', [CompanyEmployeesController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [CompanyEmployeesController::class, 'storeOrUpdate'])->name('create');
                    Route::get('{id}/edit', [CompanyEmployeesController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CompanyEmployeesController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CompanyEmployeesController::class, 'delete'])->name('delete');
                });

                //{{ new_routes}}
            });
        });
    }
);





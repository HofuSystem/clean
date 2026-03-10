<?php

use Core\Users\Controllers\Dashboard\PermissionController;
use Core\Users\Controllers\Dashboard\RoleController;
use Core\Users\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Users\Controllers\Dashboard\PermissionsController;
use Core\Users\Controllers\Dashboard\RolesController;
use Core\Users\Controllers\Dashboard\UsersController;
use Core\Users\Controllers\Dashboard\DevicesController;
use Core\Users\Controllers\Dashboard\FavsController;
use Core\Users\Controllers\Dashboard\UserEditRequestsController;
use Core\Users\Controllers\Dashboard\ProfilesController;
use Core\Users\Controllers\Dashboard\BansController;
use Core\Users\Controllers\Dashboard\PointsController;
use Core\Users\Controllers\Dashboard\AddressesController;
use Core\Users\Controllers\Dashboard\CompanyController;
use Core\Users\Controllers\Dashboard\ContractsController;
use Core\Users\Controllers\Dashboard\ContractsPricesController;
use Core\Users\Controllers\Dashboard\ContractsCustomerPricesController;
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
            Route::group(['middleware' => ['auth', 'active', 'checkPermission']], function () {

                Route::group(['prefix' => 'favs', 'as' => 'favs.' ], function () {
                    Route::get('', [FavsController::class,'index'])->name('index');
                    Route::post('', [FavsController::class,'dataTable'])->name('index');
                    Route::get('create', [FavsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [FavsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [FavsController::class,'importView'])->name('import');
                    Route::post('import', [FavsController::class,'import'])->name('import');
                    Route::get('export', [FavsController::class,'export'])->name('export');
                    Route::get('{id}', [FavsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [FavsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [FavsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [FavsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [FavsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [FavsController::class,'restore'])->name('restore');

                });

                Route::group(['prefix' => 'bans', 'as' => 'bans.' ], function () {
                    Route::get('', [BansController::class,'index'])->name('index');
                    Route::post('', [BansController::class,'dataTable'])->name('index');
                    Route::get('create', [BansController::class,'createOrEdit'])->name('create');
                    Route::post('create', [BansController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [BansController::class,'importView'])->name('import');
                    Route::post('import', [BansController::class,'import'])->name('import');
                    Route::get('export', [BansController::class,'export'])->name('export');
                    Route::get('{id}', [BansController::class,'show'])->name('show');
                    Route::get('{id}/edit', [BansController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [BansController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [BansController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [BansController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [BansController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'profiles', 'as' => 'profiles.' ], function () {
                    Route::get('', [ProfilesController::class,'index'])->name('index');
                    Route::post('', [ProfilesController::class,'dataTable'])->name('index');
                    Route::get('create', [ProfilesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [ProfilesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [ProfilesController::class,'importView'])->name('import');
                    Route::post('import', [ProfilesController::class,'import'])->name('import');
                    Route::get('export', [ProfilesController::class,'export'])->name('export');
                    Route::get('{id}', [ProfilesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [ProfilesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ProfilesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ProfilesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [ProfilesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [ProfilesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'devices', 'as' => 'devices.' ], function () {
                    Route::get('', [DevicesController::class,'index'])->name('index');
                    Route::post('', [DevicesController::class,'dataTable'])->name('index');
                    Route::get('create', [DevicesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [DevicesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [DevicesController::class,'importView'])->name('import');
                    Route::post('import', [DevicesController::class,'import'])->name('import');
                    Route::get('export', [DevicesController::class,'export'])->name('export');
                    Route::get('{id}', [DevicesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [DevicesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [DevicesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [DevicesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [DevicesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [DevicesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'users', 'as' => 'users.' ], function () {
                    Route::get('', [UsersController::class,'index'])->name('index');
                    Route::post('', [UsersController::class,'dataTable'])->name('index');
                    Route::get('create', [UsersController::class,'createOrEdit'])->name('create');
                    Route::post('create', [UsersController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [UsersController::class,'importView'])->name('import');
                    Route::post('import', [UsersController::class,'import'])->name('import');
                    Route::get('export', [UsersController::class,'export'])->name('export');
                    Route::get('{id}', [UsersController::class,'show'])->name('show');
                    Route::patch('{id}/update-password', [UsersController::class,'updatePassword'])->name('update-password');
                    Route::get('{id}/edit', [UsersController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [UsersController::class,'storeOrUpdate'])->name('edit');
                    Route::put('{id}/profile/update', [UsersController::class,'updateProfile'])->name('profile.edit');
                    Route::delete('{id}/delete', [UsersController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [UsersController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [UsersController::class,'restore'])->name('restore');

                });

                Route::group(['prefix' => 'user-edit-requests', 'as' => 'user-edit-requests.' ], function () {
                    Route::get('', [UserEditRequestsController::class,'index'])->name('index');
                    Route::post('', [UserEditRequestsController::class,'dataTable'])->name('index');
                    Route::get('create', [UserEditRequestsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [UserEditRequestsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [UserEditRequestsController::class,'importView'])->name('import');
                    Route::post('import', [UserEditRequestsController::class,'import'])->name('import');
                    Route::get('export', [UserEditRequestsController::class,'export'])->name('export');
                    Route::get('{id}', [UserEditRequestsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [UserEditRequestsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [UserEditRequestsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [UserEditRequestsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [UserEditRequestsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [UserEditRequestsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'roles', 'as' => 'roles.' ], function () {
                    Route::get('', [RolesController::class,'index'])->name('index');
                    Route::post('', [RolesController::class,'dataTable'])->name('index');
                    Route::get('create', [RolesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [RolesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [RolesController::class,'importView'])->name('import');
                    Route::post('import', [RolesController::class,'import'])->name('import');
                    Route::get('export', [RolesController::class,'export'])->name('export');
                    Route::get('{id}', [RolesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [RolesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [RolesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [RolesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [RolesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [RolesController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'permissions', 'as' => 'permissions.' ], function () {
                    Route::get('', [PermissionsController::class,'index'])->name('index');
                    Route::post('', [PermissionsController::class,'dataTable'])->name('index');
                    Route::get('create', [PermissionsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [PermissionsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [PermissionsController::class,'importView'])->name('import');
                    Route::post('import', [PermissionsController::class,'import'])->name('import');
                    Route::get('export', [PermissionsController::class,'export'])->name('export');
                    Route::get('{id}', [PermissionsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [PermissionsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [PermissionsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [PermissionsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [PermissionsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [PermissionsController::class,'restore'])->name('restore');
                });



                Route::group(['prefix' => 'points', 'as' => 'points.' ], function () {
                    Route::get('', [PointsController::class,'index'])->name('index');
                    Route::post('', [PointsController::class,'dataTable'])->name('index');
                    Route::get('create', [PointsController::class,'createOrEdit'])->name('create');
                    Route::post('create', [PointsController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [PointsController::class,'importView'])->name('import');
                    Route::post('import', [PointsController::class,'import'])->name('import');
                    Route::get('export', [PointsController::class,'export'])->name('export');
                    Route::get('{id}', [PointsController::class,'show'])->name('show');
                    Route::get('{id}/edit', [PointsController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [PointsController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [PointsController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [PointsController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [PointsController::class,'restore'])->name('restore');
                });

                Route::group(['prefix' => 'addresses', 'as' => 'addresses.'], function () {
                    Route::get('', [AddressesController::class,'index'])->name('index');
                    Route::post('', [AddressesController::class,'dataTable'])->name('index');
                    Route::get('create', [AddressesController::class,'createOrEdit'])->name('create');
                    Route::post('create', [AddressesController::class,'storeOrUpdate'])->name('create');
                    Route::get('import', [AddressesController::class,'importView'])->name('import');
                    Route::post('import', [AddressesController::class,'import'])->name('import');
                    Route::get('export', [AddressesController::class,'export'])->name('export');
                    Route::get('{id}', [AddressesController::class,'show'])->name('show');
                    Route::get('{id}/edit', [AddressesController::class,'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [AddressesController::class,'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [AddressesController::class,'delete'])->name('delete');
                    Route::post('{id}/comment', [AddressesController::class,'comment'])->name('comment');
                    Route::put('{id}/restore', [AddressesController::class,'restore'])->name('restore');
                });
                Route::group(['prefix' => 'company', 'as' => 'company.'], routes: function () {
                    Route::group(['prefix' => 'addresses', 'as' => 'addresses.'], function () {
                        Route::get('', [CompanyController::class,'addresses'])->name('index');
                    });
                    Route::group(['prefix' => 'accounts', 'as' => 'accounts.' ], function () {
                        Route::get('', [CompanyController::class,'accounts'])->name('index');
    
                    });
                    Route::group(['prefix' => 'orders', 'as' => 'orders.' ], function () {
                        Route::get('', [CompanyController::class,'orders'])->name('index');
    
                    });
    
                });
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
                //{{ new_routes}}

            });
        });
    }
);

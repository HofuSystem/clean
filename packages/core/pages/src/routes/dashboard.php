<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Core\Info\Controllers\Dashboard\CountriesController;
use Core\Info\Controllers\Dashboard\CitiesController;
use Core\Info\Controllers\Dashboard\DistrictsController;
use Core\Info\Controllers\Dashboard\NationalitiesController;
use Core\Info\Controllers\Dashboard\FavsController;
use Core\Info\Controllers\Dashboard\StatesController;
use Core\Info\Controllers\Dashboard\CurrenciesController;
use Core\Pages\Controllers\Dashboard\BusinessesController;
use Core\Pages\Controllers\Dashboard\SlidersController;
use Core\Pages\Controllers\Dashboard\SlidesController;
use Core\Pages\Controllers\Dashboard\PagesController;
use Core\Pages\Controllers\Dashboard\SectionsController;
use Core\Pages\Controllers\Dashboard\FeaturesController;
use Core\Pages\Controllers\Dashboard\CountersController;
use Core\Pages\Controllers\Dashboard\FaqsController;
use Core\Pages\Controllers\Dashboard\ContactRequestsController;
use Core\Pages\Controllers\Dashboard\ReasonsController;
use Core\Pages\Controllers\Dashboard\TestimonialsController;
use Core\Pages\Controllers\Dashboard\WorkStepsController;
use Core\Pages\Controllers\Dashboard\ComparisonsController;

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
            Route::group(['middleware' => ['auth', 'active']], function () {

                Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
                    Route::get('', [PagesController::class, 'index'])->name('index');
                    Route::post('', [PagesController::class, 'dataTable'])->name('index');
                    Route::get('create', [PagesController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [PagesController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [PagesController::class, 'importView'])->name('import');
                    Route::post('import', [PagesController::class, 'import'])->name('import');
                    Route::get('export', [PagesController::class, 'export'])->name('export');
                    Route::get('{id}', [PagesController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [PagesController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [PagesController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [PagesController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [PagesController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [PagesController::class, 'restore'])->name('restore');
                });
                Route::group(['prefix' => 'sections', 'as' => 'sections.'], function () {
                    Route::get('', [SectionsController::class, 'index'])->name('index');
                    Route::post('', [SectionsController::class, 'dataTable'])->name('index');
                    Route::get('create', [SectionsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [SectionsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [SectionsController::class, 'importView'])->name('import');
                    Route::post('import', [SectionsController::class, 'import'])->name('import');
                    Route::get('export', [SectionsController::class, 'export'])->name('export');
                    Route::get('{id}', [SectionsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [SectionsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [SectionsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [SectionsController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [SectionsController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [SectionsController::class, 'restore'])->name('restore');
                });

                Route::group(['prefix' => 'features', 'as' => 'features.'], function () {
                    Route::get('', [FeaturesController::class, 'index'])->name('index');
                    Route::post('', [FeaturesController::class, 'dataTable'])->name('index');
                    Route::get('create', [FeaturesController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [FeaturesController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [FeaturesController::class, 'importView'])->name('import');
                    Route::post('import', [FeaturesController::class, 'import'])->name('import');
                    Route::get('export', [FeaturesController::class, 'export'])->name('export');
                    Route::get('{id}', [FeaturesController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [FeaturesController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [FeaturesController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [FeaturesController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [FeaturesController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [FeaturesController::class, 'restore'])->name('restore');
                });

                Route::group(['prefix' => 'contact-requests', 'as' => 'contact-requests.'], function () {
                    Route::get('', [ContactRequestsController::class, 'index'])->name('index');
                    Route::post('', [ContactRequestsController::class, 'dataTable'])->name('index');
                    Route::get('create', [ContactRequestsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [ContactRequestsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [ContactRequestsController::class, 'importView'])->name('import');
                    Route::post('import', [ContactRequestsController::class, 'import'])->name('import');
                    Route::get('export', [ContactRequestsController::class, 'export'])->name('export');
                    Route::get('{id}', [ContactRequestsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ContactRequestsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ContactRequestsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ContactRequestsController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [ContactRequestsController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [ContactRequestsController::class, 'restore'])->name('restore');
                });

                Route::group(['prefix' => 'counters', 'as' => 'counters.'], function () {
                    Route::get('', [CountersController::class, 'index'])->name('index');
                    Route::post('', [CountersController::class, 'dataTable'])->name('index');
                    Route::get('create', [CountersController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [CountersController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [CountersController::class, 'importView'])->name('import');
                    Route::post('import', [CountersController::class, 'import'])->name('import');
                    Route::get('export', [CountersController::class, 'export'])->name('export');
                    Route::get('{id}', [CountersController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [CountersController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [CountersController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [CountersController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [CountersController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [CountersController::class, 'restore'])->name('restore');
                });

                Route::group(['prefix' => 'reasons', 'as' => 'reasons.'], function () {
                    Route::get('', [ReasonsController::class, 'index'])->name('index');
                    Route::post('', [ReasonsController::class, 'dataTable'])->name('index');
                    Route::get('create', [ReasonsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [ReasonsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [ReasonsController::class, 'importView'])->name('import');
                    Route::post('import', [ReasonsController::class, 'import'])->name('import');
                    Route::get('export', [ReasonsController::class, 'export'])->name('export');
                    Route::get('{id}', [ReasonsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ReasonsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ReasonsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ReasonsController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [ReasonsController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [ReasonsController::class, 'restore'])->name('restore');
                });
                 Route::group(['prefix' => 'businesses', 'as' => 'businesses.'], function () {
                    Route::get('', [BusinessesController::class, 'index'])->name('index');
                    Route::post('', [BusinessesController::class, 'dataTable'])->name('index');
                    Route::get('create', [BusinessesController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [BusinessesController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [BusinessesController::class, 'importView'])->name('import');
                    Route::post('import', [BusinessesController::class, 'import'])->name('import');
                    Route::get('export', [BusinessesController::class, 'export'])->name('export');
                    Route::get('{id}', [BusinessesController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [BusinessesController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [BusinessesController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [BusinessesController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [BusinessesController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [BusinessesController::class, 'restore'])->name('restore');
                });
                Route::group(['prefix' => 'faqs', 'as' => 'faqs.'], function () {
                    Route::get('', [FaqsController::class, 'index'])->name('index');
                    Route::post('', [FaqsController::class, 'dataTable'])->name('index');
                    Route::get('create', [FaqsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [FaqsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [FaqsController::class, 'importView'])->name('import');
                    Route::post('import', [FaqsController::class, 'import'])->name('import');
                    Route::get('export', [FaqsController::class, 'export'])->name('export');
                    Route::get('{id}', [FaqsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [FaqsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [FaqsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [FaqsController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [FaqsController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [FaqsController::class, 'restore'])->name('restore');
                });
                Route::group(['prefix' => 'testimonials', 'as' => 'testimonials.'], function () {
                    Route::get('', [TestimonialsController::class, 'index'])->name('index');
                    Route::post('', [TestimonialsController::class, 'dataTable'])->name('index');
                    Route::get('create', [TestimonialsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [TestimonialsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [TestimonialsController::class, 'importView'])->name('import');
                    Route::post('import', [TestimonialsController::class, 'import'])->name('import');
                    Route::get('export', [TestimonialsController::class, 'export'])->name('export');
                    Route::get('{id}', [TestimonialsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [TestimonialsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [TestimonialsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [TestimonialsController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [TestimonialsController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [TestimonialsController::class, 'restore'])->name('restore');
                });
                Route::group(['prefix' => 'work-steps', 'as' => 'work-steps.'], function () {
                    Route::get('', [WorkStepsController::class, 'index'])->name('index');
                    Route::post('', [WorkStepsController::class, 'dataTable'])->name('index');
                    Route::get('create', [WorkStepsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [WorkStepsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [WorkStepsController::class, 'importView'])->name('import');
                    Route::post('import', [WorkStepsController::class, 'import'])->name('import');
                    Route::get('export', [WorkStepsController::class, 'export'])->name('export');
                    Route::get('{id}', [WorkStepsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [WorkStepsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [WorkStepsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [WorkStepsController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [WorkStepsController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [WorkStepsController::class, 'restore'])->name('restore');
                });
                Route::group(['prefix' => 'comparisons', 'as' => 'comparisons.'], function () {
                    Route::get('', [ComparisonsController::class, 'index'])->name('index');
                    Route::post('', [ComparisonsController::class, 'dataTable'])->name('index');
                    Route::get('create', [ComparisonsController::class, 'createOrEdit'])->name('create');
                    Route::post('create', [ComparisonsController::class, 'storeOrUpdate'])->name('create');
                    Route::get('import', [ComparisonsController::class, 'importView'])->name('import');
                    Route::post('import', [ComparisonsController::class, 'import'])->name('import');
                    Route::get('export', [ComparisonsController::class, 'export'])->name('export');
                    Route::get('{id}', [ComparisonsController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ComparisonsController::class, 'createOrEdit'])->name('edit');
                    Route::put('{id}/edit', [ComparisonsController::class, 'storeOrUpdate'])->name('edit');
                    Route::delete('{id}/delete', [ComparisonsController::class, 'delete'])->name('delete');
                    Route::post('{id}/comment', [ComparisonsController::class, 'comment'])->name('comment');
                    Route::put('{id}/restore', [ComparisonsController::class, 'restore'])->name('restore');
                });
                //{{ new_routes}}
            });
        });
    }
);

<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
});

Route::match(['get', 'post'], '/marketing-engine/sync', [\App\Http\Controllers\Api\MarketingEngineSyncController::class, 'sync'])
    ->middleware('marketing.auth');

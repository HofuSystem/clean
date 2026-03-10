<?php

use Core\Settings\Controllers\Api\SettingsController;
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

Route::get('about',[SettingsController::class,'about']);
Route::get('terms',[SettingsController::class,'terms']);
Route::get('policy',[SettingsController::class,'policy']);
Route::get('contact',[SettingsController::class,'contact']);
Route::get('app_settings',[SettingsController::class,'appSettings']);

Route::get('app_config',[SettingsController::class,'appConfig']);

Route::get('app_config_new',[SettingsController::class,'appConfigNew']);


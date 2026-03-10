<?php

use Core\Users\Controllers\Auth\ConfirmPasswordController;
use Core\Users\Controllers\Auth\ForgotPasswordController;
use Core\Users\Controllers\Auth\LoginController;
use Core\Users\Controllers\Auth\RegisterController;
use Core\Users\Controllers\Auth\ResetPasswordController;
use Core\Users\Controllers\Auth\VerificationController;
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

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::group(['prefix' => 'admin'], function () {

            Route::group(['namespace' => "Auth"], function () {

                // Login Routes
                Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login')->middleware('guest');
                Route::post('/login', [LoginController::class, 'login'])->name('login');
                Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

                // Registration Routes
                Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
                Route::post('/register', [RegisterController::class, 'register']);

                // Password Reset Routes
                Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
                Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
                Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
                Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.edit');

                // Email Verification Routes
                Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
                Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
                Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

                // Password Confirmation Routes
                Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
                Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm']);
            });
        });
    }
);

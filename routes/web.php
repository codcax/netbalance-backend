<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailLinkController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\AuthenticateSessionController;

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

Route::post('/register', [RegisterUserController::class, 'store'])
    ->middleware(['guest', 'throttle:3,1'])
    ->name('register');

Route::post('/login', [AuthenticateSessionController::class, 'store'])
    ->middleware(['guest'])
    ->name('login');

Route::post('/logout', [AuthenticateSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
    ->middleware(['guest', 'throttle:3,1'])
    ->name('password.email');

Route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->middleware(['guest', 'throttle:3,1'])
    ->name('password.store');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'store'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/link', [EmailLinkController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/account', [AccountController::class, 'index']);
    Route::put('/account/update', [AccountController::class, 'update']);
    Route::post('/account/deactivate', [AccountController::class, 'destroy']);
});
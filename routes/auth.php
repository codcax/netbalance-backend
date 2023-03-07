<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;


Route::get('/login', function () {
    return redirect(config('app.frontend_url') . '\signin');
});

Route::get('/register', function () {
    return redirect(config('app.frontend_url') . '\signup');
});

Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticateController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/logout', [AuthenticateController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'store'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/send', [VerifyEmailController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

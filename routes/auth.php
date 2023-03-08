<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginUserController;
use App\Http\Controllers\Auth\LogoutUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerifyEmailLinkController;


Route::post('/register', RegisterUserController::class)
    ->middleware(['guest', 'throttle:3,1'])
    ->name('register');

Route::post('/login', LoginUserController::class)
    ->middleware(['guest'])
    ->name('login');

Route::post('/logout', LogoutUserController::class)
    ->middleware('auth')
    ->name('logout');

Route::post('/forgot-password', ForgotPasswordController::class)
    ->middleware(['guest', 'throttle:3,1'])
    ->name('password.email');

Route::post('/reset-password', ResetPasswordController::class)
    ->middleware(['guest', 'throttle:3,1'])
    ->name('password.store');

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/send', VerifyEmailLinkController::class)
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

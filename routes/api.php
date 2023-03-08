<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Account\UserAccountController;
use App\Http\Controllers\Account\UpdateProfileController;
use App\Http\Controllers\Account\ChangePasswordController;
use App\Http\Controllers\Account\DeactivateAccountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum', 'verified', 'user.active')->group(function () {
//     Route::resources(['users' => UserController::class], ['except' => ['create', 'edit', 'store', 'destroy']]);
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/account', UserAccountController::class);
    Route::post('/account/update-profile', [UpdateProfileController::class, 'store']);
    Route::post('/account/update-password', [ChangePasswordController::class, 'store']);
    Route::delete('/account/deactivate', [DeactivateAccountController::class, 'destroy']);
});
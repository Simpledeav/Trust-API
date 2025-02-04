<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\SavingsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Generic\AssetController;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\Admin\SavingsAccountController;
use App\Http\Controllers\User\Auth\VerificationController;
use App\Http\Controllers\User\Auth\ResetPasswordController;

Route::middleware('throttle:100,1')->group(function () {

    Route::post('register', RegisterController::class);
    Route::post('login', [LoginController::class, 'login']);

    // Password reset
    Route::post('password/forgot', [ResetPasswordController::class, 'forgot']);
    Route::post('password/verify', [ResetPasswordController::class, 'verify']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);
});

Route::middleware('auth:api_user')->group(function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::get('assets', [AssetController::class, 'index']);

    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('logout-others', [LoginController::class, 'logoutOtherDevices']);

    Route::middleware('throttle:3,1')->group(function () {
        // Email verification
        Route::post('email/verify', [VerificationController::class, 'verify']);
        Route::post('email/resend', [VerificationController::class, 'resend'])->middleware('throttle:resend');
    });

    Route::middleware(['verified', 'unblocked'])->group(function () {
        Route::get('transactions/fetch', [TransactionController::class, 'index']);
        Route::post('transaction/deposit', [TransactionController::class, 'store']);
        Route::post('transaction/withdraw', [TransactionController::class, 'store']);
        Route::post('transaction/swap', [TransactionController::class, 'store']);

        Route::prefix('savings-accounts')->group(function () {
            Route::get('/', [SavingsAccountController::class, 'index']);
            Route::post('/store', [SavingsController::class, 'store']);

            Route::post('/credit', [SavingsController::class, 'credit']);
            Route::post('/debit', [SavingsController::class, 'debit']);
            Route::get('/balance', [SavingsController::class, 'balance']);

            // ADMIN::::::
            Route::get('/{id}', [SavingsAccountController::class, 'show']);
            Route::post('/', [SavingsAccountController::class, 'store']);
            Route::put('/{id}', [SavingsAccountController::class, 'update']);
            Route::delete('/{id}', [SavingsAccountController::class, 'destroy']);
        });
    });
});
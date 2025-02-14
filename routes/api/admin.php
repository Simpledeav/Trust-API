<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\VerificationController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\TwoFactorLoginController;

Route::post('login', [LoginController::class, 'login']);

// Password reset
Route::post('password/forgot', [ResetPasswordController::class, 'forgot']);
Route::post('password/verify', [ResetPasswordController::class, 'verify']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);

// Two-FA Authentication
Route::middleware('auth.two_fa:0')->group(function () {
    Route::post('verify-two-fa', [TwoFactorLoginController::class, 'verify']);
    Route::post('resend-two-fa', [TwoFactorLoginController::class, 'resend'])->middleware('throttle:resend');
});

Route::middleware('auth:api_admin')->group(function () {
    
    // Email verification
    Route::post('email/verify', [VerificationController::class, 'verify']);
    Route::post('email/resend', [VerificationController::class, 'resend'])->middleware('throttle:resend');

    Route::get('/', function (): JsonResponse {
        return response()->json([
            'message' => 'Welcome to ITrust API!',
            'status' => 'success',
            'time' => now(),
        ]);
    });

});
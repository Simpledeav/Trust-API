<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\User\ArticleController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SavingsController;
use App\Http\Controllers\Generic\AssetController;
use App\Http\Controllers\User\WatchlistController;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\Admin\SavingsAccountController;
use App\Http\Controllers\User\Auth\VerificationController;
use App\Http\Controllers\User\Auth\ResetPasswordController;
use App\Http\Controllers\User\Auth\TwoFactorLoginController;

Route::middleware('throttle:3,1')->group(function () {
    Route::post('register', RegisterController::class);
    Route::post('login', [LoginController::class, 'login']);

    // Password reset
    Route::post('password/forgot', [ResetPasswordController::class, 'forgot']);
    Route::post('password/verify', [ResetPasswordController::class, 'verify']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);
});

Route::middleware('auth:api_user')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->middleware('auth.two_fa');
    Route::get('assets', [AssetController::class, 'index']);

    Route::middleware('throttle:3,1')->group(function () {
        // Logout
        Route::post('logout', [LoginController::class, 'logout']);
        Route::post('logout-others', [LoginController::class, 'logoutOtherDevices']);

        // Two-FA Authentication
        Route::middleware('auth.two_fa:0')->group(function () {
            Route::post('verify-two-fa', [TwoFactorLoginController::class, 'verify']);
            Route::post('resend-two-fa', [TwoFactorLoginController::class, 'resend'])->middleware('throttle:resend');
        });
    });

    // Email verification
    Route::post('email/verify', [VerificationController::class, 'verify']);
    Route::post('email/resend', [VerificationController::class, 'resend'])->middleware('throttle:resend');

    // Full Authentication
    Route::middleware(['auth.two_fa', 'verified', 'unblocked'])->group(function () {
        
        //Profile
        Route::post('profile/password', [ProfileController::class, 'updatePassword']);
        Route::post('profile/two-fa', [ProfileController::class, 'updateTwoFa']);

        // Complete security
        Route::middleware(['password.secure'])->group(function () {

            //Profile
            Route::patch('profile', [ProfileController::class, 'updateProfile']);
            Route::patch('profile/kyc', [ProfileController::class, 'updateKYC']);
            Route::delete('profile', [ProfileController::class, 'destroy']);
            Route::post('/profile/bank', [PaymentController::class, 'updatePayment']);

            //Analytics
            Route::get('analytics', [ProfileController::class, 'analytics']);

            // Transactions
            Route::prefix('transaction')->group(function () {
                Route::get('/fetch', [TransactionController::class, 'index']);
                Route::middleware('throttle:6,1')->group(function () {
                    Route::post('/deposit', [TransactionController::class, 'store']);
                    Route::post('/withdraw', [TransactionController::class, 'store']);
                    Route::post('/swap', [TransactionController::class, 'transfer']);
                });
            });

            // Savings
            Route::prefix('savings-accounts')->group(function () {
                Route::get('/', [SavingsController::class, 'fetch']);
                Route::post('/store', [SavingsController::class, 'store']);

                Route::post('/credit', [SavingsController::class, 'credit']);
                Route::post('/debit', [SavingsController::class, 'debit']);
                Route::get('/balance', [SavingsController::class, 'balance']);
                Route::get('/history', [SavingsController::class, 'index']);

                // ADMIN::::::
                Route::get('/{id}', [SavingsAccountController::class, 'show']);
                Route::post('/', [SavingsAccountController::class, 'store']);
                Route::put('/{id}', [SavingsAccountController::class, 'update']);
                Route::delete('/{id}', [SavingsAccountController::class, 'destroy']);
            });

            // Trades
            Route::prefix('trade')->group(function () {
                Route::get('/', [TradeController::class, 'index']);
                Route::post('/store', [TradeController::class, 'store']);
                Route::put('/toggle/{trade}', [TradeController::class, 'toggleStatus']);
            });

            // Position
            Route::prefix('position')->group(function () {
                Route::get('/', [PositionController::class, 'index']);
                Route::get('/history', [PositionController::class, 'fetchTrades']);
                Route::post('/store', [PositionController::class, 'store']);
                Route::put('/close/{position}', [PositionController::class, 'close']);
            });

            // Watchlist
            Route::prefix('watchlist')->group(function () {
                Route::get('/', [WatchlistController::class, 'index']);
                Route::get('/assets', [WatchlistController::class, 'watchlistAssets']);
                Route::post('/store', [WatchlistController::class, 'store']);
                Route::delete('/destroy/{assetId}', [WatchlistController::class, 'destroy']);
            });

        });
    });
});
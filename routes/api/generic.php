<?php

use App\Http\Controllers\Admin\SettingController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ArticleController;
use App\Http\Controllers\Generic\AssetController;
use App\Http\Controllers\Generic\CurrencyController;
use App\Http\Controllers\Generic\CountryStateCityController;

Route::get('/', function (): JsonResponse {
    return response()->json([
        'message' => 'Welcome to ITrust API!',
        'status' => 'success',
        'time' => now(),
    ]);
});
Route::get('assets', AssetController::class);
Route::get('country', [CountryStateCityController::class, 'countries']);
Route::get('states', [CountryStateCityController::class, 'states']);
Route::get('cities', [CountryStateCityController::class, 'cities']);
Route::get('currencies', CurrencyController::class);

Route::get('settings', [SettingController::class, 'fetchSettings']);

Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{id}', [ArticleController::class, 'show']);               
});
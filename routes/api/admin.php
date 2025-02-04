<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', function (): JsonResponse {
    return response()->json([
        'message' => 'Welcome to Laravel 11 API!',
        'status' => 'success',
        'time' => now(),
    ]);
});
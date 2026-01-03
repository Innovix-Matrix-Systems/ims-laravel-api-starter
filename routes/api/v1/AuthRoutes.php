<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

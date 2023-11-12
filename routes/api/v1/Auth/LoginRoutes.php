<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;

Route::prefix('v1')->group(function () {
    //login
    Route::post('login/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('login/verify-otp', [AuthController::class, 'login']);
    //fallback login(without otp)
    Route::post('login/fallback', [AuthController::class, 'fallbackLogin']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    //logout
    Route::post('logout', [AuthController::class, 'logout']);
});

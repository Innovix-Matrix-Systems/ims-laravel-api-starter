<?php

use App\Http\Controllers\Api\V1\User\UserController;

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    // User Management Roles for admins
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user/profile', [UserController::class, 'getProfileData']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::post('/user', [UserController::class, 'store']);
    Route::patch('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::post('/user/assign-role', [UserController::class, 'assignRole']);
    Route::post('/user/change-password', [UserController::class, 'changePassword']);
    Route::post('/user/export', [UserController::class, 'exportUserData']);

    // Profile
    Route::post('/user/profile/update', [UserController::class, 'updateProfile']);
    Route::post('/user/profile/change-password', [UserController::class, 'changeProfilePassword']);
    Route::post('/user/profile/update-avatar', [UserController::class, 'updateProfileAvatar']);
    // Add more routes as needed
});

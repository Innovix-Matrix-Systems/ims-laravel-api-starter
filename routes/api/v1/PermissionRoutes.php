<?php

use App\Http\Controllers\Api\V1\Permission\PermissionController;

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/permission', [PermissionController::class, 'index']);
    Route::post('/permission', [PermissionController::class, 'store']);
    Route::delete('/permission/{id}', [PermissionController::class, 'destroy']);
    Route::get('/permission/user', [PermissionController::class, 'getUserPermissions']);
    // Add more routes as needed
});

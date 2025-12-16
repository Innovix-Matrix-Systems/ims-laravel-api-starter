<?php

use App\Http\Controllers\Api\V1\Role\RoleController;

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/role', [RoleController::class, 'index']);
    Route::get('/role/{id}', [RoleController::class, 'show']);
    Route::post('/role', [RoleController::class, 'store']);
    Route::patch('/role/{id}', [RoleController::class, 'update']);
    Route::delete('/role/{id}', [RoleController::class, 'destroy']);
    Route::post('/role/assign-permission', [RoleController::class, 'assignPermission']);
    // Add more routes as needed
});

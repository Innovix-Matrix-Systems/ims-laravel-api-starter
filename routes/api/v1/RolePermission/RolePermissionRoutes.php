<?php

use App\Http\Controllers\Api\V1\Auth\PermissionController;
use App\Http\Controllers\Api\V1\Auth\RoleController;

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    //role
    //Route::resource('role', RoleController::class);
    Route::get('/role', [RoleController::class , 'index']);
    Route::get('/role/{id}', [RoleController::class , 'show']);
    Route::post('/role', [RoleController::class , 'store']);
    Route::post('/role/update', [RoleController::class , 'update']);
    Route::delete('/role/{id}', [RoleController::class , 'destroy']);
    Route::post('/role/assign-permission', [RoleController::class, 'assignPermission']);
    //permission
    Route::get('/permission', [PermissionController::class , 'index']);
    Route::post('/permission', [PermissionController::class , 'store']);
    Route::delete('/permission/{id}', [PermissionController::class , 'destroy']);

});

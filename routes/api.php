<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health', HealthCheckJsonResultsController::class);

// Auth Routes
include 'api/v1/AuthRoutes.php';
// Permission Routes
include 'api/v1/PermissionRoutes.php';
// Role Routes
include 'api/v1/RoleRoutes.php';
// User Routes
include 'api/v1/UserRoutes.php';

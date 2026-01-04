<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;

/**
 * Endpoints for checking the health of the application.
 */
Route::get('/health', HealthCheckJsonResultsController::class);

// Auth Routes
include 'api/v1/AuthRoutes.php';
// Permission Routes
include 'api/v1/PermissionRoutes.php';
// Role Routes
include 'api/v1/RoleRoutes.php';
// User Routes
include 'api/v1/UserRoutes.php';
// Data Processing Job Routes
include 'api/v1/DataProcessingJobRoutes.php';

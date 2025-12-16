<?php

use App\Http\Controllers\Auth\ObservabilityAuthController;
use App\Http\Middleware\ObservabilityAuthMiddleware;
use App\Http\Middleware\RestrictApiDocsAccess;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('/', function () {
    return view('welcome');
});

// protect Observability health dashboard (we have public api endpoint for standard api health)
Route::get('health', HealthCheckResultsController::class)->middleware(ObservabilityAuthMiddleware::class);
Route::get('observability-auth/login', [ObservabilityAuthController::class, 'showLogin']);
Route::post('observability-auth/login', [ObservabilityAuthController::class, 'login']);
Route::get('observability-auth/logout', [ObservabilityAuthController::class, 'logout']);
Route::get('observability', [ObservabilityAuthController::class, 'dashboard'])->middleware(ObservabilityAuthMiddleware::class);

// API Documentation routes
Route::middleware(RestrictApiDocsAccess::class)->group(function () {
    Route::get('/docs', [App\Http\Controllers\DocsController::class, 'scalar'])->name('docs.scalar');
    Route::get('/docs/swagger', [App\Http\Controllers\DocsController::class, 'swagger'])->name('docs.swagger');
    Route::get('/docs/openapi.yaml', [App\Http\Controllers\DocsController::class, 'openapi'])->name('docs.openapi');
    Route::get('/docs/collection.json', [App\Http\Controllers\DocsController::class, 'postman'])->name('docs.postman');
});

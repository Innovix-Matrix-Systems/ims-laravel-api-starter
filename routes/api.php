<?php

use App\Http\Controllers\Health\HealthController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//App Health Route

Route::get('/healthz', [HealthController::class, 'healthz']);
Route::get('/http-test', function (Request $request) {
    return response()->json([
        "success" => true,
    ], Response::HTTP_OK);
})->name('http.test');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

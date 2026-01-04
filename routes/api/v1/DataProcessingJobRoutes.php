<?php

use App\Http\Controllers\Api\V1\DataProcessingJob\DataProcessingJobController;

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    // Data Processing Job Management
    Route::get('/data-processing/job/{id}', [DataProcessingJobController::class, 'getJobStatus']);
    Route::get('/data-processing/jobs/user', [DataProcessingJobController::class, 'getUserJobs']);
});

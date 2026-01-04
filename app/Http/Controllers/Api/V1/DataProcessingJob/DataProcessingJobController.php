<?php

namespace App\Http\Controllers\Api\V1\DataProcessingJob;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataProcessing\DataProcessingJobResource;
use App\Services\DataProcessingJob\DataProcessingJobService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Data Processing Job Management
 * APIs for managing data processing jobs (imports/exports)
 */
class DataProcessingJobController extends Controller
{
    const JOB_NOT_FOUND_ERROR_CODE = 'JOB_NOT_FOUND';

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        protected DataProcessingJobService $dataProcessingJobService
    ) {}

    /**
     * Get job status by job ID
     *
     * Retrieve detailed information about a specific data processing job.
     *
     * @urlParam id string required The job ID (UUID). Example: 550e8400-e29b-41d4-a716-446655440000
     *
     * @apiResource App\Http\Resources\DataProcessing\DataProcessingJobResource
     *
     * @apiResourceModel App\Models\DataProcessingJob
     */
    public function getJobStatus(string $id)
    {
        $job = $this->dataProcessingJobService->getJobByJobId($id);

        if (! $job) {
            $this->fail(
                self::JOB_NOT_FOUND_ERROR_CODE,
                __('http-statuses.404'),
                Response::HTTP_NOT_FOUND
            );
        }

        return DataProcessingJobResource::make($job);
    }

    /**
     * Get user's data processing jobs
     *
     * Retrieve all data processing jobs for the authenticated user.
     *
     * @apiResourceCollection App\Http\Resources\DataProcessing\DataProcessingJobResource
     *
     * @apiResourceModel App\Models\DataProcessingJob
     */
    public function getUserJobs(Request $request)
    {
        $userId = auth()->id();
        $jobs = $this->dataProcessingJobService->getUserJobs($userId);

        return DataProcessingJobResource::collection($jobs);
    }
}

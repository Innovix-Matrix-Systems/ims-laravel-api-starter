<?php

namespace App\Services\DataProcessingJob;

use App\DTOs\DataProcessingJob\DataProcessingJobDTO;
use App\Enums\DataProcessingJobStatus;
use App\Models\DataProcessingJob;
use App\Repositories\Contracts\DataProcessingJobRepositoryInterface;
use Illuminate\Support\Collection;

class DataProcessingJobService
{
    public function __construct(
        protected DataProcessingJobRepositoryInterface $repository
    ) {}

    /** Create a new data processing job */
    public function createJob(
        DataProcessingJobDTO $data
    ): DataProcessingJob {
        return $this->repository->create($data->toArray());
    }

    /** Update job status */
    public function updateJobStatus(DataProcessingJob $job, DataProcessingJobStatus $status): DataProcessingJob
    {
        return $this->repository->updateStatus($job, $status);
    }

    /** Update job with processing results */
    public function updateJobResults(
        DataProcessingJob $job,
        int $processedRows,
        int $successCount,
        int $errorCount,
        ?array $errors = null
    ): DataProcessingJob {
        $data = [
            'status' => DataProcessingJobStatus::COMPLETED,
            'processed_rows' => $processedRows,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
            'completed_at' => now(),
        ];

        return $this->repository->update($job, $data);
    }

    /** Update job with error */
    public function updateJobError(DataProcessingJob $job, string $errorMessage): DataProcessingJob
    {
        $data = [
            'status' => DataProcessingJobStatus::FAILED,
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ];

        return $this->repository->update($job, $data);
    }

    /** Update job with export file */
    public function updateJobFile(DataProcessingJob $job, string $fileName, string $filePath): DataProcessingJob
    {
        $data = [
            'file_path' => $filePath,
            'file_name' => $fileName,
            'completed_at' => now(),
        ];

        return $this->repository->update($job, $data);
    }

    /** Find job by ID */
    public function findById(int $id): ?DataProcessingJob
    {
        return $this->repository->findById($id);
    }

    /** Find job by job ID */
    public function findByJobId(string $jobId): ?DataProcessingJob
    {
        return $this->repository->findByJobId($jobId);
    }

    /** Get jobs by user ID */
    public function getByUserId(int $userId): Collection
    {
        return $this->repository->getByUserId($userId);
    }

    /** Get job by job ID */
    public function getJobByJobId(string $jobId): ?DataProcessingJob
    {
        return $this->repository->findByJobId($jobId);
    }

    /** Get user's jobs */
    public function getUserJobs(int $userId): Collection
    {
        return $this->repository->getByUserId($userId);
    }
}

<?php

namespace App\Jobs;

use App\DTOs\User\UserFilterDTO;
use App\Enums\DataProcessingJobStatus;
use App\Exports\UserExport;
use App\Models\DataProcessingJob;
use App\Services\DataProcessingJob\DataProcessingJobService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;

class ProcessUserExport implements ShouldQueue
{
    use Queueable;

    /** Create a new job instance. */
    public function __construct(
        private DataProcessingJob $dataProcessingJob
    ) {}

    /** Execute the job. */
    public function handle(): void
    {
        $dataProcessingJobService = app(DataProcessingJobService::class);
        $errors = [];
        $processedRows = 0;
        $successCount = 0;
        $errorCount = 0;

        try {
            // Update job status to processing
            $dataProcessingJobService->updateJobStatus(
                $this->dataProcessingJob,
                DataProcessingJobStatus::PROCESSING
            );

            $filters = $this->dataProcessingJob->filters ?
                UserFilterDTO::fromArray($this->dataProcessingJob->filters) :
                new UserFilterDTO;

            $export = new UserExport($filters);
            $fileName = 'user_export_' . $this->dataProcessingJob->job_id . '.xlsx';
            $filePath = 'exports/' . $fileName;

            // Store the export file
            Excel::store($export, $filePath, 'public');

            // For export jobs, we consider the entire operation as successful if file was created
            $successCount = 1;
            $processedRows = 1;

            // Update job with file info and results
            $dataProcessingJobService->updateJobFile(
                $this->dataProcessingJob,
                $fileName,
                $filePath
            );

            // Update job results with export statistics
            $dataProcessingJobService->updateJobResults(
                $this->dataProcessingJob,
                $processedRows,
                $successCount,
                $errorCount,
                $errors
            );

            // Update job status to completed
            $dataProcessingJobService->updateJobStatus(
                $this->dataProcessingJob,
                DataProcessingJobStatus::COMPLETED
            );

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errors[] = [
                'type' => 'export_error',
                'message' => $errorMessage,
                'timestamp' => now()->toDateTimeString(),
            ];

            // Update job with error and results
            $dataProcessingJobService->updateJobError(
                $this->dataProcessingJob,
                $errorMessage
            );

            $dataProcessingJobService->updateJobResults(
                $this->dataProcessingJob,
                $processedRows,
                $successCount,
                $errorCount + 1,
                $errors
            );

            throw $e;
        }
    }
}

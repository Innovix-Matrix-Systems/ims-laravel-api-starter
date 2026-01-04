<?php

namespace App\Jobs;

use App\Enums\DataProcessingJobStatus;
use App\Imports\UserImport;
use App\Models\DataProcessingJob;
use App\Services\DataProcessingJob\DataProcessingJobService;
use App\Services\User\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessUserImport implements ShouldQueue
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

            $filePath = $this->dataProcessingJob->file_path;

            if (! Storage::disk('public')->exists($filePath)) {
                $errorMessage = 'Import file not found: ' . $filePath;
                $errors[] = [
                    'type' => 'file_error',
                    'message' => $errorMessage,
                    'timestamp' => now()->toDateTimeString(),
                ];

                $dataProcessingJobService->updateJobError(
                    $this->dataProcessingJob,
                    $errorMessage
                );

                // Update job results with error information
                $dataProcessingJobService->updateJobResults(
                    $this->dataProcessingJob,
                    $processedRows,
                    $successCount,
                    $errorCount + 1,
                    $errors
                );

                return;
            }

            $userService = app(UserService::class);
            $import = new UserImport($userService);

            Excel::import($import, Storage::disk('public')->path($filePath));

            // Get results from import
            $processedRows = $import->getProcessedRows();
            $successCount = $import->getSuccessCount();
            $errorCount = $import->getErrorCount();
            $importErrors = $import->getErrors();

            // Combine import errors with any system errors
            if (! empty($importErrors)) {
                $errors = array_merge($errors, $importErrors);
            }

            // Update job with results
            $dataProcessingJobService->updateJobResults(
                $this->dataProcessingJob,
                $processedRows,
                $successCount,
                $errorCount,
                $errors
            );

            // Clean up the uploaded file after processing
            Storage::disk('public')->delete($filePath);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errors[] = [
                'type' => 'system_error',
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

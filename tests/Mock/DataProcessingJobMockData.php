<?php

namespace Tests\Mock;

use App\Enums\DataProcessingJobStatus;
use App\Enums\DataProcessingJobType;

class DataProcessingJobMockData
{
    public static function getJobData(): array
    {
        return [
            'type' => DataProcessingJobType::IMPORT,
            'entity_type' => 'User',
            'user_id' => 1,
            'file_path' => 'imports/test.csv',
            'original_file_name' => 'test.csv',
        ];
    }

    public static function getExportJobData(): array
    {
        return [
            'type' => DataProcessingJobType::EXPORT,
            'entity_type' => 'User',
            'user_id' => 1,
            'file_path' => null,
            'original_file_name' => null,
        ];
    }

    public static function getJobResultsData(): array
    {
        return [
            'processed_rows' => 100,
            'success_count' => 95,
            'error_count' => 5,
            'errors' => ['Row 1: Invalid email', 'Row 2: Missing name'],
            'status' => DataProcessingJobStatus::COMPLETED,
        ];
    }

    public static function getJobErrorData(): array
    {
        return [
            'error_message' => 'Failed to process file: Invalid format',
            'status' => DataProcessingJobStatus::FAILED,
        ];
    }

    public static function getJobFileData(): array
    {
        return [
            'file_name' => 'users_export_2024.xlsx',
            'file_path' => 'exports/users_export_2024.xlsx',
        ];
    }
}

<?php

namespace App\DTOs\DataProcessingJob;

use App\Enums\DataProcessingJobStatus;
use App\Enums\DataProcessingJobType;
use App\Models\DataProcessingJob;
use Illuminate\Http\Request;

class DataProcessingJobDTO
{
    public function __construct(
        public readonly ?string $jobId,
        public readonly ?DataProcessingJobType $type,
        public readonly ?DataProcessingJobStatus $status,
        public readonly ?string $entityType,
        public readonly ?string $filePath,
        public readonly ?string $fileName,
        public readonly ?string $originalFileName,
        public readonly ?array $filters,
        public readonly ?array $errors,
        public readonly ?int $processedRows,
        public readonly ?int $successCount,
        public readonly ?int $errorCount,
        public readonly ?string $errorMessage,
        public readonly ?int $userId,
        public readonly ?\DateTime $startedAt,
        public readonly ?\DateTime $completedAt,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('job_id'),
            $request->input('type') ? DataProcessingJobType::from($request->input('type')) : null,
            $request->input('status') ? DataProcessingJobStatus::from($request->input('status')) : null,
            $request->input('entity_type'),
            $request->input('file_path'),
            $request->input('file_name'),
            $request->input('original_file_name'),
            $request->input('filters') ? json_decode($request->input('filters'), true) : null,
            $request->input('errors') ? json_decode($request->input('errors'), true) : null,
            $request->input('processed_rows'),
            $request->input('success_count'),
            $request->input('error_count'),
            $request->input('error_message'),
            $request->input('user_id'),
            $request->input('started_at') ? new \DateTime($request->input('started_at')) : null,
            $request->input('completed_at') ? new \DateTime($request->input('completed_at')) : null,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['job_id'] ?? null,
            isset($data['type']) ? ($data['type'] instanceof DataProcessingJobType ? $data['type'] : DataProcessingJobType::from($data['type'])) : null,
            isset($data['status']) ? ($data['status'] instanceof DataProcessingJobStatus ? $data['status'] : DataProcessingJobStatus::from($data['status'])) : null,
            $data['entity_type'] ?? null,
            $data['file_path'] ?? null,
            $data['file_name'] ?? null,
            $data['original_file_name'] ?? null,
            $data['filters'] ?? null,
            $data['errors'] ?? null,
            $data['processed_rows'] ?? null,
            $data['success_count'] ?? null,
            $data['error_count'] ?? null,
            $data['error_message'] ?? null,
            $data['user_id'] ?? null,
            isset($data['started_at']) ? ($data['started_at'] instanceof \DateTime ? $data['started_at'] : new \DateTime($data['started_at'])) : null,
            isset($data['completed_at']) ? ($data['completed_at'] instanceof \DateTime ? $data['completed_at'] : new \DateTime($data['completed_at'])) : null,
        );
    }

    public static function fromModel(DataProcessingJob $model): self
    {
        return new self(
            $model->job_id,
            $model->type,
            $model->status,
            $model->entity_type,
            $model->file_path,
            $model->file_name,
            $model->original_file_name,
            $model->filters,
            $model->errors,
            $model->processed_rows,
            $model->success_count,
            $model->error_count,
            $model->error_message,
            $model->user_id,
            $model->started_at,
            $model->completed_at,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'job_id' => $this->jobId,
            'type' => $this->type?->value,
            'status' => $this->status?->value,
            'entity_type' => $this->entityType,
            'file_path' => $this->filePath,
            'file_name' => $this->fileName,
            'original_file_name' => $this->originalFileName,
            'filters' => $this->filters,
            'errors' => $this->errors,
            'processed_rows' => $this->processedRows,
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'error_message' => $this->errorMessage,
            'user_id' => $this->userId,
            'started_at' => $this->startedAt?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completedAt?->format('Y-m-d H:i:s'),
        ], fn ($value) => ! is_null($value));
    }
}

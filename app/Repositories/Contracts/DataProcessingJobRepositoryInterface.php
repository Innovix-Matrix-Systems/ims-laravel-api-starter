<?php

namespace App\Repositories\Contracts;

use App\Enums\DataProcessingJobStatus;
use App\Enums\DataProcessingJobType;
use App\Models\DataProcessingJob;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DataProcessingJobRepositoryInterface
{
    public function getAllPaginated(): LengthAwarePaginator;

    public function getAll(): Collection;

    public function findById(int $id): ?DataProcessingJob;

    public function findByJobId(string $jobId): ?DataProcessingJob;

    public function create(array $data): DataProcessingJob;

    public function update(DataProcessingJob $job, array $data): DataProcessingJob;

    public function updateStatus(DataProcessingJob $job, DataProcessingJobStatus $status): DataProcessingJob;

    public function delete(DataProcessingJob $job): bool;

    public function getByUserId(int $userId): Collection;

    public function getByType(DataProcessingJobType $type): Collection;

    public function getByStatus(DataProcessingJobStatus $status): Collection;
}

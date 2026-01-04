<?php

namespace App\Repositories\DataProcessingJob;

use App\Enums\DataProcessingJobStatus;
use App\Enums\DataProcessingJobType;
use App\Models\DataProcessingJob;
use App\Repositories\Contracts\DataProcessingJobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DataProcessingJobRepository implements DataProcessingJobRepositoryInterface
{
    public function getAllPaginated(): LengthAwarePaginator
    {
        return DataProcessingJob::paginate();
    }

    public function getAll(): Collection
    {
        return DataProcessingJob::all();
    }

    public function findById(int $id): ?DataProcessingJob
    {
        return DataProcessingJob::find($id);
    }

    public function findByJobId(string $jobId): ?DataProcessingJob
    {
        return DataProcessingJob::where('job_id', $jobId)->first();
    }

    public function create(array $data): DataProcessingJob
    {
        if (! isset($data['job_id'])) {
            $jobId = Str::uuid()->toString();
            $data['job_id'] = $jobId;
        } else {
            $jobId = $data['job_id'];
        }

        if (! isset($data['status'])) {
            $data['status'] = DataProcessingJobStatus::PENDING;
        }

        $job = new DataProcessingJob;
        $job->fill($data);
        $job->save();

        return $job;
    }

    public function update(DataProcessingJob $job, array $data): DataProcessingJob
    {
        $job->fill($data);
        $job->save();

        return $job;
    }

    public function updateStatus(DataProcessingJob $job, DataProcessingJobStatus $status): DataProcessingJob
    {

        $job->status = $status;

        if ($status === DataProcessingJobStatus::PROCESSING) {
            $job->started_at = now();
        } elseif (in_array($status, [DataProcessingJobStatus::COMPLETED, DataProcessingJobStatus::FAILED])) {
            $job->completed_at = now();
        }

        $job->save();

        return $job;

    }

    public function delete(DataProcessingJob $job): bool
    {
        return $job->delete();
    }

    public function getByUserId(int $userId): Collection
    {
        return DataProcessingJob::where('user_id', $userId)->get();
    }

    public function getByType(DataProcessingJobType $type): Collection
    {
        return DataProcessingJob::where('type', $type->value)->get();
    }

    public function getByStatus(DataProcessingJobStatus $status): Collection
    {
        return DataProcessingJob::where('status', $status->value)->get();
    }
}

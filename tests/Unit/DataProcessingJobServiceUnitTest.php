<?php

namespace Tests\Unit;

use App\DTOs\DataProcessingJob\DataProcessingJobDTO;
use App\Enums\DataProcessingJobStatus;
use App\Models\DataProcessingJob;
use App\Repositories\Contracts\DataProcessingJobRepositoryInterface;
use App\Services\DataProcessingJob\DataProcessingJobService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\Mock\DataProcessingJobMockData;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->repository = Mockery::mock(DataProcessingJobRepositoryInterface::class);
    $this->service = new DataProcessingJobService($this->repository);
});

test('createJob creates a new data processing job', function () {
    $data = DataProcessingJobMockData::getJobData();
    $dto = DataProcessingJobDTO::fromArray($data);
    $job = Mockery::mock(DataProcessingJob::class);

    $this->repository->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function ($arg) use ($data) {
            return $arg['type'] === $data['type']->value
                && $arg['entity_type'] === $data['entity_type']
                && $arg['user_id'] === $data['user_id']
                && $arg['file_path'] === $data['file_path']
                && $arg['original_file_name'] === $data['original_file_name'];
        }))
        ->andReturn($job);

    $result = $this->service->createJob($dto);

    expect($result)->toBe($job);
});

test('updateJobStatus updates job status', function () {
    $job = Mockery::mock(DataProcessingJob::class);
    $status = DataProcessingJobStatus::PROCESSING;
    $updatedJob = Mockery::mock(DataProcessingJob::class);

    $this->repository->shouldReceive('updateStatus')
        ->once()
        ->with($job, $status)
        ->andReturn($updatedJob);

    $result = $this->service->updateJobStatus($job, $status);

    expect($result)->toBe($updatedJob);
});

test('updateJobResults updates job with processing results', function () {
    $job = Mockery::mock(DataProcessingJob::class);
    $resultsData = DataProcessingJobMockData::getJobResultsData();
    $updatedJob = Mockery::mock(DataProcessingJob::class);

    $this->repository->shouldReceive('update')
        ->once()
        ->with($job, Mockery::any())
        ->andReturn($updatedJob);

    $result = $this->service->updateJobResults(
        $job,
        $resultsData['processed_rows'],
        $resultsData['success_count'],
        $resultsData['error_count'],
        $resultsData['errors']
    );

    expect($result)->toBe($updatedJob);
});

test('updateJobError updates job with error message', function () {
    $job = Mockery::mock(DataProcessingJob::class);
    $errorData = DataProcessingJobMockData::getJobErrorData();
    $updatedJob = Mockery::mock(DataProcessingJob::class);

    $this->repository->shouldReceive('update')
        ->once()
        ->with($job, Mockery::any())
        ->andReturn($updatedJob);

    $result = $this->service->updateJobError($job, $errorData['error_message']);

    expect($result)->toBe($updatedJob);
});

test('updateJobFile updates job with export file information', function () {
    $job = Mockery::mock(DataProcessingJob::class);
    $fileData = DataProcessingJobMockData::getJobFileData();
    $updatedJob = Mockery::mock(DataProcessingJob::class);

    $expectedData = [
        'file_path' => $fileData['file_path'],
        'file_name' => $fileData['file_name'],
        'completed_at' => Mockery::type(\DateTime::class),
    ];

    $this->repository->shouldReceive('update')
        ->once()
        ->with($job, Mockery::on(function ($data) use ($expectedData) {
            return $data['file_path'] === $expectedData['file_path']
                && $data['file_name'] === $expectedData['file_name']
                && $data['completed_at'] instanceof \DateTime;
        }))
        ->andReturn($updatedJob);

    $result = $this->service->updateJobFile($job, $fileData['file_name'], $fileData['file_path']);

    expect($result)->toBe($updatedJob);
});

test('findById finds job by ID', function () {
    $id = 123;
    $job = Mockery::mock(DataProcessingJob::class);

    $this->repository->shouldReceive('findById')
        ->once()
        ->with($id)
        ->andReturn($job);

    $result = $this->service->findById($id);

    expect($result)->toBe($job);
});

test('findByJobId finds job by job ID', function () {
    $jobId = '550e8400-e29b-41d4-a716-446655440000';
    $job = Mockery::mock(DataProcessingJob::class);

    $this->repository->shouldReceive('findByJobId')
        ->once()
        ->with($jobId)
        ->andReturn($job);

    $result = $this->service->findByJobId($jobId);

    expect($result)->toBe($job);
});

test('getByUserId gets jobs by user ID', function () {
    $userId = 456;
    $jobs = Mockery::mock(Collection::class);

    $this->repository->shouldReceive('getByUserId')
        ->once()
        ->with($userId)
        ->andReturn($jobs);

    $result = $this->service->getByUserId($userId);

    expect($result)->toBe($jobs);
});

test('getJobByJobId gets job by job ID', function () {
    $jobId = '550e8400-e29b-41d4-a716-446655440000';
    $job = Mockery::mock(DataProcessingJob::class);

    $this->repository->shouldReceive('findByJobId')
        ->once()
        ->with($jobId)
        ->andReturn($job);

    $result = $this->service->getJobByJobId($jobId);

    expect($result)->toBe($job);
});

test('getUserJobs gets user jobs', function () {
    $userId = 789;
    $jobs = Mockery::mock(Collection::class);

    $this->repository->shouldReceive('getByUserId')
        ->once()
        ->with($userId)
        ->andReturn($jobs);

    $result = $this->service->getUserJobs($userId);

    expect($result)->toBe($jobs);
});

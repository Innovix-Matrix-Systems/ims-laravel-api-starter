<?php

use App\Enums\DataProcessingJobStatus;
use App\Enums\DataProcessingJobType;
use App\Models\DataProcessingJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $authData = generateAdminUserAndAuthToken();
    $this->adminUser = $authData['user'];
    $this->authToken = $authData['token'];
});

test('can get job status by job ID', function () {
    $job = DataProcessingJob::factory()->create([
        'job_id' => '550e8400-e29b-41d4-a716-446655440000',
        'user_id' => $this->adminUser->id,
        'type' => DataProcessingJobType::IMPORT,
        'status' => DataProcessingJobStatus::PENDING,
        'entity_type' => 'User',
    ]);

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/data-processing/job/' . $job->job_id);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'job_id',
                'type',
                'status',
                'entity_type',
                'file_path',
                'file_name',
                'original_file_name',
                'filters',
                'errors',
                'processed_rows',
                'success_count',
                'error_count',
                'error_message',
                'user_id',
                'started_at',
                'completed_at',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJsonPath('data.job_id', $job->job_id)
        ->assertJsonPath('data.status', DataProcessingJobStatus::PENDING->value);
});

test('returns 404 for non-existent job', function () {
    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/data-processing/job/non-existent-job-id');

    $response->assertStatus(404)
        ->assertJsonStructure([
            'type',
            'title',
            'detail',
            'instance',
            'error_code',
            'timestamp',
        ]);
});

test('can get user data processing jobs', function () {
    DataProcessingJob::factory()->count(3)->create([
        'user_id' => $this->adminUser->id,
    ]);

    // Create a job for another user (should not be returned)
    $otherUser = User::factory()->create();
    DataProcessingJob::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/data-processing/jobs/user');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'job_id',
                    'type',
                    'status',
                    'entity_type',
                    'file_path',
                    'file_name',
                    'original_file_name',
                    'filters',
                    'errors',
                    'processed_rows',
                    'success_count',
                    'error_count',
                    'error_message',
                    'user_id',
                    'started_at',
                    'completed_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    expect(count($response->json('data')))->toBe(3);
});

test('can get user jobs with different statuses', function () {
    // Create jobs with different statuses
    DataProcessingJob::factory()->create([
        'user_id' => $this->adminUser->id,
        'status' => DataProcessingJobStatus::PENDING,
    ]);

    DataProcessingJob::factory()->create([
        'user_id' => $this->adminUser->id,
        'status' => DataProcessingJobStatus::PROCESSING,
    ]);

    DataProcessingJob::factory()->create([
        'user_id' => $this->adminUser->id,
        'status' => DataProcessingJobStatus::COMPLETED,
    ]);

    DataProcessingJob::factory()->create([
        'user_id' => $this->adminUser->id,
        'status' => DataProcessingJobStatus::FAILED,
    ]);

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/data-processing/jobs/user');

    $response->assertStatus(200);

    $jobs = $response->json('data');
    expect(count($jobs))->toBe(4);

    // Check that all statuses are present
    $statuses = array_column($jobs, 'status');
    expect(in_array(DataProcessingJobStatus::PENDING->value, $statuses))->toBeTrue();
    expect(in_array(DataProcessingJobStatus::PROCESSING->value, $statuses))->toBeTrue();
    expect(in_array(DataProcessingJobStatus::COMPLETED->value, $statuses))->toBeTrue();
    expect(in_array(DataProcessingJobStatus::FAILED->value, $statuses))->toBeTrue();
});

test('can get user jobs with different types', function () {
    // Create jobs with different types
    DataProcessingJob::factory()->create([
        'user_id' => $this->adminUser->id,
        'type' => DataProcessingJobType::IMPORT,
    ]);

    DataProcessingJob::factory()->create([
        'user_id' => $this->adminUser->id,
        'type' => DataProcessingJobType::EXPORT,
    ]);

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/data-processing/jobs/user');

    $response->assertStatus(200);

    $jobs = $response->json('data');
    expect(count($jobs))->toBe(2);

    // Check that both types are present
    $types = array_column($jobs, 'type');
    expect(in_array(DataProcessingJobType::IMPORT->value, $types))->toBeTrue();
    expect(in_array(DataProcessingJobType::EXPORT->value, $types))->toBeTrue();
});

test('user jobs include file information when available', function () {
    $job = DataProcessingJob::factory()->create([
        'user_id' => $this->adminUser->id,
        'file_path' => 'exports/users_export_2024.xlsx',
        'file_name' => 'users_export_2024.xlsx',
        'original_file_name' => 'users_export.xlsx',
    ]);

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/data-processing/jobs/user');

    $response->assertStatus(200);

    $jobs = $response->json('data');
    expect($jobs[0]['file_path'])->toBe('exports/users_export_2024.xlsx');
    expect($jobs[0]['file_name'])->toBe('users_export_2024.xlsx');
    expect($jobs[0]['original_file_name'])->toBe('users_export.xlsx');
});

test('user jobs include processing statistics when available', function () {
    $job = DataProcessingJob::factory()->create([
        'user_id' => $this->adminUser->id,
        'processed_rows' => 150,
        'success_count' => 145,
        'error_count' => 5,
        'errors' => ['Row 1: Invalid email', 'Row 2: Missing name'],
    ]);

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/data-processing/jobs/user');

    $response->assertStatus(200);

    $jobs = $response->json('data');
    expect($jobs[0]['processed_rows'])->toBe(150);
    expect($jobs[0]['success_count'])->toBe(145);
    expect($jobs[0]['error_count'])->toBe(5);
    expect($jobs[0]['errors'])->toBe(['Row 1: Invalid email', 'Row 2: Missing name']);
});

test('requires authentication for job status endpoint', function () {
    $response = $this->getJson('/api/v1/data-processing/job/some-job-id');

    $response->assertStatus(401);
});

test('requires authentication for user jobs endpoint', function () {
    $response = $this->getJson('/api/v1/data-processing/jobs/user');

    $response->assertStatus(401);
});

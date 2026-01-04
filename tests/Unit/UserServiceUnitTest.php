<?php

namespace Tests\Unit;

use App\DTOs\User\UserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Exceptions\ApiException;
use App\Jobs\ProcessUserExport;
use App\Jobs\ProcessUserImport;
use App\Models\DataProcessingJob;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\DataProcessingJob\DataProcessingJobService;
use App\Services\User\UserService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\Mock\UserMockData;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
    $this->dataProcessingJobService = Mockery::mock(DataProcessingJobService::class);
    $this->userService = new UserService($this->userRepository, $this->dataProcessingJobService);
});

test('getAllUsers calls repository with filters', function () {
    $filters = new UserFilterDTO(search: 'test');
    $paginator = Mockery::mock(LengthAwarePaginator::class);

    $this->userRepository->shouldReceive('getAllWithFiltersPaginated')
        ->once()
        ->with($filters)
        ->andReturn($paginator);

    $result = $this->userService->getAllUsers($filters);

    expect($result)->toBe($paginator);
});

test('insertUserData creates user via repository', function () {
    $dataArray = UserMockData::getUserData();
    $dto = new UserDTO(
        id: null,
        firstName: null,
        lastName: null,
        name: $dataArray['name'],
        email: $dataArray['email'],
        password: $dataArray['password'],
        phone: $dataArray['phone'],
        isActive: true,
        roles: $dataArray['roles']
    );

    $user = Mockery::mock(User::class);

    $this->userRepository->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function ($arg) use ($dto) {
            return is_array($arg) && $arg['email'] === $dto->email;
        }))
        ->andReturn($user);

    $result = $this->userService->insertUserData($dto);

    expect($result)->toBe($user);
});

test('updateUserData updates user via repository', function () {
    $dataArray = UserMockData::getUpdateUserData();
    $dto = new UserDTO(
        id: 1,
        firstName: null,
        lastName: null,
        name: $dataArray['name'],
        email: $dataArray['email'],
        password: null,
        phone: $dataArray['phone'],
        isActive: null,
        roles: null
    );

    $user = Mockery::mock(User::class);
    $updatedUser = Mockery::mock(User::class);

    $this->userRepository->shouldReceive('update')
        ->once()
        ->with($user, Mockery::type('array'))
        ->andReturn($updatedUser);

    $result = $this->userService->updateUserData($dto, $user);

    expect($result)->toBe($updatedUser);
});

test('updateUserData updates profile via repository', function () {
    $dataArray = UserMockData::getUpdateUserData();
    $dto = new UserDTO(
        id: 1,
        firstName: null,
        lastName: null,
        name: $dataArray['name'],
        email: $dataArray['email'],
        password: null,
        phone: $dataArray['phone'],
        isActive: null,
        roles: null
    );

    $user = Mockery::mock(User::class);
    $updatedUser = Mockery::mock(User::class);

    $this->userRepository->shouldReceive('updateProfile')
        ->once()
        ->with($user, Mockery::type('array'))
        ->andReturn($updatedUser);

    $result = $this->userService->updateUserData($dto, $user, true);

    expect($result)->toBe($updatedUser);
});

test('assignUserRole assigns roles via repository', function () {
    $user = Mockery::mock(User::class);
    $roles = [1, 2];

    $this->userRepository->shouldReceive('assignRole')
        ->once()
        ->with($user, $roles)
        ->andReturn($user);

    $result = $this->userService->assignUserRole($user, $roles);

    expect($result)->toBe($user);
});

test('updateUserPassword throws exception if current password incorrect', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getAttribute')->with('password')->andReturn('hashed_password');

    Hash::shouldReceive('check')
        ->once()
        ->with('wrong_password', 'hashed_password')
        ->andReturn(false);

    $this->userService->updateUserPassword($user, 'new_password', 'wrong_password');
})->throws(ApiException::class);

test('updateUserPassword updates password if current password correct', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getAttribute')->with('password')->andReturn('hashed_password');

    Hash::shouldReceive('check')
        ->once()
        ->with('correct_password', 'hashed_password')
        ->andReturn(true);

    $this->userRepository->shouldReceive('updateUserPassword')
        ->once()
        ->with($user, 'new_password')
        ->andReturn($user);

    $result = $this->userService->updateUserPassword($user, 'new_password', 'correct_password');

    expect($result)->toBe($user);
});

test('updateUserAvatar updates avatar via repository', function () {
    $user = Mockery::mock(User::class);
    $avatar = 'avatar_file';

    $this->userRepository->shouldReceive('updateUserAvatar')
        ->once()
        ->with($user, $avatar)
        ->andReturn($user);

    $result = $this->userService->updateUserAvatar($user, $avatar);

    expect($result)->toBe($user);
});

test('deleteUser throws exception if user is super admin', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('isSuperAdmin')->once()->andReturn(true);

    $this->userService->deleteUser($user);
})->throws(ApiException::class);

test('deleteUser deletes user via repository', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('isSuperAdmin')->once()->andReturn(false);

    $this->userRepository->shouldReceive('delete')
        ->once()
        ->with($user)
        ->andReturn(true);

    $result = $this->userService->deleteUser($user);

    expect($result)->toBeTrue();
});

test('generateImportTemplate returns CSV template content', function () {
    $result = $this->userService->generateImportTemplate();

    expect($result)->toBeString();
    expect($result)->toContain('name,email,phone,password,status,roles');
    expect($result)->toContain('John Doe');
    expect($result)->toContain('Jane Smith');
    expect($result)->toContain('Bob Johnson');
});

test('importUsersBackground creates data processing job and dispatches it', function () {
    // Fake the queue to prevent actual job execution
    Queue::fake();

    $file = Mockery::mock(\Illuminate\Http\UploadedFile::class);
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
    $file->shouldReceive('getClientOriginalExtension')->andReturn('csv');
    $file->shouldReceive('getClientOriginalName')->andReturn('users.csv');
    $file->shouldReceive('storeAs')
        ->with('imports', Mockery::type('string'), 'public')
        ->andReturn('imports/test-uuid.csv');

    $dataProcessingJob = Mockery::mock(DataProcessingJob::class);
    $dataProcessingJob->shouldReceive('getKey')->andReturn(1);

    $this->dataProcessingJobService->shouldReceive('createJob')
        ->once()
        ->andReturn($dataProcessingJob);

    $result = $this->userService->importUsersBackground($file, $user);
    expect($result)->toBe($dataProcessingJob);
    // Assert the background job was dispatched (but not executed)
    Queue::assertPushed(ProcessUserImport::class);
});

test('exportUsersBackground creates data processing job and dispatches it', function () {
    // Fake the queue to prevent actual job execution
    Queue::fake();

    $user = Mockery::mock(User::class);
    $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

    $filters = new UserFilterDTO;
    $dataProcessingJob = Mockery::mock(DataProcessingJob::class);
    $dataProcessingJob->shouldReceive('getKey')->andReturn(1);

    $this->dataProcessingJobService->shouldReceive('createJob')
        ->once()
        ->andReturn($dataProcessingJob);

    $result = $this->userService->exportUsersBackground($filters, $user);
    expect($result)->toBe($dataProcessingJob);
    // Assert the background job was dispatched (but not executed)
    Queue::assertPushed(ProcessUserExport::class);
});

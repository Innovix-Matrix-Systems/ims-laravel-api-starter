<?php

namespace App\Services\User;

use App\DTOs\DataProcessingJob\DataProcessingJobDTO;
use App\DTOs\User\UserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Enums\DataProcessingJobType;
use App\Exceptions\ApiException;
use App\Imports\UserImport;
use App\Jobs\ProcessUserExport;
use App\Jobs\ProcessUserImport;
use App\Models\DataProcessingJob;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\DataProcessingJob\DataProcessingJobService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class UserService
{
    const INCORRECT_PASSWORD_ERROR_CODE = 'INCORRECT_PASSWORD';
    const DELETE_SYSTEM_USER_ERROR_CODE = 'DELETE_SYSTEM_USER_ERROR';

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected DataProcessingJobService $dataProcessingJobService
    ) {}

    public function getAllUsers(UserFilterDTO $filters): LengthAwarePaginator
    {
        return $this->userRepository->getAllWithFiltersPaginated($filters);
    }

    /** Insert user data */
    public function insertUserData(UserDTO $data): User
    {
        $user = $this->userRepository->create($data->toArray());

        return $user;
    }

    /** Update user data */
    public function updateUserData(
        UserDTO $data,
        User $user,
        bool $isProfileUpdate = false
    ): User {

        if ($isProfileUpdate) {
            $user = $this->userRepository->updateProfile($user, $data->toArray());
        } else {
            $user = $this->userRepository->update($user, $data->toArray());
        }

        return $user;
    }

    /** Assign user role */
    public function assignUserRole(User $user, array $roles): User
    {
        $user = $this->userRepository->assignRole($user, $roles);

        return $user;
    }

    /** Update user password */
    public function updateUserPassword(
        User $user,
        string $password,
        ?string $currentPassword,
    ): User {

        if ($currentPassword) {
            if (! Hash::check($currentPassword, $user->password)) {
                throw new ApiException(
                    Response::HTTP_BAD_REQUEST,
                    self::INCORRECT_PASSWORD_ERROR_CODE,
                    trans('messages.password.current.wrong')
                );
            }

        }

        $user = $this->userRepository->updateUserPassword($user, $password);

        return $user;
    }

    /** Update user avatar */
    public function updateUserAvatar(User $user, $avatar): User
    {
        $updatedUser = $this->userRepository->updateUserAvatar($user, $avatar);

        return $updatedUser;
    }

    /** Delete user */
    public function deleteUser(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            throw new ApiException(
                Response::HTTP_BAD_REQUEST,
                self::DELETE_SYSTEM_USER_ERROR_CODE,
                __('messages.user.delete.unalterable.fail')
            );
        }
        $isDeleted = $this->userRepository->delete($user);

        return $isDeleted;
    }

    /** Direct import users from uploaded file */
    public function importUsers(UploadedFile $file): array
    {
        $import = new UserImport($this);

        Excel::import($import, $file);

        return [
            'processed_rows' => $import->getProcessedRows(),
            'success_count' => $import->getSuccessCount(),
            'error_count' => $import->getErrorCount(),
            'errors' => $import->getErrors(),
        ];
    }

    /** Background import users from uploaded file */
    public function importUsersBackground(UploadedFile $file, User $user): DataProcessingJob
    {
        $jobId = Str::uuid()->toString();
        $fileName = 'imports/' . $jobId . '.' . $file->getClientOriginalExtension();

        // Store the file temporarily
        $file->storeAs('imports', $jobId . '.' . $file->getClientOriginalExtension(), 'public');

        // Create data processing job using the service
        $data = [
            'type' => DataProcessingJobType::IMPORT,
            'entity_type' => 'User',
            'user_id' => $user->id,
            'file_path' => $fileName,
            'original_file_name' => $file->getClientOriginalName(),
        ];
        $dataProcessingJobData = DataProcessingJobDTO::fromArray($data);
        $dataProcessingJob = $this->dataProcessingJobService->createJob($dataProcessingJobData);

        // Dispatch the background job
        ProcessUserImport::dispatch($dataProcessingJob);

        return $dataProcessingJob;
    }

    /** Background export users with filters */
    public function exportUsersBackground(UserFilterDTO $filters, User $user): DataProcessingJob
    {

        $data = [
            'type' => DataProcessingJobType::EXPORT,
            'entity_type' => 'User',
            'user_id' => $user->id,
            'filters' => [
                'search' => $filters->search,
                'is_active' => $filters->isActive,
                'role_name' => $filters->roleName,
                'order_by' => $filters->orderBy,
                'order_direction' => $filters->orderDirection,
            ],
        ];
        $dataProcessingJobData = DataProcessingJobDTO::fromArray($data);
        // Create data processing job using the service
        $dataProcessingJob = $this->dataProcessingJobService->createJob($dataProcessingJobData);

        // Dispatch the background job
        ProcessUserExport::dispatch($dataProcessingJob);

        return $dataProcessingJob;
    }

    /** Generate CSV template for user import */
    public function generateImportTemplate(): string
    {
        $headers = ['name', 'email', 'phone', 'password', 'status', 'roles'];

        $sampleData = [
            ['John Doe', 'john@example.com', '+1234567890', 'password123', 'Active', 'Admin,User'],
            ['Jane Smith', 'jane@example.com', '+0987654321', 'password123', 'Active', 'User'],
            ['Bob Johnson', 'bob@example.com', '', 'password123', 'Inactive', 'User'],
        ];

        $csvContent = implode(',', $headers) . "\n";

        foreach ($sampleData as $row) {
            $csvContent .= implode(',', array_map(function ($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        return $csvContent;
    }
}

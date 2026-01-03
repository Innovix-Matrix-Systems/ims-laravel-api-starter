<?php

namespace App\Services\User;

use App\DTOs\User\UserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Exceptions\ApiException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserService
{
    const INCORRECT_PASSWORD_ERROR_CODE = 'INCORRECT_PASSWORD';
    const DELETE_SYSTEM_USER_ERROR_CODE = 'DELETE_SYSTEM_USER_ERROR';

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getAllUsers(UserFilterDTO $filters): LengthAwarePaginator
    {
        return $this->userRepository->getAllWithFiltersPaginated($filters);
    }

    public function insertUserData(UserDTO $data): User
    {
        $user = $this->userRepository->create($data->toArray());

        return $user;
    }

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

    public function assignUserRole(User $user, array $roles): User
    {
        $user = $this->userRepository->assignRole($user, $roles);

        return $user;
    }

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

    public function updateUserAvatar(User $user, $avatar): User
    {
        $updatedUser = $this->userRepository->updateUserAvatar($user, $avatar);

        return $updatedUser;
    }

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
}

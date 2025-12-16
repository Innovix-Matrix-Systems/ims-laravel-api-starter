<?php

namespace App\Repositories\Contracts;

use App\DTOs\User\UserFilterDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function getAllWithFiltersPaginated(UserFilterDTO $filters): LengthAwarePaginator;

    public function getAllWithFilters(UserFilterDTO $filters): Collection;

    public function findById(int $id): ?User;

    public function findByIdWithRoles(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function updateProfile(User $user, array $data): User;

    public function updateUserAvatar(User $user, $avatar): User;

    public function updateUserPassword(User $user, string $password): User;

    public function delete(User $user): bool;

    public function assignRole(User $user, array $roles): User;
}

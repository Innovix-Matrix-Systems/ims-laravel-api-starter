<?php

namespace App\Repositories\User;

use App\DTOs\User\UserFilterDTO;
use App\Enums\MediaCollection;
use App\Enums\UserStatus;
use App\Helpers\EloquentFilterHelper;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(array $data): User
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles'], $data['password_confirmation']);

        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_active'] ??= UserStatus::ACTIVE->value;
        $data['created_by'] = auth()->user()->id;

        return DB::transaction(function () use ($data, $roles) {
            $user = new User;
            $user->fill($data);
            $user->save();

            if (! empty($roles)) {
                $user->assignRole($roles);
            }

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        unset($data['roles'], $data['password_confirmation']);

        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        }

        $data['updated_by'] = auth()->user()->id;

        return DB::transaction(function () use ($user, $data) {
            $user->fill($data);
            $user->save();

            return $user;
        });
    }

    public function updateProfile(User $user, array $data): User
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles'], $data['password_confirmation'], $data['password']);

        return DB::transaction(function () use ($user, $data, $roles) {
            $user->fill($data);
            $user->save();

            if (! empty($roles)) {
                $user->syncRoles($roles);
            }

            return $user;
        });
    }

    public function updateUserAvatar(User $user, $avatar): User
    {
        $user->addMedia($avatar)->toMediaCollection(MediaCollection::PROFILE->value);
        $user->updated_by = auth()->id();
        $user->save();

        return $user;
    }

    public function updateUserPassword(User $user, string $password): User
    {
        $user->password = Hash::make($password);
        $user->updated_by = auth()->id();
        $user->save();

        return $user;
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByIdWithRoles(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    public function getAllWithFiltersPaginated(UserFilterDTO $filters): LengthAwarePaginator
    {
        return $this->buildFilterQuery($filters)
            ->paginate($filters->perPage);
    }

    public function getAllWithFilters(UserFilterDTO $filters): Collection
    {
        return $this->buildFilterQuery($filters)
            ->get();
    }

    public function assignRole(User $user, array $roles): User
    {
        return DB::transaction(function () use ($user, $roles) {
            $user->updated_by = auth()->id();
            $user->save();
            $user->syncRoles($roles);

            return $user;
        });
    }

    private function buildFilterQuery(UserFilterDTO $filters): Builder
    {
        $searchFields = ['name', 'email', 'phone'];
        $selectFields = [
            'is_active' => $filters->isActive,
        ];
        $relationSelectFields = ['name' => $filters->roleName];

        $query = User::with('roles');

        $query = EloquentFilterHelper::applyFilters(
            $filters->search,
            $searchFields,
            $selectFields,
            $query
        );

        if ($filters->roleName) {
            $query = EloquentFilterHelper::applyRelationSelectFilters(
                'roles',
                $query,
                $relationSelectFields,
            );
        }

        return $query->orderBy($filters->orderBy, $filters->orderDirection);
    }
}

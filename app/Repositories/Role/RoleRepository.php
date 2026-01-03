<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function create(array $attributes): Role
    {
        return Role::create($attributes);
    }

    public function update(Role $role, array $attributes): Role
    {
        $role->update($attributes);

        return $role;
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }
}

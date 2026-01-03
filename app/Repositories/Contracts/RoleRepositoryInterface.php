<?php

namespace App\Repositories\Contracts;

use App\Models\Role;

interface RoleRepositoryInterface
{
    public function create(array $attributes): Role;

    public function update(Role $role, array $attributes): Role;

    public function delete(Role $role): bool;
}

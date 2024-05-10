<?php

namespace App\Traits;

trait RolePermissionTrait
{
    public function getRolePermissions()
    {
        return [
            [
                'name' => 'role.view',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'role',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'role.view.all',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'role',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'role.create',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'role',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'role.update',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'role',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'role.delete',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'role',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
    }

    public function getUserPermissions(array $excludePermissions = [])
    {
        $permissions = [
            [
                'name' => 'user.view',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user.view.all',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user.create',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user.update',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user.delete',
                'guard_name' => config('constants.GUARD_NAME'),
                'group' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Filter out excluded permissions
        foreach ($excludePermissions as $excludePermission) {
            $permissions = array_filter($permissions, function ($permission) use ($excludePermission) {
                return $permission['name'] !== $excludePermission;
            });
        }

        return $permissions;
    }
}

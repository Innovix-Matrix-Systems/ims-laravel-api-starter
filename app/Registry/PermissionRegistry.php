<?php

namespace App\Registry;

class PermissionRegistry
{
    public static function getRolePermissions(array $excludePermissions = []): array
    {
        $permissions = array_map(function ($permission) {
            return [
                ...$permission,
                'guard_name' => config('auth.defaults.guard'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, config('permission-registry.role.permissions', []));

        foreach ($excludePermissions as $excludePermission) {
            $permissions = array_filter($permissions, function ($permission) use ($excludePermission) {
                return $permission['name'] !== $excludePermission;
            });
        }

        return array_values($permissions);
    }

    public static function getUserPermissions(array $excludePermissions = []): array
    {
        $permissions = array_map(function ($permission) {
            return [
                ...$permission,
                'guard_name' => config('auth.defaults.guard'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, config('permission-registry.user.permissions', []));

        foreach ($excludePermissions as $excludePermission) {
            $permissions = array_filter($permissions, function ($permission) use ($excludePermission) {
                return $permission['name'] !== $excludePermission;
            });
        }

        return array_values($permissions);
    }

    public static function getAllModulePermissions(): array
    {
        return [
            ...self::getRolePermissions(),
            ...self::getUserPermissions(),
        ];
    }
}

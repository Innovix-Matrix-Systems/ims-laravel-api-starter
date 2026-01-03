<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\Role;
use App\Registry\PermissionRegistry;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Role::create([
            'guard_name' => config('auth.defaults.guard'),
            'name' => UserRole::SUPER_ADMIN,
        ]);

        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create([
            'guard_name' => config('auth.defaults.guard'),
            'name' => UserRole::ADMIN,
        ]);
        $admin->givePermissionTo([
            ...array_column(PermissionRegistry::getRolePermissions([
                'role.update',
                'role.delete',
            ]), 'name'),
            ...array_column(PermissionRegistry::getUserPermissions([
                'user.delete',
            ]), 'name'),
        ]);

        Role::create([
            'guard_name' => config('auth.defaults.guard'),
            'name' => UserRole::USER,
        ]);
    }
}

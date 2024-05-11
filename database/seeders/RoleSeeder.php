<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Traits\RolePermissionTrait;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use RolePermissionTrait;
    private $guard = "sanctum";
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Role::create([
            'guard_name' => config('constants.GUARD_NAME'),
            'name' => UserRole::SUPER_ADMIN
        ]);

        $superAdmin->givePermissionTo(Permission::all());

        $admin =Role::create([
            'guard_name' => config('constants.GUARD_NAME'),
            'name' => UserRole::ADMIN
        ]);
        $admin->givePermissionTo([
            'role.view.all',
            ...array_column($this->getUserPermissions(['user.delete']), 'name'),
        ]);

        Role::create([
            'guard_name' => config('constants.GUARD_NAME'),
            'name' => UserRole::USER
        ]);
    }
}

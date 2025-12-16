<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Registry\PermissionRegistry;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        $permissions = [
            // role
            ...PermissionRegistry::getRolePermissions(),
            // user
            ...PermissionRegistry::getUserPermissions(),

        ];
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::insert($permissions);
    }
}

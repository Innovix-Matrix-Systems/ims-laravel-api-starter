<?php

namespace Database\Seeders;

use App\Traits\RolePermissionTrait;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    use RolePermissionTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            //role
            ...$this->getRolePermissions(),
            //user
            ...$this->getUserPermissions(),
        ];
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::insert($permissions);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            //role
            [
                'name' => 'role.view',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'role.view.all',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'role.create',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'role.update',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'role.delete',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            //user
            [
                'name' => 'user.view',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user.view.all',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user.create',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user.update',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user.delete',
                'guard_name' => config('constants.GUARD_NAME'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::insert($permissions);
    }
}

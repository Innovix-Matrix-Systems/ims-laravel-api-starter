<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        Model::unguard(); // Disable mass assignment

        //role seeder with admin & super admin
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        //user Seeder
        $this->call(UserSeeder::class);
        if (app()->environment('local', 'development')) {
            //random user
            \App\Models\User::factory(10)->create();
        }

        Model::reguard(); // Enable mass assignment
    }
}

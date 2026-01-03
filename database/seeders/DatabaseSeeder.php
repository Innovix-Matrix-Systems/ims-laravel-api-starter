<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /** Seed the application's database. */
    public function run(): void
    {
        Model::unguard(); // Disable mass assignment

        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);

        if (app()->environment('local', 'development')) {
            // random user for  development environment
            \App\Models\User::factory(10)->create();
        }

        Model::reguard(); // Enable mass assignment
    }
}

<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {

        // superadmin
        $superAdmin = new User;
        $superAdmin->name = 'Super Admin';
        $superAdmin->user_name = 'super.admin';
        $superAdmin->email = 'superadmin@ims.com';
        $superAdmin->email_verified_at = now();
        $superAdmin->phone = '01700000000';
        $superAdmin->phone_verified_at = now();
        $superAdmin->password = Hash::make(123456);
        $superAdmin->is_active = UserStatus::ACTIVE;
        $superAdmin->last_login_at = now();

        $superAdmin->save();

        $superAdmin->assignRole(UserRole::SUPER_ADMIN);
    }
}

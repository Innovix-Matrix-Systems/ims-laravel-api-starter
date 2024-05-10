<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //superadmin
        $superAdmin = new User();
        $superAdmin->name = $this->faker->firstName. ' '. $this->faker->lastName;
        $superAdmin->user_name = 'super.admin';
        $superAdmin->email = 'superadmin@ims.com';
        $superAdmin->email_verified_at = now();
        $superAdmin->phone = $this->faker->phoneNumber();
        $superAdmin->phone_verified_at = now();
        $superAdmin->password = Hash::make(123456);
        $superAdmin->is_active = UserStatus::ACTIVE;
        $superAdmin->last_login_at = now();
        $superAdmin->save();

        $superAdmin->assignRole(UserRole::SUPER_ADMIN);
    }
}

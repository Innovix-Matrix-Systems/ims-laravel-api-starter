<?php

namespace Database\Seeders;

use App\Http\Traits\UserTrait;
use App\Models\User;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use UserTrait;

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

        //superadmin of Dokani
        $superAdmin = new User();
        $superAdmin->name = $this->faker->firstName. ' '. $this->faker->lastName;
        $superAdmin->user_name = 'super.admin';
        $superAdmin->email = 'superadmin@dokani.com';
        $superAdmin->email_verified_at = now();
        $superAdmin->phone = '01818947266';
        $superAdmin->phone_verified_at = now();
        $superAdmin->password = Hash::make(123456);
        $superAdmin->is_active = $this->USER_ACTIVE;
        $superAdmin->last_login_at = now();
        $superAdmin->save();

        $superAdmin->assignRole($this->SUPER_ADMIN, $this->ADMIN);

        //admin of dokani
        $admin = new User();
        $admin->name = $this->faker->firstName. ' '. $this->faker->lastName;
        $admin->user_name = 'admin';
        $admin->email = 'admin@dokani.com';
        $admin->email_verified_at = now();
        $admin->phone = '01777392602';
        $admin->phone_verified_at = now();
        $admin->password = Hash::make(123456);
        $admin->is_active = $this->USER_ACTIVE;
        $admin->last_login_at = now();
        $admin->save();

        $admin->assignRole($this->ADMIN);

        //users of dokani
        //user 1
        $user1 = new User();
        $user1->user_name = "shafiul.azim";
        $user1->name = "Shafiul Azim";
        $user1->phone = "01859187354";
        $user1->phone_verified_at = now();
        $user1->password = Hash::make(123456);
        $user1->last_active_device = "Samsung A51";
        $user1->last_login_at = now();
        $user1->is_active = $this->USER_ACTIVE;
        $user1->save();

        $user1->assignRole($this->USER);

        //user 2
        $user2 = new User();
        $user2->user_name = "sazid.khan";
        $user2->name = "Sazid Khan";
        $user2->phone = "01533459360";
        $user2->phone_verified_at = now();
        $user2->password = Hash::make(123456);
        $user2->last_active_device = "Xiaomi BalckShark 4";
        $user2->is_active = $this->USER_ACTIVE;
        $user2->last_login_at = now();
        $user2->save();

        $user2->assignRole($this->USER);

        //user 1
        $user3 = new User();
        $user3->user_name = "azizul.hakim";
        $user3->name = "Azizul Hakim";
        $user3->phone = "01777392600";
        $user3->phone_verified_at = now();
        $user3->password = Hash::make(123456);
        $user3->last_active_device = $this->faker->iosMobileToken();
        $user3->is_active = $this->USER_ACTIVE;
        $user3->last_login_at = now();
        $user3->save();

        $user3->assignRole($this->USER);
    }
}

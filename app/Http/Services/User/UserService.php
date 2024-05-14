<?php

namespace App\Http\Services\User;

use App\Enums\UserStatus;
use App\Exceptions\BasicValidationErrorException;
use App\Http\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    const INCORRECT_PASSWORD_ERROR_CODE = 1100;

    public function insertUserData(UserDTO $data): User
    {

        $user = new User();
        $user->first_name = $data->firstName;
        $user->last_name = $data->lastName;
        $user->user_name = $data->name;
        $user->name = $data->name;
        $user->email = $data->email;
        $user->email_verified_at = now();
        $user->password = Hash::make($data->password);
        $user->phone = $data->phone;
        $user->designation = $data->designation;
        $user->address = $data->address;
        $user->is_active = $data->isActive ? UserStatus::ACTIVE : UserStatus::DEACTIVE;
        $user->created_by = auth()->id();

        DB::beginTransaction();
        try {
            $user->save();
            $user->assignRole($data->roles);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $user;
    }


    public function updateUserData(
        UserDTO $data,
        User $user,
        bool $isProfileUpdate = false
    ): User {

        $user->first_name = $data->firstName;
        $user->last_name = $data->lastName;
        $user->name = $data->name;
        $user->email = $data->email;
        $user->phone = $data->phone;
        $user->designation = $data->designation;
        $user->address = $data->address;
        $user->updated_by = auth()->id();
        if(!$isProfileUpdate) {
            $user->is_active = $data->isActive ? UserStatus::ACTIVE : UserStatus::DEACTIVE;
        }
        $user->save();

        return $user;
    }

    public function assignUserRole(User $user, $roles): User
    {
        DB::beginTransaction();
        try {
            $user->updated_by = auth()->id();
            $user->save();
            $user->syncRoles($roles);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $user;
    }

    public function updateUserPassword(
        User $user,
        string $password,
        string|null $currentPassword,
    ): User {

        if($currentPassword) {
            if (!Hash::check($currentPassword, $user->password)) {
                throw new BasicValidationErrorException(
                    Response::HTTP_BAD_REQUEST,
                    self::INCORRECT_PASSWORD_ERROR_CODE,
                    __('Wrong current password! Please try again with the correct password.')
                );
            }

        }

        $user->password = Hash::make($password);
        $user->updated_by = auth()->id();
        $user->save();
        return $user;
    }

}

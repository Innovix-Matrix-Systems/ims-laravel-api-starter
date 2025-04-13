<?php

namespace App\Http\Services\User;

use App\Exceptions\BasicValidationErrorException;
use App\Http\DTOs\UserDTO;
use App\Http\Mappers\UserMapper;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    const INCORRECT_PASSWORD_ERROR_CODE = 1100;

    public function insertUserData(UserDTO $data): User
    {

        $user = UserMapper::toModel($data, new User());

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

        $user = UserMapper::toModel($data, $user, [
            'isProfileUpdate' => $isProfileUpdate,
        ]);
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
        ?string $currentPassword,
    ): User {

        if ($currentPassword) {
            if (! Hash::check($currentPassword, $user->password)) {
                throw new BasicValidationErrorException(
                    Response::HTTP_BAD_REQUEST,
                    self::INCORRECT_PASSWORD_ERROR_CODE,
                    __('messages.password.current.wrong')
                );
            }

        }

        $user->password = Hash::make($password);
        $user->updated_by = auth()->id();
        $user->save();

        return $user;
    }
}

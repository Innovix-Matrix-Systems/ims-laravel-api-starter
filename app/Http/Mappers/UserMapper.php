<?php

namespace App\Http\Mappers;

use App\Enums\UserStatus;
use App\Http\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \App\Http\Mappers\AbstractMapper<User, mixed>
 */
class UserMapper extends AbstractMapper
{
    public static function toModel($dto, Model $model, array $extra = []): Model
    {
        if (! $dto instanceof UserDTO || ! $model instanceof User) {
            throw new \InvalidArgumentException('Invalid DTO or Model type');
        }

        $isCreate = ! $model->exists;

        $model->fill([
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'user_name' => $dto->name,
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'designation' => $dto->designation,
            'address' => $dto->address,
        ]);

        if ($isCreate) {
            $model->email_verified_at = now();
            $model->password = Hash::make($dto->password);
        }

        if (! ($extra['isProfileUpdate'] ?? false)) {
            $model->is_active = $dto->isActive ? UserStatus::ACTIVE->value : UserStatus::DEACTIVE->value;
        }

        self::fillAuditFields($model, $isCreate);

        return $model;
    }
}

<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'Super-Admin';
    case ADMIN = 'Admin';
    case USER = 'User';

    public static function fromId(int $id): self
    {
        return match ($id) {
            1 => self::SUPER_ADMIN,
            2 => self::ADMIN,
            3 => self::USER,
            default => throw new \InvalidArgumentException("Invalid role ID: $id"),
        };
    }

    public function id(): int
    {
        return match ($this) {
            self::SUPER_ADMIN => 1,
            self::ADMIN => 2,
            self::USER => 3,
        };
    }
}

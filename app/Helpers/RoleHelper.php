<?php

namespace App\Helpers;

use App\Enums\UserRole;
use App\Enums\UserRoleID;

class RoleHelper
{
    /**
     * Returns a UserRole enum value based on the given role ID.
     *
     * @param string $id The role ID.
     *
     * @return UserRole The corresponding enum value for the given role ID.
     */
    public static function getRoleNameById(string $id): UserRole
    {
        return match ((int)$id) {
            UserRoleID::SUPER_ADMIN_ID => UserRole::SUPER_ADMIN,
            UserRoleID::ADMIN_ID => UserRole::ADMIN,
            UserRoleID::USER_ID => UserRole::USER,
            default => UserRole::USER,
        };
    }
}

<?php

namespace Tests\Mock;

class RoleMockData
{
    public static function getRoleData(): array
    {
        return [
            'name' => 'Test Role',
            'guard_name' => 'web',
        ];
    }

    public static function getUpdateRoleData(): array
    {
        return [
            'name' => 'Updated Role',
        ];
    }
}

<?php

namespace Tests\Mock;

class UserMockData
{
    public static function getUserData(): array
    {
        return [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '01234567890',
            'password' => 'password123',
            'roles' => [1],
        ];
    }

    public static function getUpdateUserData(): array
    {
        return [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'phone' => '09876543210',
        ];
    }
}

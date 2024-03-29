<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'Super-Admin';
    case ADMIN = 'Admin';
    case USER = 'User';
}

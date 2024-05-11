<?php

namespace App\Enums;

enum UserRoleID : int
{

    case SUPER_ADMIN_ID = 1;
    case ADMIN_ID = 2;
    case USER_ID = 3;
}

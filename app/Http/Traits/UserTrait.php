<?php

namespace App\Http\Traits;

trait UserTrait
{
    private $USER_ACTIVE = 1;
    private $USER_DEACTIVE = 0;
    private $USER_TOKEN_PREFIX = 'dokani_user_';
    private $SUPER_ADMIN = 'Super-Admin';
    private $ADMIN = 'Admin';
    private $USER = 'User';
}

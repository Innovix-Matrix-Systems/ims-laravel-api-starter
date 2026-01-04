<?php

return [
    'role' => [
        'permissions' => [
            [
                'name' => 'role.view',
                'group' => 'role',
                'description' => 'Can view role details',
            ],
            [
                'name' => 'role.view.all',
                'group' => 'role',
                'description' => 'Can view all roles',
            ],
            [
                'name' => 'role.create',
                'group' => 'role',
                'description' => 'Can create new roles',
            ],
            [
                'name' => 'role.update',
                'group' => 'role',
                'description' => 'Can modify existing roles',
            ],
            [
                'name' => 'role.delete',
                'group' => 'role',
                'description' => 'Can delete roles',
            ],
        ],
    ],
    'user' => [
        'permissions' => [
            [
                'name' => 'user.view',
                'group' => 'user',
                'description' => 'Can view user details',
            ],
            [
                'name' => 'user.view.all',
                'group' => 'user',
                'description' => 'Can view all users',
            ],
            [
                'name' => 'user.create',
                'group' => 'user',
                'description' => 'Can create new users',
            ],
            [
                'name' => 'user.update',
                'group' => 'user',
                'description' => 'Can modify existing users',
            ],
            [
                'name' => 'user.delete',
                'group' => 'user',
                'description' => 'Can delete users',
            ],
            [
                'name' => 'user.role.assign',
                'group' => 'user',
                'description' => 'Can Assign role to users',
            ],
            [
                'name' => 'user.export',
                'group' => 'user',
                'description' => 'Can export all users',
            ],
            [
                'name' => 'user.import',
                'group' => 'user',
                'description' => 'Can import users',
            ],
        ],
    ],
];

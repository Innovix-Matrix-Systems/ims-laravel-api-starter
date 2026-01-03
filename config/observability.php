<?php

return [
    'enabled' => env('OBSERVABILITY_ENABLED', false),
    'auth' => [
        'enabled' => env('OBSERVABILITY_AUTH_ENABLED', true),
        'email' => env('OBSERVABILITY_AUTH_EMAIL', ''),
        'password' => env('OBSERVABILITY_AUTH_PASSWORD', ''),
    ],
];

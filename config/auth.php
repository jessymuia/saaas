<?php

return [

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // ← This is the missing guard for the Central (Super Admin) Panel
        'system_admin' => [
            'driver' => 'session',
            'provider' => 'system_admins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'tenant_eloquent',   // for normal tenant users
            'model'  => App\Models\User::class,
        ],

        'system_admins' => [
            'driver' => 'eloquent',          // for central super admins
            'model'  => App\Models\SystemAdmin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'system_admins' => [
            'provider' => 'system_admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
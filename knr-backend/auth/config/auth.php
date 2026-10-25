<?php

// Authentication is handled by JWTs issued in App\Services\JwtIssuer;
// this file only keeps framework defaults satisfied.
return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    'passwords' => [],

    'password_timeout' => 10800,

];

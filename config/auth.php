<?php

declare(strict_types=1);

return [
    'session_key' => 'auth_user',
    'guest_redirect' => '/admin/login',
    'dashboard_redirect' => '/admin',
    'reset_expiry_minutes' => 30,
    'allowed_roles' => [
        'admin',
        'editor',
        'author',
    ],
    'roles' => [
        'admin',
        'editor',
        'author',
        'visitor',
    ],
    'middleware' => [
        'auth' => \App\Middleware\AuthenticationMiddleware::class,
    ],
];

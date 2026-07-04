<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Developer Ruhban'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => rtrim((string) env('APP_URL', 'http://localhost'), '/'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'providers' => [],
    'middleware' => [
        'auth' => \App\Middleware\AuthenticationMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'csrf' => \App\Middleware\CsrfMiddleware::class,
        'admin_access' => \App\Middleware\AdminAccessMiddleware::class,
        'admin_only' => \App\Middleware\AdminOnlyMiddleware::class,
    ],
    'routes' => [
        __DIR__ . '/../routes/web.php',
        __DIR__ . '/../routes/auth.php',
        __DIR__ . '/../routes/admin.php',
        __DIR__ . '/../routes/content.php',
        __DIR__ . '/../routes/api.php',
        __DIR__ . '/../routes/seo.php',
        __DIR__ . '/../routes/redirects.php',
    ],
];

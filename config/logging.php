<?php

declare(strict_types=1);

return [
    'channel' => env('LOG_CHANNEL', 'single'),
    'level' => env('LOG_LEVEL', 'debug'),
    'path' => base_path('logs'),
    'filename' => 'app-' . date('Y-m-d') . '.log',
];

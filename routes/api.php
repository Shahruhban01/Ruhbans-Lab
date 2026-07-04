<?php

declare(strict_types=1);

use App\Core\Router;

return static function (Router $router): void {
    $router->get('/api/health', static fn () => ['status' => 'ok']);
};

<?php

declare(strict_types=1);

use App\Controllers\SeoController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/{slug}', [SeoController::class, 'performRedirect']);
};
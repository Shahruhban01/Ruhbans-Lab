<?php

declare(strict_types=1);

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ContentController;
use App\Controllers\Admin\UserController;
use App\Core\Router;

return static function (Router $router): void {
    $router->group([
        'prefix' => '/admin',
        'middleware' => ['auth', 'csrf', 'admin_access'],
    ], static function (Router $router): void {
        $router->get('/', [DashboardController::class, 'index']);
        $router->get('/activity-logs', [DashboardController::class, 'activityLogs']);

        $router->group([
            'prefix' => '/content',
        ], static function (Router $router): void {
            $router->get('/', [ContentController::class, 'index']);
            $router->get('/drafts', [ContentController::class, 'drafts']);
            $router->get('/create', [ContentController::class, 'create']);
            $router->post('/', [ContentController::class, 'store']);
            $router->get('/{id}/edit', [ContentController::class, 'edit']);
            $router->patch('/{id}', [ContentController::class, 'update']);
            $router->post('/{id}/publish', [ContentController::class, 'publish']);
            $router->post('/{id}/schedule', [ContentController::class, 'schedule']);
            $router->get('/{id}/revisions', [ContentController::class, 'revisions']);
            $router->post('/{id}/revisions/{revisionId}/restore', [ContentController::class, 'restoreRevision']);
            $router->get('/categories', [ContentController::class, 'categories']);
            $router->post('/categories', [ContentController::class, 'saveCategory']);
            $router->get('/tags', [ContentController::class, 'tags']);
            $router->post('/tags', [ContentController::class, 'saveTag']);
            $router->get('/types', [ContentController::class, 'contentTypes']);
            $router->post('/types', [ContentController::class, 'saveContentType']);
            $router->get('/media', [ContentController::class, 'media']);
            $router->post('/media', [ContentController::class, 'uploadMedia']);
        });

        $router->group([
            'prefix' => '/users',
            'middleware' => ['admin_only'],
        ], static function (Router $router): void {
            $router->get('/', [UserController::class, 'index']);
            $router->post('/update-role', [UserController::class, 'updateRole']);
            $router->post('/toggle-status', [UserController::class, 'toggleStatus']);
        });
    });
};

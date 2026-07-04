<?php

declare(strict_types=1);

use App\Controllers\Admin\ContentController;
use App\Core\Router;

return static function (Router $router): void {
    $router->group(array(
        'prefix' => '/admin/content',
        'middleware' => array('auth', 'csrf', 'admin_access'),
    ), static function (Router $router): void {
        $router->get('/', array(ContentController::class, 'index'));
        $router->get('/create', array(ContentController::class, 'create'));
        $router->post('/', array(ContentController::class, 'store'));
        $router->get('/drafts', array(ContentController::class, 'drafts'));
        $router->get('/{id}/edit', array(ContentController::class, 'edit'));
        $router->patch('/{id}', array(ContentController::class, 'update'));
        $router->post('/{id}/publish', array(ContentController::class, 'publish'));
        $router->post('/{id}/schedule', array(ContentController::class, 'schedule'));
        $router->get('/{id}/revisions', array(ContentController::class, 'revisions'));
        $router->post('/{id}/revisions/{revisionId}/restore', array(ContentController::class, 'restoreRevision'));

        $router->get('/categories', array(ContentController::class, 'categories'));
        $router->post('/categories', array(ContentController::class, 'saveCategory'));
        $router->get('/tags', array(ContentController::class, 'tags'));
        $router->post('/tags', array(ContentController::class, 'saveTag'));
        $router->get('/types', array(ContentController::class, 'contentTypes'));
        $router->post('/types', array(ContentController::class, 'saveContentType'));
        $router->get('/media', array(ContentController::class, 'media'));
        $router->post('/media', array(ContentController::class, 'uploadMedia'));
    });
};

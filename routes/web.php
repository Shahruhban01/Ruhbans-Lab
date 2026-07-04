<?php

declare(strict_types=1);

use App\Controllers\SiteController;
use App\Controllers\SearchController;
use App\Controllers\EngagementController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/', [SiteController::class, 'home']);
    $router->get('/archive', [SiteController::class, 'archive']);
    $router->get('/search', [SearchController::class, 'index']);
    $router->get('/search/suggest', [SearchController::class, 'suggest']);
    $router->get('/search/instant', [SearchController::class, 'instant']);
    $router->get('/category/{slug}', [SiteController::class, 'category']);
    $router->get('/tag/{slug}', [SiteController::class, 'tag']);
    $router->get('/author/{username}', [SiteController::class, 'author']);
    $router->get('/type/{slug}', [SiteController::class, 'type']);
    $router->get('/content/{slug}', [SiteController::class, 'content']);
    $router->get('/about', [SiteController::class, 'about']);
    $router->get('/contact', [SiteController::class, 'contact']);
    $router->post('/contact', [EngagementController::class, 'contact'], array('csrf'));
    $router->post('/newsletter/subscribe', [EngagementController::class, 'newsletter'], array('csrf'));
    $router->post('/content/{postId}/comments', [EngagementController::class, 'comment'], array('csrf'));
    $router->post('/content/{postId}/react/{type}', [EngagementController::class, 'react'], array('csrf'));
    $router->get('/lab', [SiteController::class, 'lab']);
    $router->get('/lab/{slug}', [SiteController::class, 'labProduct']);
    $router->get('/login', [SiteController::class, 'showLogin']);
    $router->post('/login', [SiteController::class, 'login']);
    $router->get('/signup', [SiteController::class, 'showSignup']);
    $router->post('/signup', [SiteController::class, 'signup']);
    $router->get('/logout', [SiteController::class, 'logout']);
    $router->get('/privacy-policy', [SiteController::class, 'privacy']);
    $router->get('/terms-and-conditions', [SiteController::class, 'terms']);
};

<?php

declare(strict_types=1);

use App\Controllers\SiteController;
use App\Controllers\SearchController;
use App\Controllers\EngagementController;
use App\Core\Router;

return static function (Router $router): void {
    $router->group([
        'middleware' => ['membership'],
    ], static function (Router $router): void {
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
        $router->get('/pricing', [SiteController::class, 'publicPricing']);
        $router->post('/contact', [EngagementController::class, 'contact'], array('csrf'));
        $router->post('/newsletter/subscribe', [EngagementController::class, 'newsletter'], array('csrf'));
        $router->post('/content/{postId}/comments', [EngagementController::class, 'comment'], array('csrf'));
        $router->post('/content/{postId}/react/{type}', [EngagementController::class, 'react'], array('csrf'));
        $router->get('/lab', [SiteController::class, 'lab']);
        $router->get('/lab/{slug}', [SiteController::class, 'labProduct']);
        $router->group([
            'middleware' => ['guest'],
        ], static function (Router $router): void {
            $router->get('/login', [SiteController::class, 'showLogin']);
            $router->post('/login', [SiteController::class, 'login']);
            $router->get('/signup', [SiteController::class, 'showSignup']);
            $router->post('/signup', [SiteController::class, 'signup']);
        });
        $router->get('/logout', [SiteController::class, 'logout']);
        $router->get('/privacy-policy', [SiteController::class, 'privacy']);
        $router->get('/terms-and-conditions', [SiteController::class, 'terms']);
        $router->get('/membership', [SiteController::class, 'membershipPlans']);
        $router->get('/lab/{id}/download', [SiteController::class, 'downloadProduct']);
        $router->post('/lab/purchase', [SiteController::class, 'simulatedPurchase'], array('csrf'));
        $router->post('/webhooks/{provider}', [\App\Controllers\WebhookController::class, 'handle']);

        // Separated Member Portal Application
        $router->group([
            'prefix' => '/account',
            'middleware' => ['auth', 'member_only'],
        ], static function (Router $router): void {
            $router->get('/dashboard', [\App\Controllers\MemberController::class, 'dashboard']);
            $router->get('/profile', [\App\Controllers\MemberController::class, 'profile']);
            $router->post('/profile', [\App\Controllers\MemberController::class, 'updateProfile'], array('csrf'));
            $router->get('/settings', [\App\Controllers\MemberController::class, 'settings']);
            $router->get('/security', [\App\Controllers\MemberController::class, 'security']);
            $router->get('/bookmarks', [\App\Controllers\MemberController::class, 'bookmarks']);
            $router->get('/collections', [\App\Controllers\MemberController::class, 'collections']);
            $router->get('/downloads', [\App\Controllers\MemberController::class, 'downloads']);
            $router->get('/history', [\App\Controllers\MemberController::class, 'history']);
            $router->get('/notifications', [\App\Controllers\MemberController::class, 'notifications']);
            $router->post('/notifications/read-all', [\App\Controllers\MemberController::class, 'markAllNotificationsRead'], array('csrf'));
            $router->get('/membership', [\App\Controllers\MemberController::class, 'membership']);
            $router->get('/pricing', [\App\Controllers\MemberController::class, 'pricing']);
            $router->post('/pricing/checkout', [\App\Controllers\MemberController::class, 'checkout'], array('csrf'));
            $router->get('/purchases', [\App\Controllers\MemberController::class, 'purchases']);
            $router->get('/licenses', [\App\Controllers\MemberController::class, 'licenses']);
            $router->get('/billing', [\App\Controllers\MemberController::class, 'billing']);
            $router->get('/support', [\App\Controllers\MemberController::class, 'support']);
            $router->get('/activity', [\App\Controllers\MemberController::class, 'activity']);
        });
    });
};

<?php

declare(strict_types=1);

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ContentController;
use App\Controllers\Admin\AnalyticsController;
use App\Controllers\Admin\SettingsController;
use App\Controllers\Admin\RedirectController;
use App\Controllers\Admin\UserController;
use App\Core\Router;

return static function (Router $router): void {
    $router->group([
        'prefix' => '/admin',
        'middleware' => ['auth', 'csrf', 'admin_access', 'membership'],
    ], static function (Router $router): void {
        $router->get('/', [DashboardController::class, 'index']);
        $router->get('/activity-logs', [DashboardController::class, 'activityLogs']);
        $router->get('/analytics', [AnalyticsController::class, 'index']);
        $router->get('/settings', [SettingsController::class, 'index']);
        $router->post('/settings/save/{group}', [SettingsController::class, 'saveGroup']);
        $router->post('/settings/cache/clear', [SettingsController::class, 'clearCache']);
        $router->post('/settings/backup', [SettingsController::class, 'backup']);
        $router->post('/settings/restore', [SettingsController::class, 'restore']);
        $router->post('/settings/maintenance', [SettingsController::class, 'maintenance']);
        $router->get('/redirects', [RedirectController::class, 'index']);
        $router->post('/redirects', [RedirectController::class, 'store']);
        $router->post('/redirects/{id}/delete', [RedirectController::class, 'delete']);

        $router->get('/memberships', [\App\Controllers\Admin\MembershipController::class, 'index']);
        $router->post('/memberships/assign', [\App\Controllers\Admin\MembershipController::class, 'assign']);
        $router->post('/memberships/cancel', [\App\Controllers\Admin\MembershipController::class, 'cancel']);
        $router->get('/memberships/revenue', [\App\Controllers\Admin\MembershipController::class, 'revenue']);
        $router->get('/memberships/coupons', [\App\Controllers\Admin\MembershipController::class, 'coupons']);
        $router->post('/memberships/coupons/create', [\App\Controllers\Admin\MembershipController::class, 'createCoupon']);
        $router->post('/memberships/coupons/{id}/delete', [\App\Controllers\Admin\MembershipController::class, 'deleteCoupon']);
        $router->get('/memberships/licenses', [\App\Controllers\Admin\MembershipController::class, 'licenses']);
        $router->get('/memberships/gateways', [\App\Controllers\Admin\PaymentGatewayController::class, 'index']);
        $router->post('/memberships/gateways/{gateway}/save', [\App\Controllers\Admin\PaymentGatewayController::class, 'save']);
        $router->post('/memberships/gateways/{gateway}/test', [\App\Controllers\Admin\PaymentGatewayController::class, 'testConnection']);
        $router->get('/memberships/analytics', [\App\Controllers\Admin\PaymentAnalyticsController::class, 'index']);
        $router->get('/memberships/docs', [\App\Controllers\Admin\PaymentAnalyticsController::class, 'docs']);

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
             $router->post('/media/{id}/delete', [ContentController::class, 'deleteMedia']);
             $router->get('/media-manager', [ContentController::class, 'mediaManager']);
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

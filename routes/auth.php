<?php

declare(strict_types=1);

use App\Controllers\Admin\AuthController;
use App\Core\Router;

return static function (Router $router): void {
    $router->group(array(
        'prefix' => '/admin',
        'middleware' => array('guest', 'csrf'),
    ), static function (Router $router): void {
        $router->get('/login', array(AuthController::class, 'showLogin'));
        $router->post('/login', array(AuthController::class, 'login'));
        $router->get('/forgot-password', array(AuthController::class, 'showForgotPassword'));
        $router->post('/forgot-password', array(AuthController::class, 'sendPasswordResetLink'));
        $router->get('/reset-password', array(AuthController::class, 'showResetPassword'));
        $router->post('/reset-password', array(AuthController::class, 'resetPassword'));
    });

    $router->post('/admin/logout', array(AuthController::class, 'logout'), array('auth', 'csrf'));
};

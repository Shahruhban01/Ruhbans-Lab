<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\HttpException;
use App\Core\Request;

final class AdminOnlyMiddleware implements MiddlewareInterface
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, callable $next)
    {
        $user = $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));

        if (!is_array($user) || !isset($user['role'])) {
            return \App\Core\Response::redirect((string) $this->app->config()->get('auth.guest_redirect', '/admin/login'));
        }

        if ($user['role'] !== 'admin') {
            throw new HttpException('Forbidden', 403);
        }

        return $next();
    }
}

<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

final class GuestMiddleware implements MiddlewareInterface
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, callable $next)
    {
        if ($this->app->session()->has($this->app->config()->get('auth.session_key', 'auth_user'))) {
            return Response::redirect((string) $this->app->config()->get('auth.dashboard_redirect', '/admin'));
        }

        return $next();
    }
}

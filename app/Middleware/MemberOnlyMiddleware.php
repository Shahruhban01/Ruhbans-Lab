<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

final class MemberOnlyMiddleware implements MiddlewareInterface
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, callable $next)
    {
        $sessionKey = (string) $this->app->config()->get('auth.session_key', 'auth_user');

        if (!$this->app->session()->has($sessionKey)) {
            $this->app->session()->flash('error', 'Please sign in to access the member portal.');
            return Response::redirect('/login');
        }

        return $next();
    }
}

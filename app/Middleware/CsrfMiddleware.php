<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\HttpException;
use App\Core\Request;

final class CsrfMiddleware implements MiddlewareInterface
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, callable $next)
    {
        $method = strtoupper($request->method());

        if (!in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE'), true)) {
            return $next();
        }

        $token = (string) $request->input('_token', $request->header('X-CSRF-TOKEN', ''));

        if ($token === '' || !hash_equals($this->app->session()->token(), $token)) {
            throw new HttpException('CSRF token mismatch.', 419);
        }

        return $next();
    }
}

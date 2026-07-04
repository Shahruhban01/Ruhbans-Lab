<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

abstract class BaseController
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function request(): Request
    {
        return $this->app->request();
    }

    protected function view(string $template, array $data = [], array $options = []): Response
    {
        return new Response($this->app->view()->render($template, $data, $options));
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }
}

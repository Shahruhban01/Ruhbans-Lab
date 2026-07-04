<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;

final class HomeController extends BaseController
{
    public function index(Request $request)
    {
        return $this->view('home/index', [
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
        ], [
            'meta' => [
                'title' => 'Developer Ruhban',
                'description' => 'A public developer knowledge platform built for long-term scalability, SEO, and reusable content publishing.',
                'canonical' => url('/'),
                'schemaType' => 'WebSite',
            ],
        ]);
    }
}

<?php

declare(strict_types=1);

use App\Controllers\SeoController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/robots.txt', [SeoController::class, 'robots']);
    $router->get('/sitemap.xml', [SeoController::class, 'sitemapIndex']);
    $router->get('/sitemap-main.xml', [SeoController::class, 'mainSitemap']);
    $router->get('/sitemap-posts.xml', [SeoController::class, 'postsSitemap']);
    $router->get('/sitemap-categories.xml', [SeoController::class, 'categoriesSitemap']);
    $router->get('/sitemap-tags.xml', [SeoController::class, 'tagsSitemap']);
    $router->get('/sitemap-images.xml', [SeoController::class, 'imagesSitemap']);
    $router->get('/sitemap-videos.xml', [SeoController::class, 'videosSitemap']);
    $router->get('/feed.xml', [SeoController::class, 'rss']);
};
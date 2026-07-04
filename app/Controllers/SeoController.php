<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\CategoryRepository;
use App\Repositories\ContentTypeRepository;
use App\Repositories\PostRepository;
use App\Repositories\RedirectRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;

final class SeoController extends BaseController
{
    private PostRepository $postRepository;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;
    private ContentTypeRepository $contentTypeRepository;
    private UserRepository $userRepository;
    private RedirectRepository $redirectRepository;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $connection = $this->app->database()->connection();
        $this->postRepository = new PostRepository($connection);
        $this->categoryRepository = new CategoryRepository($connection);
        $this->tagRepository = new TagRepository($connection);
        $this->contentTypeRepository = new ContentTypeRepository($connection);
        $this->userRepository = new UserRepository($connection);
        $this->redirectRepository = new RedirectRepository($connection);
    }

    public function robots(Request $request)
    {
        $lines = array(
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /login',
            'Disallow: /logout',
            'Disallow: /register',
            'Disallow: /reset-password',
            'Disallow: /storage/',
            'Disallow: /cache/',
            'Disallow: /tmp/',
            'Disallow: /uploads/private/',
            'Sitemap: ' . url('/sitemap.xml'),
            'Sitemap: ' . url('/sitemap-main.xml'),
            'Sitemap: ' . url('/sitemap-posts.xml'),
            'Sitemap: ' . url('/sitemap-categories.xml'),
            'Sitemap: ' . url('/sitemap-tags.xml'),
            'Sitemap: ' . url('/sitemap-images.xml'),
            'Sitemap: ' . url('/sitemap-videos.xml'),
        );

        return (new Response(implode("\n", $lines) . "\n", 200))
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    public function sitemapIndex(Request $request)
    {
        return $this->xmlResponse($this->renderSitemapIndex(array(
            url('/sitemap-main.xml'),
            url('/sitemap-posts.xml'),
            url('/sitemap-categories.xml'),
            url('/sitemap-tags.xml'),
            url('/sitemap-images.xml'),
            url('/sitemap-videos.xml'),
        )));
    }

    public function mainSitemap(Request $request)
    {
        $urls = array(
            url('/'),
            url('/archive'),
            url('/search'),
            url('/about'),
            url('/contact'),
            url('/privacy-policy'),
            url('/terms-and-conditions'),
        );

        foreach ($this->contentTypeRepository->allTypes() as $type) {
            if (!empty($type['slug'])) {
                $urls[] = url('/type/' . $type['slug']);
            }
        }

        return $this->xmlResponse($this->renderUrlSet($urls));
    }

    public function postsSitemap(Request $request)
    {
        return $this->xmlResponse($this->renderUrlSet($this->collectPostUrls()));
    }

    public function categoriesSitemap(Request $request)
    {
        $urls = array();

        foreach ($this->categoryRepository->allCategories() as $category) {
            if (!empty($category['slug'])) {
                $urls[] = url('/category/' . $category['slug']);
            }
        }

        return $this->xmlResponse($this->renderUrlSet($urls));
    }

    public function tagsSitemap(Request $request)
    {
        $urls = array();

        foreach ($this->tagRepository->allTags() as $tag) {
            if (!empty($tag['slug'])) {
                $urls[] = url('/tag/' . $tag['slug']);
            }
        }

        return $this->xmlResponse($this->renderUrlSet($urls));
    }

    public function imagesSitemap(Request $request)
    {
        $entries = array();

        foreach ($this->collectPublishedPosts() as $post) {
            if (empty($post['featured_image'])) {
                continue;
            }

            $entries[] = array(
                'page' => url('/content/' . $post['slug']),
                'image' => asset($post['featured_image']),
                'title' => (string) $post['title'],
            );
        }

        return $this->xmlResponse($this->renderImageSet($entries));
    }

    public function videosSitemap(Request $request)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"></urlset>';

        return $this->xmlResponse($xml);
    }

    public function rss(Request $request)
    {
        $posts = array_slice($this->collectPublishedPosts(), 0, 20);
        $siteName = (string) $this->app->config()->get('app.name', 'Developer Ruhban');
        $siteUrl = url('/');

        $items = array();

        foreach ($posts as $post) {
            $link = url('/content/' . $post['slug']);
            $description = !empty($post['excerpt']) ? $post['excerpt'] : strip_tags((string) $post['content']);
            $items[] = '        <item>'
                . '<title>' . $this->xmlEscape((string) $post['title']) . '</title>'
                . '<link>' . $this->xmlEscape($link) . '</link>'
                . '<guid isPermaLink="true">' . $this->xmlEscape($link) . '</guid>'
                . '<pubDate>' . date(DATE_RSS, strtotime((string) ($post['published_at'] ?: $post['created_at']))) . '</pubDate>'
                . '<description>' . $this->xmlEscape($description) . '</description>'
                . '<content:encoded><![CDATA[' . $post['content'] . ']]></content:encoded>'
                . '</item>';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">'
            . '<channel>'
            . '<title>' . $this->xmlEscape($siteName) . '</title>'
            . '<link>' . $this->xmlEscape($siteUrl) . '</link>'
            . '<description>Latest published content from ' . $this->xmlEscape($siteName) . '</description>'
            . '<language>en</language>'
            . '<lastBuildDate>' . date(DATE_RSS) . '</lastBuildDate>'
            . implode('', $items)
            . '</channel>'
            . '</rss>';

        return $this->xmlResponse($xml, 'application/rss+xml; charset=utf-8');
    }

    public function redirect(Request $request, string $slug)
    {
        $path = '/' . trim($slug, '/');
        $redirect = $this->redirectRepository->findBySourcePath($path);

        if ($redirect === null) {
            $this->app->logger()->warning('Broken URL requested', array('path' => $path, 'referer' => (string) $request->header('Referer', '')));
            throw new HttpException('Redirect not found.', 404);
        }

        $statusCode = (int) ($redirect['status_code'] ?? 301);
        $targetPath = trim((string) $redirect['target_path']);
        $targetUrl = preg_match('#^https?://#i', $targetPath) === 1 ? $targetPath : url($targetPath);

        if ($statusCode === 410) {
            return (new Response('Gone', 410))->header('Content-Type', 'text/plain; charset=utf-8');
        }

        return Response::redirect($targetUrl, in_array($statusCode, array(301, 302), true) ? $statusCode : 301);
    }

    private function collectPublishedPosts(): array
    {
        $posts = array();
        $page = 1;

        do {
            $results = $this->postRepository->paginatePublic(array(), $page, 50);
            $chunk = isset($results['data']) && is_array($results['data']) ? $results['data'] : array();
            $posts = array_merge($posts, $chunk);
            $page++;
        } while (!empty($results['pagination']['pages']) && $page <= (int) $results['pagination']['pages']);

        return $posts;
    }

    private function collectPostUrls(): array
    {
        $urls = array();

        foreach ($this->collectPublishedPosts() as $post) {
            $urls[] = url('/content/' . $post['slug']);
        }

        return $urls;
    }

    private function renderSitemapIndex(array $locs): string
    {
        $entries = array();

        foreach ($locs as $loc) {
            $entries[] = '  <sitemap><loc>' . $this->xmlEscape($loc) . '</loc></sitemap>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . implode('', $entries)
            . '</sitemapindex>';
    }

    private function renderUrlSet(array $urls): string
    {
        $entries = array();

        foreach (array_values(array_unique($urls)) as $url) {
            $entries[] = '  <url><loc>' . $this->xmlEscape($url) . '</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . implode('', $entries)
            . '</urlset>';
    }

    private function renderImageSet(array $entries): string
    {
        $items = array();

        foreach ($entries as $entry) {
            $items[] = '  <url>'
                . '<loc>' . $this->xmlEscape((string) $entry['page']) . '</loc>'
                . '<image:image>'
                . '<image:loc>' . $this->xmlEscape((string) $entry['image']) . '</image:loc>'
                . '<image:title>' . $this->xmlEscape((string) $entry['title']) . '</image:title>'
                . '</image:image>'
                . '</url>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'
            . implode('', $items)
            . '</urlset>';
    }

    private function xmlResponse(string $content, string $contentType = 'application/xml; charset=utf-8'): Response
    {
        return (new Response($content, 200))->header('Content-Type', $contentType);
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
    }
}
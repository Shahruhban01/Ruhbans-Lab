<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\HttpException;
use App\Core\Request;
use App\Repositories\CategoryRepository;
use App\Repositories\ContentTypeRepository;
use App\Repositories\PostMetaRepository;
use App\Repositories\PostRepository;
use App\Repositories\PostSeoRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;

final class SiteController extends BaseController
{
    private PostRepository $postRepository;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;
    private ContentTypeRepository $contentTypeRepository;
    private UserRepository $userRepository;
    private PostSeoRepository $seoRepository;
    private PostMetaRepository $metaRepository;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $connection = $this->app->database()->connection();
        $this->postRepository = new PostRepository($connection);
        $this->categoryRepository = new CategoryRepository($connection);
        $this->tagRepository = new TagRepository($connection);
        $this->contentTypeRepository = new ContentTypeRepository($connection);
        $this->userRepository = new UserRepository($connection);
        $this->seoRepository = new PostSeoRepository($connection);
        $this->metaRepository = new PostMetaRepository($connection);
    }

    public function home(Request $request)
    {
        return $this->view('site/home', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'featuredPosts' => $this->postRepository->featuredPublished(3),
            'recentPosts' => $this->postRepository->recentPublished(6),
            'categories' => $this->categoryRepository->tree(),
            'tags' => array_slice($this->tagRepository->allTags(), 0, 8),
            'contentTypes' => $this->contentTypeRepository->allTypes(),
            'archiveMonths' => $this->postRepository->archiveMonths(6),
            'search' => trim((string) $request->input('q', $request->input('search', ''))),
        ), array(
            'meta' => array(
                'title' => 'Developer Ruhban - Home',
                'description' => 'A content-first developer knowledge platform with tutorials, guides, reviews, and practical technical notes.',
                'canonical' => url('/'),
                'schemaType' => 'WebSite',
                'robots' => 'index, follow',
            ),
        ));
    }

    public function archive(Request $request)
    {
        $filters = $this->archiveFilters($request);

        return $this->view('site/archive', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'posts' => $this->postRepository->publicListing($filters, (int) $request->input('page', 1), 12),
            'filters' => $filters,
            'contentTypes' => $this->contentTypeRepository->allTypes(),
            'categories' => $this->categoryRepository->allCategories(),
            'tags' => $this->tagRepository->allTags(),
            'archiveMonths' => $this->postRepository->archiveMonths(12),
        ), array(
            'meta' => array(
                'title' => 'Archive - Developer Ruhban',
                'description' => 'Browse all published content across tutorials, guides, categories, tags, and authors.',
                'canonical' => url('/archive'),
                'schemaType' => 'CollectionPage',
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Archive', 'url' => url('/archive')),
            )),
        ));
    }

    public function category(Request $request, string $slug)
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (!$category) {
            throw new HttpException('Category not found.', 404);
        }

        return $this->renderTaxonomyPage('Category', (string) $category['name'], (string) $category['description'], 'category', $slug, $this->postRepository->publicListing(array('category_slug' => $slug), (int) $request->input('page', 1), 12), url('/category/' . $slug), array(
            array('label' => 'Home', 'url' => url('/')),
            array('label' => 'Archive', 'url' => url('/archive')),
            array('label' => $category['name'], 'url' => url('/category/' . $slug)),
        ));
    }

    public function tag(Request $request, string $slug)
    {
        $tag = $this->tagRepository->findBySlug($slug);

        if (!$tag) {
            throw new HttpException('Tag not found.', 404);
        }

        return $this->renderTaxonomyPage('Tag', (string) $tag['name'], 'Posts tagged with ' . $tag['name'] . '.', 'tag', $slug, $this->postRepository->publicListing(array('tag_slug' => $slug), (int) $request->input('page', 1), 12), url('/tag/' . $slug), array(
            array('label' => 'Home', 'url' => url('/')),
            array('label' => 'Archive', 'url' => url('/archive')),
            array('label' => $tag['name'], 'url' => url('/tag/' . $slug)),
        ));
    }

    public function author(Request $request, string $username)
    {
        $author = $this->userRepository->findByUsername($username);

        if (!$author) {
            throw new HttpException('Author not found.', 404);
        }

        return $this->renderTaxonomyPage('Author', (string) $author['name'], !empty($author['bio']) ? (string) $author['bio'] : 'Published content by ' . $author['name'] . '.', 'author', $username, $this->postRepository->publicListing(array('author_username' => $username), (int) $request->input('page', 1), 12), url('/author/' . $username), array(
            array('label' => 'Home', 'url' => url('/')),
            array('label' => 'Archive', 'url' => url('/archive')),
            array('label' => $author['name'], 'url' => url('/author/' . $username)),
        ), $author);
    }

    public function type(Request $request, string $slug)
    {
        $type = $this->contentTypeRepository->findBySlug($slug);

        if (!$type) {
            throw new HttpException('Content type not found.', 404);
        }

        return $this->renderTaxonomyPage('Content Type', (string) $type['name'], !empty($type['description']) ? (string) $type['description'] : 'Content published under the ' . $type['name'] . ' type.', 'type', $slug, $this->postRepository->publicListing(array('type_slug' => $slug), (int) $request->input('page', 1), 12), url('/type/' . $slug), array(
            array('label' => 'Home', 'url' => url('/')),
            array('label' => 'Archive', 'url' => url('/archive')),
            array('label' => $type['name'], 'url' => url('/type/' . $slug)),
        ));
    }

    public function content(Request $request, string $slug)
    {
        $post = $this->postRepository->findPublishedBySlug($slug);

        if (!$post) {
            throw new HttpException('Content not found.', 404);
        }

        $categories = $this->mapItemsByIds($this->categoryRepository->allCategories(), $this->postRepository->categoriesForPost($post['id']));
        $tags = $this->mapItemsByIds($this->tagRepository->allTags(), $this->postRepository->tagsForPost($post['id']));
        $seo = $this->seoRepository->findByPostId($post['id']);

        $this->postRepository->recordView((int) $post['id']);

        return $this->view('site/content', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
            'seo' => $seo ?: array(),
            'metaFields' => $this->metaRepository->getByPostId($post['id']),
            'relatedPosts' => $this->postRepository->relatedPublished($post['id'], $post['content_type_id'], array_map(static function (array $item): int {
                return (int) $item['id'];
            }, $categories), 3),
            'author' => $this->userRepository->findByUsername($post['author_username']),
        ), array(
            'meta' => array(
                'title' => $this->contentTitle($post, $seo),
                'description' => $this->contentDescription($post, $seo),
                'canonical' => url('/content/' . $slug),
                'schemaType' => 'Article',
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => $post['content_type_name'], 'url' => url('/type/' . $post['content_type_slug'])),
                array('label' => $post['title'], 'url' => url('/content/' . $slug)),
            )),
        ));
    }

    public function about(Request $request)
    {
        return $this->view('site/about', array('siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban')), array(
            'meta' => array(
                'title' => 'About - Developer Ruhban',
                'description' => 'Learn what Developer Ruhban is building and why the platform exists.',
                'canonical' => url('/about'),
                'schemaType' => 'AboutPage',
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'About', 'url' => url('/about')),
            )),
        ));
    }

    public function contact(Request $request)
    {
        return $this->view('site/contact', array('siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban')), array(
            'meta' => array(
                'title' => 'Contact - Developer Ruhban',
                'description' => 'Reach out to the Developer Ruhban team for collaborations, feedback, or support.',
                'canonical' => url('/contact'),
                'schemaType' => 'ContactPage',
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Contact', 'url' => url('/contact')),
            )),
        ));
    }

    public function privacy(Request $request)
    {
        return $this->view('site/privacy', array('siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban')), array(
            'meta' => array(
                'title' => 'Privacy Policy - Developer Ruhban',
                'description' => 'Understand how Developer Ruhban handles privacy, data, and cookies.',
                'canonical' => url('/privacy-policy'),
                'schemaType' => 'WebPage',
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Privacy Policy', 'url' => url('/privacy-policy')),
            )),
        ));
    }

    public function terms(Request $request)
    {
        return $this->view('site/terms', array('siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban')), array(
            'meta' => array(
                'title' => 'Terms & Conditions - Developer Ruhban',
                'description' => 'Read the terms and conditions for using Developer Ruhban.',
                'canonical' => url('/terms-and-conditions'),
                'schemaType' => 'WebPage',
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Terms & Conditions', 'url' => url('/terms-and-conditions')),
            )),
        ));
    }

    private function archiveFilters(Request $request): array
    {
        return array(
            'search' => trim((string) $request->input('search', '')),
            'type_slug' => trim((string) $request->input('type', '')),
            'category_slug' => trim((string) $request->input('category', '')),
            'tag_slug' => trim((string) $request->input('tag', '')),
            'author_username' => trim((string) $request->input('author', '')),
            'year' => (int) $request->input('year', 0),
            'month' => (int) $request->input('month', 0),
            'featured' => (int) $request->input('featured', 0),
        );
    }

    private function renderTaxonomyPage(string $typeLabel, string $title, string $description, string $slugKey, string $slug, array $posts, string $canonical, array $breadcrumbs, array $author = array())
    {
        return $this->view('site/taxonomy', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'taxonomyType' => $typeLabel,
            'taxonomyTitle' => $title,
            'taxonomyDescription' => $description,
            'taxonomySlug' => $slug,
            'taxonomyKey' => $slugKey,
            'posts' => $posts,
            'author' => $author,
            'archiveMonths' => $this->postRepository->archiveMonths(12),
            'contentTypes' => $this->contentTypeRepository->allTypes(),
        ), array(
            'meta' => array(
                'title' => $title . ' - Developer Ruhban',
                'description' => $description,
                'canonical' => $canonical,
                'schemaType' => 'CollectionPage',
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $breadcrumbs,
        ));
    }

    private function mapItemsByIds(array $items, array $ids): array
    {
        $lookup = array();

        foreach ($items as $item) {
            $lookup[(int) $item['id']] = $item;
        }

        $results = array();

        foreach ($ids as $id) {
            $id = (int) $id;
            if (isset($lookup[$id])) {
                $results[] = $lookup[$id];
            }
        }

        return $results;
    }

    private function contentTitle(array $post, $seo): string
    {
        if (is_array($seo) && !empty($seo['meta_title'])) {
            return (string) $seo['meta_title'];
        }

        return (string) $post['title'];
    }

    private function contentDescription(array $post, $seo): string
    {
        if (is_array($seo) && !empty($seo['meta_description'])) {
            return (string) $seo['meta_description'];
        }

        if (!empty($post['excerpt'])) {
            return (string) $post['excerpt'];
        }

        return 'Read ' . (string) $post['title'] . ' on Developer Ruhban.';
    }

    private function breadcrumbs(array $items): array
    {
        return $items;
    }
}
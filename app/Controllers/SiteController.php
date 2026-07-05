<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\HttpException;
use App\Core\Request;
use App\Repositories\CategoryRepository;
use App\Repositories\EngagementRepository;
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
    private EngagementRepository $engagementRepository;

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
        $this->engagementRepository = new EngagementRepository($connection);
    }

    public function home(Request $request)
    {
        $defaultImage = asset('assets/images/seo-card.svg');

        return $this->view('site/home', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'featuredPosts' => $this->postRepository->featuredPublished(3),
            'recentPosts' => $this->postRepository->recentPublished(6),
            'categories' => $this->categoryRepository->tree(),
            'tags' => array_slice($this->tagRepository->allTags(), 0, 8),
            'contentTypes' => $this->contentTypeRepository->allTypes(),
            'archiveMonths' => $this->postRepository->archiveMonths(6),
            'search' => trim((string) $request->input('q', $request->input('search', ''))),
            'activityFeed' => $this->engagementRepository->recentActivity(6),
            'readingHistory' => $this->engagementRepository->recentHistory($this->interactionIdentity(), 5),
        ), array(
            'meta' => array(
                'title' => 'Developer Ruhban - Home',
                'description' => 'A content-first developer knowledge platform with tutorials, guides, reviews, and practical technical notes.',
                'canonical' => url('/'),
                'schemaType' => 'WebSite',
                'robots' => 'index, follow',
                'ogImage' => $defaultImage,
                'schema' => array(
                    array(
                        '@context' => 'https://schema.org',
                        '@type' => 'WebSite',
                        'name' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
                        'url' => url('/'),
                        'description' => 'A content-first developer knowledge platform with tutorials, guides, reviews, and practical technical notes.',
                        'potentialAction' => array(
                            '@type' => 'SearchAction',
                            'target' => url('/search?q={search_term_string}'),
                            'query-input' => 'required name=search_term_string',
                        ),
                    ),
                    array(
                        '@context' => 'https://schema.org',
                        '@type' => 'Organization',
                        'name' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
                        'url' => url('/'),
                        'logo' => $defaultImage,
                    ),
                ),
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

        // Custom premium access block -> Teaser conversion page
        if (!has_post_access($post)) {
            $requiredAccess = strtolower(trim((string) ($post['visibility'] ?? 'pro')));
            $categories = $this->mapItemsByIds($this->categoryRepository->allCategories(), $this->postRepository->categoriesForPost($post['id']));
            $catIds = array_map(static function (array $item): int { return (int) $item['id']; }, $categories);
            $relatedFree = $this->postRepository->relatedPublished($post['id'], $post['content_type_id'], $catIds, 2);
            
            return $this->view('site/teaser', array(
                'post' => $post,
                'requiredAccess' => $requiredAccess,
                'relatedFree' => $relatedFree,
            ), array(
                'meta' => array(
                    'title' => 'Upgrade Required — ' . $post['title'],
                    'robots' => 'noindex, nofollow',
                ),
            ));
        }

        $categories = $this->mapItemsByIds($this->categoryRepository->allCategories(), $this->postRepository->categoriesForPost($post['id']));
        $tags = $this->mapItemsByIds($this->tagRepository->allTags(), $this->postRepository->tagsForPost($post['id']));
        $seo = $this->seoRepository->findByPostId($post['id']);

        $this->postRepository->recordView((int) $post['id']);
        $identity = $this->interactionIdentity();
        $this->engagementRepository->recordHistory((int) $post['id'], $identity);
        $featuredImage = !empty($post['featured_image']) ? asset($post['featured_image']) : asset('assets/images/seo-card.svg');
        $contentHtml = $this->optimizeContentImages((string) $post['content']);
        $schema = array(
            array(
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => (string) $post['title'],
                'description' => $this->contentDescription($post, $seo),
                'image' => $featuredImage,
                'author' => array(
                    '@type' => 'Person',
                    'name' => (string) $post['author_name'],
                ),
                'publisher' => array(
                    '@type' => 'Organization',
                    'name' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
                    'logo' => array(
                        '@type' => 'ImageObject',
                        'url' => asset('assets/images/seo-card.svg'),
                    ),
                ),
                'datePublished' => !empty($post['published_at']) ? $post['published_at'] : $post['created_at'],
                'dateModified' => !empty($post['updated_at']) ? $post['updated_at'] : $post['created_at'],
                'mainEntityOfPage' => url('/content/' . $slug),
                'keywords' => trim(implode(', ', array_filter(array_map(static function (array $tag): string {
                    return isset($tag['name']) ? (string) $tag['name'] : '';
                }, $tags)))),
            ),
        );

        return $this->view('site/content', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'post' => $post,
            'contentHtml' => $contentHtml,
            'categories' => $categories,
            'tags' => $tags,
            'seo' => $seo ?: array(),
            'metaFields' => $this->metaRepository->getByPostId($post['id']),
            'relatedPosts' => $this->postRepository->relatedPublished($post['id'], $post['content_type_id'], array_map(static function (array $item): int {
                return (int) $item['id'];
            }, $categories), 3),
            'author' => $this->userRepository->findByUsername($post['author_username']),
            'comments' => $this->engagementRepository->commentsForPost((int) $post['id']),
            'interactionCounts' => $this->engagementRepository->interactionCounts((int) $post['id']),
            'interactionState' => $this->engagementRepository->interactionState((int) $post['id'], $identity),
            'readingHistory' => $this->engagementRepository->recentHistory($identity, 5),
            'activityFeed' => $this->engagementRepository->recentActivity(5),
            'notifications' => is_array($this->currentUser()) && isset($this->currentUser()['id']) ? $this->engagementRepository->notificationsForUser((int) $this->currentUser()['id'], 5) : array(),
            'notificationCount' => is_array($this->currentUser()) && isset($this->currentUser()['id']) ? $this->engagementRepository->unreadNotificationCount((int) $this->currentUser()['id']) : 0,
        ), array(
            'meta' => array(
                'title' => $this->contentTitle($post, $seo),
                'description' => $this->contentDescription($post, $seo),
                'canonical' => url('/content/' . $slug),
                'schemaType' => 'Article',
                'robots' => 'index, follow',
                'ogImage' => $featuredImage,
                'twitterCard' => 'summary_large_image',
                'schema' => $schema,
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
        $faq = array(
            array('question' => 'What is Developer Ruhban?', 'answer' => 'A content-first knowledge platform for tutorials, guides, reviews, and practical technical notes.'),
            array('question' => 'What content types does it support?', 'answer' => 'It supports a universal content model for articles, tutorials, projects, tools, resources, and more.'),
            array('question' => 'Why is SEO built in?', 'answer' => 'Because crawlability, structured data, and fast loading are core product requirements, not later additions.'),
        );

        return $this->view('site/about', array('siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban')), array(
            'meta' => array(
                'title' => 'About - Developer Ruhban',
                'description' => 'Learn what Developer Ruhban is building and why the platform exists.',
                'canonical' => url('/about'),
                'schemaType' => 'AboutPage',
                'robots' => 'index, follow',
                'ogImage' => asset('assets/images/seo-card.svg'),
                'faq' => $faq,
                'schema' => array(
                    array(
                        '@context' => 'https://schema.org',
                        '@type' => 'FAQPage',
                        'mainEntity' => array_map(static function (array $item): array {
                            return array(
                                '@type' => 'Question',
                                'name' => $item['question'],
                                'acceptedAnswer' => array(
                                    '@type' => 'Answer',
                                    'text' => $item['answer'],
                                ),
                            );
                        }, $faq),
                    ),
                ),
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'About', 'url' => url('/about')),
            )),
            'faq' => $faq,
        ));
    }

    public function contact(Request $request)
    {
        return $this->view('site/contact', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'flashSuccess' => $this->app->session()->pullFlash('success'),
            'flashError' => $this->app->session()->pullFlash('error'),
        ), array(
            'meta' => array(
                'title' => 'Contact - Developer Ruhban',
                'description' => 'Reach out to the Developer Ruhban team for collaborations, feedback, or support.',
                'canonical' => url('/contact'),
                'schemaType' => 'ContactPage',
                'robots' => 'index, follow',
                'ogImage' => asset('assets/images/seo-card.svg'),
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
                'ogImage' => asset('assets/images/seo-card.svg'),
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
                'ogImage' => asset('assets/images/seo-card.svg'),
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

    private function optimizeContentImages(string $html): string
    {
        if ($html === '' || stripos($html, '<img') === false) {
            return $html;
        }

        return preg_replace('/<img\b(?![^>]*\bloading=)([^>]*?)>/i', '<img loading="lazy" decoding="async"$1>', $html) ?: $html;
    }

    private function breadcrumbs(array $items): array
    {
        return $items;
    }

    public function lab(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $categorySlug = trim((string) $request->input('category', ''));
        $tagSlug = trim((string) $request->input('tag', ''));
        $status = trim((string) $request->input('status', ''));
        $productType = trim((string) $request->input('product_type', ''));

        $filters = array(
            'type_slug' => 'product',
            'search' => $search,
            'category_slug' => $categorySlug,
            'tag_slug' => $tagSlug,
        );

        $results = $this->postRepository->publicListing($filters, (int) $request->input('page', 1), 12);
        
        if ($status !== '' || $productType !== '') {
            $filteredData = array();
            foreach ($results['data'] as $post) {
                $metaFields = $this->metaRepository->getByPostId($post['id']);
                $postStatus = isset($metaFields['product_status']) ? trim(strtolower((string)$metaFields['product_status'])) : '';
                $postType = isset($metaFields['product_type']) ? trim(strtolower((string)$metaFields['product_type'])) : '';
                
                if ($status !== '' && strtolower($status) !== $postStatus) {
                    continue;
                }
                if ($productType !== '' && strtolower($productType) !== $postType) {
                    continue;
                }
                $filteredData[] = $post;
            }
            $results['data'] = $filteredData;
            $results['pagination']['total'] = count($filteredData);
            $results['pagination']['pages'] = 1;
        }

        $allCategories = $this->categoryRepository->allCategories();
        $allTags = $this->tagRepository->allTags();

        return $this->view('site/lab', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'products' => $results['data'],
            'pagination' => $results['pagination'],
            'categories' => $allCategories,
            'tags' => $allTags,
            'filters' => array(
                'search' => $search,
                'category' => $categorySlug,
                'tag' => $tagSlug,
                'status' => $status,
                'product_type' => $productType
            )
        ), array(
            'meta' => array(
                'title' => 'Lab - Product Showcase',
                'description' => 'Explore the custom applications, experiments, templates, and tools built in Ruhban\'s Lab.',
                'canonical' => url('/lab'),
                'schemaType' => 'CollectionPage',
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Lab', 'url' => url('/lab')),
            )),
        ));
    }

    public function labProduct(Request $request, string $slug)
    {
        $post = $this->postRepository->findPublishedBySlug($slug);

        if (!$post) {
            throw new HttpException('Product not found.', 404);
        }

        // Custom premium access block -> Teaser conversion page for Lab Products
        if (!has_post_access($post)) {
            $requiredAccess = strtolower(trim((string) ($post['visibility'] ?? 'pro')));
            $categories = $this->mapItemsByIds($this->categoryRepository->allCategories(), $this->postRepository->categoriesForPost($post['id']));
            $catIds = array_map(static function (array $item): int { return (int) $item['id']; }, $categories);
            $relatedFree = $this->postRepository->relatedPublished($post['id'], $post['content_type_id'], $catIds, 2);
            
            return $this->view('site/teaser', array(
                'post' => $post,
                'requiredAccess' => $requiredAccess,
                'relatedFree' => $relatedFree,
            ), array(
                'meta' => array(
                    'title' => 'Upgrade Required — ' . $post['title'],
                    'robots' => 'noindex, nofollow',
                ),
            ));
        }

        $categories = $this->mapItemsByIds($this->categoryRepository->allCategories(), $this->postRepository->categoriesForPost($post['id']));
        $tags = $this->mapItemsByIds($this->tagRepository->allTags(), $this->postRepository->tagsForPost($post['id']));
        $seo = $this->seoRepository->findByPostId($post['id']);
        $metaFields = $this->metaRepository->getByPostId($post['id']);

        $this->postRepository->recordView((int) $post['id']);
        $identity = $this->interactionIdentity();
        $this->engagementRepository->recordHistory((int) $post['id'], $identity);

        $featuredImage = !empty($post['featured_image']) ? asset($post['featured_image']) : asset('assets/images/seo-card.svg');
        $contentHtml = $this->optimizeContentImages((string) $post['content']);

        return $this->view('site/lab-detail', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'post' => $post,
            'contentHtml' => $contentHtml,
            'categories' => $categories,
            'tags' => $tags,
            'seo' => $seo ?: array(),
            'metaFields' => $metaFields,
            'author' => $this->userRepository->findByUsername($post['author_username']),
            'comments' => $this->engagementRepository->commentsForPost((int) $post['id']),
            'interactionCounts' => $this->engagementRepository->interactionCounts((int) $post['id']),
            'interactionState' => $this->engagementRepository->interactionState((int) $post['id'], $identity),
            'relatedProducts' => $this->postRepository->relatedPublished($post['id'], $post['content_type_id'], array_map(static function (array $item): int {
                return (int) $item['id'];
            }, $categories), 3),
        ), array(
            'meta' => array(
                'title' => $this->contentTitle($post, $seo),
                'description' => $this->contentDescription($post, $seo),
                'canonical' => url('/lab/' . $slug),
                'schemaType' => 'SoftwareApplication',
                'robots' => 'index, follow',
                'ogImage' => $featuredImage,
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Lab', 'url' => url('/lab')),
                array('label' => $post['title'], 'url' => url('/lab/' . $slug)),
            )),
        ));
    }

    public function showLogin(Request $request)
    {
        return $this->view('site/login', array(
            'errors' => array(),
            'old' => array(),
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
        ), array(
            'meta' => array(
                'title' => 'Sign In - Developer Ruhban',
                'description' => 'Login to your account to browse developer content and access Lab projects.',
                'canonical' => url('/login'),
                'robots' => 'noindex, nofollow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Login', 'url' => url('/login')),
            )),
        ));
    }

    public function login(Request $request)
    {
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $errors = array();

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if ($errors !== array()) {
            return $this->view('site/login', array(
                'errors' => $errors,
                'old' => array('email' => $email),
                'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            ), array(
                'meta' => array('title' => 'Sign In - Developer Ruhban', 'robots' => 'noindex, nofollow')
            ));
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, (string) $user['password'])) {
            return $this->view('site/login', array(
                'errors' => array('general' => 'Invalid email or password.'),
                'old' => array('email' => $email),
                'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            ), array(
                'meta' => array('title' => 'Sign In - Developer Ruhban', 'robots' => 'noindex, nofollow')
            ));
        }

        if (empty($user['is_active'])) {
            return $this->view('site/login', array(
                'errors' => array('general' => 'Your account is deactivated.'),
                'old' => array('email' => $email),
                'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            ), array(
                'meta' => array('title' => 'Sign In - Developer Ruhban', 'robots' => 'noindex, nofollow')
            ));
        }

        // Store session
        $this->app->session()->set((string) $this->app->config()->get('auth.session_key', 'auth_user'), array(
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role_slug'],
            'role_name' => $user['role_name'],
        ));

        $this->userRepository->updateLastLogin($user['id']);
        $this->app->session()->flash('success', 'Welcome back, ' . $user['name'] . '.');

        return $this->redirect('/lab');
    }

    public function logout(Request $request)
    {
        $this->app->session()->forget((string) $this->app->config()->get('auth.session_key', 'auth_user'));
        $this->app->session()->flash('success', 'You have been signed out.');

        return $this->redirect('/login');
    }

    public function showSignup(Request $request)
    {
        return $this->view('site/signup', array(
            'errors' => array(),
            'old' => array(),
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
        ), array(
            'meta' => array(
                'title' => 'Create Account - Developer Ruhban',
                'description' => 'Register a free account to explore lab projects and articles.',
                'canonical' => url('/signup'),
                'robots' => 'noindex, nofollow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Register', 'url' => url('/signup')),
            )),
        ));
    }

    public function signup(Request $request)
    {
        $name = trim((string) $request->input('name', ''));
        $username = trim((string) $request->input('username', ''));
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $errors = array();

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }
        if ($username === '' || strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if ($errors === array()) {
            $existingUser = $this->userRepository->findByEmail($email);
            if ($existingUser) {
                $errors['email'] = 'Email is already registered.';
            }
            $existingUsername = $this->userRepository->findByUsername($username);
            if ($existingUsername) {
                $errors['username'] = 'Username is already taken.';
            }
        }

        if ($errors !== array()) {
            return $this->view('site/signup', array(
                'errors' => $errors,
                'old' => array('name' => $name, 'username' => $username, 'email' => $email),
                'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            ), array(
                'meta' => array('title' => 'Create Account - Developer Ruhban', 'robots' => 'noindex, nofollow')
            ));
        }

        // Get role ID for 'visitor' or default role
        $roleId = 4; // Assuming visitor role is 4 as standard from schema setup
        
        $payload = array(
            'role_id' => $roleId,
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $db = $this->app->database()->connection();
        $stmt = $db->prepare('INSERT INTO users (role_id, username, name, email, password, is_active, created_at, updated_at) VALUES (:role_id, :username, :name, :email, :password, :is_active, :created_at, :updated_at)');
        $stmt->execute($payload);

        $userId = (int) $db->lastInsertId();
        if ($userId > 0) {
            $membershipRepository = new \App\Repositories\MembershipRepository($db);
            $freePlan = $membershipRepository->findPlanBySlug('free');
            if ($freePlan) {
                $membershipRepository->assignPlan($userId, (int) $freePlan['id']);
            }
        }

        $this->app->session()->flash('success', 'Registration successful! Please sign in.');
        return $this->redirect('/login');
    }

    public function membershipPlans(Request $request)
    {
        $connection = $this->app->database()->connection();
        $membershipRepository = new \App\Repositories\MembershipRepository($connection);
        $plans = $membershipRepository->allPlans();

        $currentUser = $this->currentUser();
        $activeMembership = null;
        if (is_array($currentUser) && isset($currentUser['id'])) {
            $activeMembership = $membershipRepository->getActiveMembership((int) $currentUser['id']);
        }

        return $this->view('site/membership', array(
            'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
            'plans' => $plans,
            'activeMembership' => $activeMembership,
            'currentUser' => $currentUser,
        ), array(
            'meta' => array(
                'title' => 'Membership Plans - Ruhban\'s Lab',
                'description' => 'Unlock premium developer tutorials, templates, downloads, and resources in Ruhban\'s Lab.',
                'canonical' => url('/membership'),
                'robots' => 'index, follow',
            ),
            'breadcrumbs' => $this->breadcrumbs(array(
                array('label' => 'Home', 'url' => url('/')),
                array('label' => 'Membership', 'url' => url('/membership')),
            )),
        ));
    }

    public function downloadProduct(Request $request, $id)
    {
        $id = (int) $id;
        $post = $this->postRepository->findPublishedById($id);

        if (!$post) {
            throw new HttpException('Product not found.', 404);
        }

        $metaFields = $this->metaRepository->getByPostId($id);

        if (!has_product_feature_access($post, 'download', $metaFields)) {
            $this->app->session()->flash('error', 'You do not have permission to download this product. Please upgrade your plan.');
            return $this->redirect('/membership');
        }

        $downloadUrl = trim((string) ($metaFields['download_url'] ?? ''));
        if ($downloadUrl === '') {
            $this->app->session()->flash('error', 'Download link not available.');
            return $this->redirect('/lab/' . $post['slug']);
        }

        $currentCount = (int) ($metaFields['download_count'] ?? 0);
        $metaFields['download_count'] = $currentCount + 1;
        $this->metaRepository->syncMeta($id, $metaFields);

        return $this->redirect($downloadUrl);
    }

    public function publicPricing(Request $request)
    {
        $db = $this->app->database()->connection();
        $membershipRepository = new \App\Repositories\MembershipRepository($db);
        $plans = $membershipRepository->allPlans();
        
        $currentUser = $this->currentUser();
        $activeMembership = $currentUser ? $membershipRepository->getActiveMembership((int) $currentUser['id']) : null;

        return $this->view('site/pricing', array(
            'plans' => $plans,
            'activeMembership' => $activeMembership,
        ), array(
            'meta' => array(
                'title' => 'Pricing Plans — Ruhban\'s Lab',
                'description' => 'Compare our Free, Pro, and Lifetime plans and unlock premium codebase resources.',
                'robots' => 'index, follow',
            ),
        ));
    }

    public function simulatedPurchase(Request $request)
    {
        $productId = (int) $request->input('product_id', 0);
        $post = $this->postRepository->findPublishedById($productId);

        if (!$post) {
            throw new HttpException('Product not found.', 404);
        }

        $session = $this->app->session();
        $purchasedIds = $session->get('purchased_product_ids', array());
        if (!is_array($purchasedIds)) {
            $purchasedIds = array();
        }

        if (!in_array($productId, $purchasedIds, true)) {
            $purchasedIds[] = $productId;
            $session->set('purchased_product_ids', $purchasedIds);
        }

        // Record a download/purchase event in user activity timeline
        $currentUser = $this->currentUser();
        if ($currentUser) {
            $db = $this->app->database()->connection();
            $stmt = $db->prepare('INSERT INTO activity_events (user_id, post_id, event_type, created_at) VALUES (:uid, :pid, "download", :created)');
            $stmt->execute(array(
                'uid' => (int) $currentUser['id'],
                'pid' => $productId,
                'created' => date('Y-m-d H:i:s'),
            ));
        }

        $session->flash('success', 'Purchase simulated successfully! Premium product unlocked.');
        return $this->redirect('/lab/' . $post['slug']);
    }
}
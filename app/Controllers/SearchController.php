<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Request;
use App\Repositories\CategoryRepository;
use App\Repositories\ContentTypeRepository;
use App\Repositories\PostRepository;
use App\Repositories\SearchAnalyticsRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;

final class SearchController extends BaseController
{
    private PostRepository $postRepository;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;
    private ContentTypeRepository $contentTypeRepository;
    private UserRepository $userRepository;
    private SearchAnalyticsRepository $searchAnalyticsRepository;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $connection = $this->app->database()->connection();
        $this->postRepository = new PostRepository($connection);
        $this->categoryRepository = new CategoryRepository($connection);
        $this->tagRepository = new TagRepository($connection);
        $this->contentTypeRepository = new ContentTypeRepository($connection);
        $this->userRepository = new UserRepository($connection);
        $this->searchAnalyticsRepository = new SearchAnalyticsRepository($connection);
    }

    public function index(Request $request)
    {
        $context = $this->buildSearchContext($request);

        return $this->view('site/search', $context['data'], $context['options']);
    }

    public function suggest(Request $request)
    {
        $term = trim((string) $request->input('term', $request->input('q', $request->input('search', ''))));

        return $this->json(array(
            'term' => $term,
            'suggestions' => $this->buildSuggestions($term),
            'popularSearches' => $this->searchAnalyticsRepository->popularSearches(8),
            'recentSearches' => $this->searchAnalyticsRepository->recentSearches(8),
            'relatedSearches' => $term !== '' ? $this->searchAnalyticsRepository->relatedSearches($term, 8) : array(),
        ));
    }

    public function instant(Request $request)
    {
        $context = $this->buildSearchContext($request, true);
        $results = array_map(array($this, 'decorateResultUrl'), $context['data']['results']['data']);

        return $this->json(array(
            'query' => $context['data']['query'],
            'filters' => $context['data']['filters'],
            'results' => $results,
            'pagination' => $context['data']['results']['pagination'],
            'suggestions' => $context['data']['suggestions'],
            'popularSearches' => $context['data']['popularSearches'],
            'recentSearches' => $context['data']['recentSearches'],
            'relatedSearches' => $context['data']['relatedSearches'],
            'analytics' => $context['data']['analytics'],
        ));
    }

    private function buildSearchContext(Request $request, bool $skipLogging = false): array
    {
        $filters = $this->searchFilters($request);
        $page = (int) $request->input('page', 1);
        $results = $this->postRepository->publicListing($filters, $page, 12);
        $query = (string) $filters['search'];

        if (!$skipLogging && $query !== '') {
            $this->searchAnalyticsRepository->logSearch($query, $filters, (int) $results['pagination']['total'], array(
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->header('User-Agent', ''),
                'referrer' => (string) $request->header('Referer', ''),
            ));

            $this->postRepository->recordSearchResults($results['data']);
        }

        $analytics = $this->searchAnalyticsRepository->analyticsSummary();
        $popularSearches = $this->searchAnalyticsRepository->popularSearches(8);
        $recentSearches = $this->searchAnalyticsRepository->recentSearches(8);
        $relatedSearches = $query !== '' ? $this->searchAnalyticsRepository->relatedSearches($query, 8) : array();
        $suggestions = $this->buildSuggestions($query);
        $featuredPosts = $this->postRepository->featuredPublished(6);
        $trendingPosts = $this->postRepository->trendingPublished(6);
        $popularPosts = $this->postRepository->popularPublished(6);
        $recentlyUpdatedPosts = $this->postRepository->recentlyUpdatedPublished(6);
        $collectionsPosts = $this->postRepository->publicListing(array('type_slug' => 'collections', 'sort' => 'newest'), 1, 6)['data'];
        $recommendedPosts = $query !== '' ? $this->postRepository->publicListing(array_merge($filters, array('search' => $query, 'sort' => 'relevance')), 1, 6)['data'] : $featuredPosts;
        $canonical = url('/search');
        $robots = $query !== '' || $this->hasAdvancedFilters($filters) ? 'noindex, follow' : 'index, follow';
        $title = $query !== '' ? 'Search results for ' . $query . ' - Developer Ruhban' : 'Search & Discovery - Developer Ruhban';
        $description = $query !== '' ? 'Explore matching content, related searches, and discovery recommendations for ' . $query . '.' : 'Search the public knowledge base and explore trending, popular, and recently updated content.';

        return array(
            'data' => array(
                'siteName' => (string) $this->app->config()->get('app.name', 'Developer Ruhban'),
                'query' => $query,
                'filters' => $filters,
                'results' => $results,
                'analytics' => $analytics,
                'popularSearches' => $popularSearches,
                'recentSearches' => $recentSearches,
                'relatedSearches' => $relatedSearches,
                'suggestions' => $suggestions,
                'featuredPosts' => $featuredPosts,
                'trendingPosts' => $trendingPosts,
                'popularPosts' => $popularPosts,
                'recentlyUpdatedPosts' => $recentlyUpdatedPosts,
                'collectionsPosts' => $collectionsPosts,
                'recommendedPosts' => $recommendedPosts,
                'contentTypes' => $this->contentTypeRepository->allTypes(),
                'categories' => $this->categoryRepository->tree(),
                'tags' => array_slice($this->tagRepository->allTags(), 0, 12),
                'authors' => $this->userRepository->recent(8),
                'archiveMonths' => $this->postRepository->archiveMonths(12),
                'availableSorts' => $this->sortOptions(),
            ),
            'options' => array(
                'meta' => array(
                    'title' => $title,
                    'description' => $description,
                    'canonical' => $canonical,
                    'schemaType' => 'WebPage',
                    'robots' => $robots,
                ),
                'breadcrumbs' => $this->breadcrumbs(array(
                    array('label' => 'Home', 'url' => url('/')),
                    array('label' => 'Search', 'url' => $canonical),
                )),
            ),
        );
    }

    private function searchFilters(Request $request): array
    {
        return array(
            'search' => trim((string) $request->input('q', $request->input('search', ''))),
            'type_slug' => trim((string) $request->input('type', '')),
            'category_slug' => trim((string) $request->input('category', '')),
            'tag_slug' => trim((string) $request->input('tag', '')),
            'author_username' => trim((string) $request->input('author', '')),
            'year' => (int) $request->input('year', 0),
            'month' => (int) $request->input('month', 0),
            'featured' => (int) $request->input('featured', 0),
            'sort' => trim((string) $request->input('sort', 'relevance')),
        );
    }

    private function buildSuggestions(string $term): array
    {
        $term = trim($term);
        $suggestions = array();
        $seen = array();

        $sources = array(
            $this->searchAnalyticsRepository->suggestions($term, 8),
            $this->contentMatches($term),
        );

        foreach ($sources as $source) {
            foreach ($source as $item) {
                $label = isset($item['label']) ? trim((string) $item['label']) : '';
                $key = strtolower($label . '|' . (string) ($item['kind'] ?? ''));

                if ($label === '' || isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;
                $suggestions[] = $item;
            }
        }

        return array_slice($suggestions, 0, 10);
    }

    private function contentMatches(string $term): array
    {
        $term = trim($term);
        $results = array();
        $needle = function_exists('mb_strtolower') ? mb_strtolower($term, 'UTF-8') : strtolower($term);

        foreach ($this->postRepository->publicListing(array('search' => $term, 'sort' => 'relevance'), 1, 6)['data'] as $post) {
            $results[] = array(
                'label' => $post['title'],
                'value' => $post['title'],
                'kind' => 'content',
                'href' => url('/content/' . $post['slug']),
                'meta' => $post['content_type_name'],
            );
        }

        if ($needle !== '') {
            foreach ($this->contentTypeRepository->allTypes() as $type) {
                $typeName = isset($type['name']) ? (string) $type['name'] : '';
                $typeSlug = isset($type['slug']) ? (string) $type['slug'] : '';
                $typeNeedle = function_exists('mb_strtolower') ? mb_strtolower($typeName, 'UTF-8') : strtolower($typeName);
                if ($typeName !== '' && strpos($typeNeedle, $needle) !== false) {
                    $results[] = array(
                        'label' => $typeName,
                        'value' => $typeName,
                        'kind' => 'type',
                        'href' => url('/type/' . $typeSlug),
                        'meta' => 'Content type',
                    );
                }
            }

            foreach ($this->categoryRepository->allCategories() as $category) {
                $categoryName = isset($category['name']) ? (string) $category['name'] : '';
                $categorySlug = isset($category['slug']) ? (string) $category['slug'] : '';
                $categoryNeedle = function_exists('mb_strtolower') ? mb_strtolower($categoryName, 'UTF-8') : strtolower($categoryName);
                if ($categoryName !== '' && strpos($categoryNeedle, $needle) !== false) {
                    $results[] = array(
                        'label' => $categoryName,
                        'value' => $categoryName,
                        'kind' => 'category',
                        'href' => url('/category/' . $categorySlug),
                        'meta' => 'Category',
                    );
                }
            }

            foreach ($this->tagRepository->allTags() as $tag) {
                $tagName = isset($tag['name']) ? (string) $tag['name'] : '';
                $tagSlug = isset($tag['slug']) ? (string) $tag['slug'] : '';
                $tagNeedle = function_exists('mb_strtolower') ? mb_strtolower($tagName, 'UTF-8') : strtolower($tagName);
                if ($tagName !== '' && strpos($tagNeedle, $needle) !== false) {
                    $results[] = array(
                        'label' => $tagName,
                        'value' => $tagName,
                        'kind' => 'tag',
                        'href' => url('/tag/' . $tagSlug),
                        'meta' => 'Tag',
                    );
                }
            }

            foreach ($this->userRepository->recent(8) as $author) {
                $authorName = isset($author['name']) ? (string) $author['name'] : '';
                $authorUsername = isset($author['username']) ? (string) $author['username'] : '';
                $authorNeedle = function_exists('mb_strtolower') ? mb_strtolower($authorName, 'UTF-8') : strtolower($authorName);
                if ($authorName !== '' && strpos($authorNeedle, $needle) !== false) {
                    $results[] = array(
                        'label' => $authorName,
                        'value' => $authorName,
                        'kind' => 'author',
                        'href' => url('/author/' . $authorUsername),
                        'meta' => 'Author',
                    );
                }
            }
        }

        return $results;
    }

    private function hasAdvancedFilters(array $filters): bool
    {
        foreach (array('type_slug', 'category_slug', 'tag_slug', 'author_username', 'year', 'month', 'featured') as $key) {
            if (!empty($filters[$key])) {
                return true;
            }
        }

        return isset($filters['sort']) && $filters['sort'] !== '' && $filters['sort'] !== 'relevance';
    }

    private function sortOptions(): array
    {
        return array(
            array('value' => 'relevance', 'label' => 'Relevance'),
            array('value' => 'newest', 'label' => 'Newest'),
            array('value' => 'updated', 'label' => 'Recently updated'),
            array('value' => 'popular', 'label' => 'Popular'),
            array('value' => 'featured', 'label' => 'Featured'),
            array('value' => 'title', 'label' => 'Title A-Z'),
            array('value' => 'oldest', 'label' => 'Oldest'),
        );
    }

    private function decorateResultUrl(array $post): array
    {
        $post['url'] = url('/content/' . $post['slug']);

        return $post;
    }

    private function breadcrumbs(array $items): array
    {
        return $items;
    }
}
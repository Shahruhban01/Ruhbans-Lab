<?php
$results = isset($results['data']) ? $results['data'] : array();
$pagination = isset($results['pagination']) ? $results['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
$filters = isset($filters) && is_array($filters) ? $filters : array();
$query = isset($query) ? (string) $query : '';
$analytics = isset($analytics) && is_array($analytics) ? $analytics : array();
$popularSearches = isset($popularSearches) && is_array($popularSearches) ? $popularSearches : array();
$recentSearches = isset($recentSearches) && is_array($recentSearches) ? $recentSearches : array();
$relatedSearches = isset($relatedSearches) && is_array($relatedSearches) ? $relatedSearches : array();
$suggestions = isset    ($suggestions) && is_array($suggestions) ? $suggestions : array();
$featuredPosts = isset($featuredPosts) && is_array($featuredPosts) ? $featuredPosts : array();
$trendingPosts = isset($trendingPosts) && is_array($trendingPosts) ? $trendingPosts : array();
$popularPosts = isset($popularPosts) && is_array($popularPosts) ? $popularPosts : array();
$recentlyUpdatedPosts = isset($recentlyUpdatedPosts) && is_array($recentlyUpdatedPosts) ? $recentlyUpdatedPosts : array();
$collectionsPosts = isset($collectionsPosts) && is_array($collectionsPosts) ? $collectionsPosts : array();
$recommendedPosts = isset($recommendedPosts) && is_array($recommendedPosts) ? $recommendedPosts : array();
$contentTypes = isset($contentTypes) && is_array($contentTypes) ? $contentTypes : array();
$categories = isset($categories) && is_array($categories) ? $categories : array();
$tags = isset($tags) && is_array($tags) ? $tags : array();
$authors = isset($authors) && is_array($authors) ? $authors : array();
$availableSorts = isset($availableSorts) && is_array($availableSorts) ? $availableSorts : array();
?>
<section class="container page-stack search-page">
    <section class="page-hero card-surface">
        <p class="eyebrow">Search &amp; discovery</p>
        <h1><?php echo e($query !== '' ? 'Search results for ' . $query : 'Find content across every module'); ?></h1>
        <p class="lead"><?php echo e($query !== '' ? 'Use advanced filters, related searches, and discovery panels to refine the result set.' : 'Search every supported content type with autocomplete, instant updates, and curated discovery sections.'); ?></p>

        <form class="site-search site-search--wide search-form" action="<?php echo e(url('/search')); ?>" method="get" data-search-form data-search-suggest-endpoint="<?php echo e(url('/search/suggest')); ?>" data-search-instant-endpoint="<?php echo e(url('/search/instant')); ?>">
            <div class="search-input-wrap">
                <input type="search" name="q" value="<?php echo e($query); ?>" placeholder="Search posts, categories, tags, authors, or content types" data-search-input autocomplete="off">
                <div class="search-suggestions" data-search-suggestions hidden></div>
            </div>
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <div class="search-stats">
            <div class="stat-card card-surface">
                <span>Total searches</span>
                <strong><?php echo e($analytics['total_searches'] ?? 0); ?></strong>
            </div>
            <div class="stat-card card-surface">
                <span>Searches this week</span>
                <strong><?php echo e($analytics['last_7_days'] ?? 0); ?></strong>
            </div>
            <div class="stat-card card-surface">
                <span>Unique terms</span>
                <strong><?php echo e($analytics['unique_terms'] ?? 0); ?></strong>
            </div>
            <div class="stat-card card-surface">
                <span>Average results</span>
                <strong><?php echo e($analytics['average_results'] ?? 0); ?></strong>
            </div>
        </div>
    </section>

    <section class="search-toolbar card-surface panel">
        <div class="chip-grid">
            <?php foreach ($popularSearches as $searchItem) : ?>
                <a class="chip" href="<?php echo e(url('/search?q=' . rawurlencode($searchItem['search_term']))); ?>"><?php echo e($searchItem['search_term']); ?></a>
            <?php endforeach; ?>
            <?php foreach ($recentSearches as $searchItem) : ?>
                <a class="chip chip--subtle" href="<?php echo e(url('/search?q=' . rawurlencode($searchItem['search_term']))); ?>"><?php echo e($searchItem['search_term']); ?></a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="search-layout">
        <div class="search-results-column">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Results</p>
                    <h2><?php echo e($query !== '' ? 'Matching content' : 'Start searching to see matches'); ?></h2>
                </div>
                <span class="search-summary" data-search-summary><?php echo e($pagination['total']); ?> results</span>
            </div>

            <div class="post-grid post-grid--search" data-search-results>
                <?php if ($results === array()) : ?>
                    <article class="card-surface empty-state">
                        <h2>No results yet</h2>
                        <p>Try a different keyword, remove a filter, or use the discovery panels below.</p>
                    </article>
                <?php endif; ?>
                <?php foreach ($results as $post) : ?>
                    <article class="post-card card-surface">
                        <p class="post-card__meta"><?php echo e($post['content_type_name']); ?></p>
                        <h2><a href="<?php echo e(url('/content/' . $post['slug'])); ?>"><?php echo e($post['title']); ?></a></h2>
                        <p><?php echo e($post['excerpt'] ?: 'Open the content page for more context.'); ?></p>
                        <div class="post-card__footer">
                            <span><?php echo e($post['author_name']); ?></span>
                            <span><?php echo e(isset($post['view_count']) ? $post['view_count'] : 0); ?> views</span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="pagination-summary pagination-summary--public">
                <span>Page <?php echo e($pagination['page']); ?> of <?php echo e($pagination['pages']); ?></span>
                <span>Total posts: <?php echo e($pagination['total']); ?></span>
            </div>

            <div class="search-discovery-grid">
                <section class="card-surface panel">
                    <div class="section-head section-head--compact">
                        <div>
                            <p class="eyebrow">Featured</p>
                            <h2>Highlighted content</h2>
                        </div>
                    </div>
                    <div class="post-list-mini">
                        <?php foreach ($featuredPosts as $post) : ?>
                            <a href="<?php echo e(url('/content/' . $post['slug'])); ?>">
                                <strong><?php echo e($post['title']); ?></strong>
                                <span><?php echo e($post['content_type_name']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="card-surface panel">
                    <div class="section-head section-head--compact">
                        <div>
                            <p class="eyebrow">Trending</p>
                            <h2>Popular now</h2>
                        </div>
                    </div>
                    <div class="post-list-mini">
                        <?php foreach ($trendingPosts as $post) : ?>
                            <a href="<?php echo e(url('/content/' . $post['slug'])); ?>">
                                <strong><?php echo e($post['title']); ?></strong>
                                <span><?php echo e($post['content_type_name']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="card-surface panel">
                    <div class="section-head section-head--compact">
                        <div>
                            <p class="eyebrow">Recently updated</p>
                            <h2>Fresh content</h2>
                        </div>
                    </div>
                    <div class="post-list-mini">
                        <?php foreach ($recentlyUpdatedPosts as $post) : ?>
                            <a href="<?php echo e(url('/content/' . $post['slug'])); ?>">
                                <strong><?php echo e($post['title']); ?></strong>
                                <span><?php echo e($post['content_type_name']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="card-surface panel">
                    <div class="section-head section-head--compact">
                        <div>
                            <p class="eyebrow">Popular</p>
                            <h2>Most viewed</h2>
                        </div>
                    </div>
                    <div class="post-list-mini">
                        <?php foreach ($popularPosts as $post) : ?>
                            <a href="<?php echo e(url('/content/' . $post['slug'])); ?>">
                                <strong><?php echo e($post['title']); ?></strong>
                                <span><?php echo e($post['content_type_name']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>

        <aside class="search-sidebar sidebar-stack">
            <section class="card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Advanced search</p>
                        <h2>Filters and sorting</h2>
                    </div>
                </div>
                <form class="search-filters" action="<?php echo e(url('/search')); ?>" method="get">
                    <label>
                        <span>Query</span>
                        <input type="search" name="q" value="<?php echo e($query); ?>" placeholder="Keyword">
                    </label>
                    <label>
                        <span>Content type</span>
                        <select name="type">
                            <option value="">All types</option>
                            <?php foreach ($contentTypes as $type) : ?>
                                <option value="<?php echo e($type['slug']); ?>"<?php echo isset($filters['type_slug']) && $filters['type_slug'] === $type['slug'] ? ' selected' : ''; ?>><?php echo e($type['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        <span>Sort</span>
                        <select name="sort">
                            <?php foreach ($availableSorts as $sortOption) : ?>
                                <option value="<?php echo e($sortOption['value']); ?>"<?php echo isset($filters['sort']) && $filters['sort'] === $sortOption['value'] ? ' selected' : ''; ?>><?php echo e($sortOption['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        <span>Category</span>
                        <select name="category">
                            <option value="">All categories</option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?php echo e($category['slug']); ?>"<?php echo isset($filters['category_slug']) && $filters['category_slug'] === $category['slug'] ? ' selected' : ''; ?>><?php echo e($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        <span>Tag</span>
                        <select name="tag">
                            <option value="">All tags</option>
                            <?php foreach ($tags as $tag) : ?>
                                <option value="<?php echo e($tag['slug']); ?>"<?php echo isset($filters['tag_slug']) && $filters['tag_slug'] === $tag['slug'] ? ' selected' : ''; ?>><?php echo e($tag['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        <span>Author</span>
                        <select name="author">
                            <option value="">All authors</option>
                            <?php foreach ($authors as $author) : ?>
                                <option value="<?php echo e($author['username']); ?>"<?php echo isset($filters['author_username']) && $filters['author_username'] === $author['username'] ? ' selected' : ''; ?>><?php echo e($author['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="search-filter-grid">
                        <label>
                            <span>Year</span>
                            <input type="number" name="year" value="<?php echo e(isset($filters['year']) ? $filters['year'] : ''); ?>" min="2000" max="2100">
                        </label>
                        <label>
                            <span>Month</span>
                            <input type="number" name="month" value="<?php echo e(isset($filters['month']) ? $filters['month'] : ''); ?>" min="1" max="12">
                        </label>
                    </div>
                    <label class="search-checkbox">
                        <input type="checkbox" name="featured" value="1"<?php echo !empty($filters['featured']) ? ' checked' : ''; ?>>
                        <span>Featured only</span>
                    </label>
                    <button class="btn btn-primary" type="submit">Apply filters</button>
                </form>
            </section>

            <section class="card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Related searches</p>
                        <h2>Useful follow-ups</h2>
                    </div>
                </div>
                <div class="chip-grid">
                    <?php foreach ($relatedSearches as $searchItem) : ?>
                        <a class="chip" href="<?php echo e(url('/search?q=' . rawurlencode($searchItem['search_term']))); ?>"><?php echo e($searchItem['search_term']); ?></a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Collections</p>
                        <h2>Curated sets</h2>
                    </div>
                </div>
                <div class="post-list-mini">
                    <?php foreach ($collectionsPosts as $post) : ?>
                        <a href="<?php echo e(url('/content/' . $post['slug'])); ?>">
                            <strong><?php echo e($post['title']); ?></strong>
                            <span><?php echo e($post['content_type_name']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Recommendations</p>
                        <h2>Suggested content</h2>
                    </div>
                </div>
                <div class="post-list-mini">
                    <?php foreach ($recommendedPosts as $post) : ?>
                        <a href="<?php echo e(url('/content/' . $post['slug'])); ?>">
                            <strong><?php echo e($post['title']); ?></strong>
                            <span><?php echo e($post['content_type_name']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </aside>
    </section>
</section>
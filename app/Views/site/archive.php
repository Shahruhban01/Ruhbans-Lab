<?php
$archivePosts = isset($posts['data']) ? $posts['data'] : array();
$pagination = isset($posts['pagination']) ? $posts['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
$filters = isset($filters) && is_array($filters) ? $filters : array();
?>
<section class="container page-stack">
    <section class="page-hero card-surface">
        <p class="eyebrow">Archive</p>
        <h1>Browse published content</h1>
        <p class="lead">Search across the public knowledge base by keyword, content type, category, tag, author, or date.</p>
        <form class="site-search site-search--wide" action="<?php echo e(url('/search')); ?>" method="get" data-search-form data-search-suggest-endpoint="<?php echo e(url('/search/suggest')); ?>">
            <input type="search" name="q" value="<?php echo e(isset($filters['search']) ? $filters['search'] : ''); ?>" placeholder="Search the archive" data-search-input autocomplete="off">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </section>

    <section class="archive-layout">
        <div class="post-grid">
            <?php if ($archivePosts === array()) : ?>
                <article class="card-surface empty-state">
                    <h2>No results found</h2>
                    <p>Try a broader search or remove one of the filters.</p>
                </article>
            <?php endif; ?>
            <?php foreach ($archivePosts as $post) : ?>
                <article class="post-card card-surface">
                    <p class="post-card__meta"><?php echo e($post['content_type_name']); ?></p>
                    <h2><a href="<?php echo e(url('/content/' . $post['slug'])); ?>"><?php echo e($post['title']); ?></a></h2>
                    <p><?php echo e($post['excerpt'] ?: 'Explore the full article for details.'); ?></p>
                    <div class="post-card__footer">
                        <span><?php echo e($post['author_name']); ?></span>
                        <span><?php echo e(isset($post['published_at']) && $post['published_at'] ? $post['published_at'] : $post['created_at']); ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <aside class="sidebar-stack">
            <section class="card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Filters</p>
                        <h2>Content types</h2>
                    </div>
                </div>
                <div class="chip-grid">
                    <?php foreach ($contentTypes as $type) : ?>
                        <a class="chip" href="<?php echo e(url('/archive?type=' . $type['slug'])); ?>"><?php echo e($type['name']); ?></a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Categories</p>
                        <h2>Topics</h2>
                    </div>
                </div>
                <div class="archive-list archive-list--compact">
                    <?php foreach ($categories as $category) : ?>
                        <a href="<?php echo e(url('/category/' . $category['slug'])); ?>"><?php echo e($category['name']); ?></a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Archive</p>
                        <h2>Months</h2>
                    </div>
                </div>
                <div class="archive-list archive-list--compact">
                    <?php foreach ($archiveMonths as $month) : ?>
                        <a href="<?php echo e(url('/archive?year=' . $month['year'] . '&month=' . $month['month'])); ?>"><?php echo e($month['year'] . '-' . str_pad((string) $month['month'], 2, '0', STR_PAD_LEFT)); ?></a>
                    <?php endforeach; ?>
                </div>
            </section>
        </aside>
    </section>

    <div class="pagination-summary pagination-summary--public">
        <span>Page <?php echo e($pagination['page']); ?> of <?php echo e($pagination['pages']); ?></span>
        <span>Total posts: <?php echo e($pagination['total']); ?></span>
    </div>
</section>

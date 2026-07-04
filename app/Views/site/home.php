<?php
$featured = isset($featuredPosts) && is_array($featuredPosts) ? $featuredPosts : array();
$recent = isset($recentPosts) && is_array($recentPosts) ? $recentPosts : array();
$categories = isset($categories) && is_array($categories) ? $categories : array();
$tags = isset($tags) && is_array($tags) ? $tags : array();
$contentTypes = isset($contentTypes) && is_array($contentTypes) ? $contentTypes : array();
$archiveMonths = isset($archiveMonths) && is_array($archiveMonths) ? $archiveMonths : array();
?>
<section class="hero hero--home container">
    <div class="hero__copy">
        <p class="eyebrow">Public developer knowledge platform</p>
        <h1><?php echo e($siteName); ?></h1>
        <p class="lead">A content-first platform for tutorials, guides, notes, and reviews with strong SEO defaults, readable design, and a shared-hosting friendly architecture.</p>
        <form class="site-search" action="<?php echo e(url('/search')); ?>" method="get" data-search-form data-search-suggest-endpoint="<?php echo e(url('/search/suggest')); ?>">
            <input type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search content, categories, authors, or keywords" data-search-input autocomplete="off">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
        <div class="hero__actions">
            <a class="btn btn-primary" href="<?php echo e(url('/archive')); ?>">Browse archive</a>
            <a class="btn btn-secondary" href="<?php echo e(url('/about')); ?>">Learn more</a>
        </div>
    </div>
    <aside class="hero__panel card-surface panel">
        <p class="eyebrow">Publishing stack</p>
        <div class="chip-grid">
            <?php foreach ($contentTypes as $type) : ?>
                <a class="chip" href="<?php echo e(url('/type/' . $type['slug'])); ?>"><?php echo e($type['name']); ?></a>
            <?php endforeach; ?>
        </div>
    </aside>
</section>

<section class="container section-stack">
    <div class="section-head">
        <div>
            <p class="eyebrow">Knowledge modules</p>
            <h2>All content types</h2>
        </div>
        <a href="<?php echo e(url('/archive')); ?>">Open archive</a>
    </div>
    <div class="module-grid">
        <?php foreach ($contentTypes as $type) : ?>
            <article class="module-card card-surface">
                <p class="post-card__meta"><?php echo e($type['name']); ?></p>
                <h3><a href="<?php echo e(url('/type/' . $type['slug'])); ?>"><?php echo e($type['name']); ?></a></h3>
                <p><?php echo e(!empty($type['description']) ? $type['description'] : 'Browse posts in this content type.'); ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="container section-stack">
    <div class="section-head">
        <div>
            <p class="eyebrow">Featured</p>
            <h2>Highlighted content</h2>
        </div>
        <a href="<?php echo e(url('/archive?featured=1')); ?>">View featured</a>
    </div>
    <div class="post-grid post-grid--featured">
        <?php if ($featured === array()) : ?>
            <article class="card-surface empty-state">
                <h3>No featured posts yet</h3>
                <p>Publish and feature posts from the admin content system to surface them here.</p>
            </article>
        <?php endif; ?>
        <?php foreach ($featured as $post) : ?>
            <article class="post-card card-surface">
                <p class="post-card__meta"><?php echo e($post['content_type_name']); ?></p>
                <h3><a href="<?php echo e(url('/content/' . $post['slug'])); ?>"><?php echo e($post['title']); ?></a></h3>
                <p><?php echo e($post['excerpt'] ?: 'Read the full article for the complete breakdown.'); ?></p>
                <div class="post-card__footer">
                    <span><?php echo e($post['author_name']); ?></span>
                    <span><?php echo e($post['reading_time']); ?> min read</span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="container section-stack two-column-grid">
    <div>
        <div class="section-head">
            <div>
                <p class="eyebrow">Latest</p>
                <h2>Recent posts</h2>
            </div>
            <a href="<?php echo e(url('/archive')); ?>">Archive</a>
        </div>
        <div class="post-grid">
            <?php if ($recent === array()) : ?>
                <article class="card-surface empty-state">
                    <h3>No posts published yet</h3>
                    <p>Once content is published, the latest article feed will appear here.</p>
                </article>
            <?php endif; ?>
            <?php foreach ($recent as $post) : ?>
                <article class="post-card card-surface">
                    <p class="post-card__meta"><?php echo e($post['content_type_name']); ?></p>
                    <h3><a href="<?php echo e(url('/content/' . $post['slug'])); ?>"><?php echo e($post['title']); ?></a></h3>
                    <p><?php echo e($post['excerpt'] ?: 'A concise summary will appear here when excerpt is empty.'); ?></p>
                    <div class="post-card__footer">
                        <span><?php echo e($post['author_name']); ?></span>
                        <span><?php echo e(isset($post['published_at']) && $post['published_at'] ? $post['published_at'] : $post['created_at']); ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <aside class="sidebar-stack">
        <section class="card-surface panel">
            <div class="section-head section-head--compact">
                <div>
                    <p class="eyebrow">Categories</p>
                    <h2>Browse topics</h2>
                </div>
            </div>
            <div class="chip-grid">
                <?php foreach ($categories as $category) : ?>
                    <a class="chip" href="<?php echo e(url('/category/' . $category['slug'])); ?>"><?php echo e($category['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="card-surface panel">
            <div class="section-head section-head--compact">
                <div>
                    <p class="eyebrow">Archive</p>
                    <h2>Recent months</h2>
                </div>
            </div>
            <div class="archive-list">
                <?php foreach ($archiveMonths as $month) : ?>
                    <a href="<?php echo e(url('/archive?year=' . $month['year'] . '&month=' . $month['month'])); ?>"><?php echo e($month['year'] . '-' . str_pad((string) $month['month'], 2, '0', STR_PAD_LEFT)); ?> <span><?php echo e($month['total']); ?></span></a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="card-surface panel">
            <div class="section-head section-head--compact">
                <div>
                    <p class="eyebrow">Tags</p>
                    <h2>Popular labels</h2>
                </div>
            </div>
            <div class="chip-grid">
                <?php foreach ($tags as $tag) : ?>
                    <a class="chip chip--subtle" href="<?php echo e(url('/tag/' . $tag['slug'])); ?>"><?php echo e($tag['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </section>
    </aside>
</section>

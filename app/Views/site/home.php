<?php
$featured = isset($featuredPosts) && is_array($featuredPosts) ? $featuredPosts : array();
$recent = isset($recentPosts) && is_array($recentPosts) ? $recentPosts : array();
$categories = isset($categories) && is_array($categories) ? $categories : array();
$tags = isset($tags) && is_array($tags) ? $tags : array();
$contentTypes = isset($contentTypes) && is_array($contentTypes) ? $contentTypes : array();
$archiveMonths = isset($archiveMonths) && is_array($archiveMonths) ? $archiveMonths : array();
$activityFeed = isset($activityFeed) && is_array($activityFeed) ? $activityFeed : array();
$readingHistory = isset($readingHistory) && is_array($readingHistory) ? $readingHistory : array();
?>
<div class="container my-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-7 hero__copy">
            <p class="eyebrow text-primary">Public developer knowledge platform</p>
            <h1 class="display-4 fw-bold mb-3"><?php echo e($siteName); ?></h1>
            <p class="lead mb-4">A premium, content-first platform for tutorials, guides, notes, projects, and reviews with strong SEO defaults, thoughtful navigation, and a shared-hosting friendly architecture.</p>
            <form class="site-search site-search--wide d-flex gap-2 mb-4" action="<?php echo e(url('/search')); ?>" method="get" data-search-form data-search-suggest-endpoint="<?php echo e(url('/search/suggest')); ?>">
                <input type="search" name="q" class="form-control" placeholder="Search tutorials, projects, tools, and more" data-search-input autocomplete="off">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
            <div class="hero__actions">
                <a class="btn btn-primary" href="<?php echo e(url('/archive')); ?>">Browse archive</a>
                <a class="btn btn-secondary" href="<?php echo e(url('/search')); ?>">Discover content</a>
            </div>
        </div>
        <div class="col-lg-5">
            <aside class="hero__panel card-surface p-4">
                <p class="eyebrow mb-3">Publishing stack</p>
                <div class="chip-grid mb-4">
                    <?php foreach ($contentTypes as $type) : ?>
                        <a class="chip chip--subtle" href="<?php echo e(url('/type/' . $type['slug'])); ?>"><?php echo e($type['name']); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="search-stats">
                    <div class="stat-card"><span>Featured</span><strong><?php echo e(count($featured)); ?></strong></div>
                    <div class="stat-card"><span>Recent</span><strong><?php echo e(count($recent)); ?></strong></div>
                    <div class="stat-card"><span>Categories</span><strong><?php echo e(count($categories)); ?></strong></div>
                    <div class="stat-card"><span>Collections</span><strong><?php echo e(count($archiveMonths)); ?></strong></div>
                </div>
            </aside>
        </div>
    </div>
</div>

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
            <p class="eyebrow">Statistics</p>
            <h2>Platform overview</h2>
        </div>
    </div>
    <div class="search-stats">
        <div class="stat-card card-surface"><span>Featured content</span><strong><?php echo e(count($featured)); ?></strong></div>
        <div class="stat-card card-surface"><span>Latest posts</span><strong><?php echo e(count($recent)); ?></strong></div>
        <div class="stat-card card-surface"><span>Content types</span><strong><?php echo e(count($contentTypes)); ?></strong></div>
        <div class="stat-card card-surface"><span>Active topics</span><strong><?php echo e(count($categories)); ?></strong></div>
    </div>
</section>

<section class="container section-stack two-column-grid">
    <section class="card-surface panel">
        <div class="section-head section-head--compact">
            <div>
                <p class="eyebrow">Activity feed</p>
                <h2>Latest engagement</h2>
            </div>
        </div>
        <div class="post-list-mini">
            <?php if ($activityFeed === array()) : ?>
                <div class="empty-inline">No recent activity yet.</div>
            <?php endif; ?>
            <?php foreach ($activityFeed as $activity) : ?>
                <a href="<?php echo e(!empty($activity['url']) ? $activity['url'] : url('/archive')); ?>">
                    <strong><?php echo e($activity['title']); ?></strong>
                    <span><?php echo e(isset($activity['event_type']) ? ucfirst($activity['event_type']) : 'Activity'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="card-surface panel">
        <div class="section-head section-head--compact">
            <div>
                <p class="eyebrow">Reading history</p>
                <h2>Continue reading</h2>
            </div>
        </div>
        <div class="post-list-mini">
            <?php if ($readingHistory === array()) : ?>
                <div class="empty-inline">Your recent reads will appear here.</div>
            <?php endif; ?>
            <?php foreach ($readingHistory as $history) : ?>
                <a href="<?php echo e(url('/content/' . $history['slug'])); ?>">
                    <strong><?php echo e($history['title']); ?></strong>
                    <span><?php echo e(isset($history['content_type_name']) ? $history['content_type_name'] : 'Content'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</section>

<section class="container section-stack">
    <div class="section-head">
        <div>
            <p class="eyebrow">Newsletter</p>
            <h2>Stay updated</h2>
        </div>
    </div>
    <div class="card-surface panel newsletter-form">
        <p class="lead">Get occasional updates on new tutorials, projects, tools, and platform improvements.</p>
        <form class="newsletter-form__row" method="post" action="<?php echo e(url('/newsletter/subscribe')); ?>">
            <?php echo csrf_field(); ?>
            <input type="email" name="email" placeholder="Email address" required>
            <button class="btn btn-primary" type="submit">Subscribe</button>
        </form>
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

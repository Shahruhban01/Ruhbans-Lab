<?php
$taxonomyPosts = isset($posts['data']) ? $posts['data'] : array();
$pagination = isset($posts['pagination']) ? $posts['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
$author = isset($author) && is_array($author) ? $author : array();
?>
<section class="container page-stack">
    <section class="page-hero card-surface">
        <p class="eyebrow"><?php echo e($taxonomyType); ?></p>
        <h1><?php echo e($taxonomyTitle); ?></h1>
        <p class="lead"><?php echo e($taxonomyDescription); ?></p>
        <?php if ($author !== array()) : ?>
            <div class="author-banner">
                <strong><?php echo e($author['name']); ?></strong>
                <span><?php echo e($author['role_name']); ?></span>
                <?php if (!empty($author['website'])) : ?><a href="<?php echo e($author['website']); ?>" rel="noopener">Website</a><?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="archive-layout">
        <div class="post-grid">
            <?php if ($taxonomyPosts === array()) : ?>
                <article class="card-surface empty-state">
                    <h2>No content in this section yet</h2>
                    <p>Publish content that matches this category, tag, author, or content type.</p>
                </article>
            <?php endif; ?>
            <?php foreach ($taxonomyPosts as $post) : ?>
                <article class="post-card card-surface">
                    <p class="post-card__meta"><?php echo e($post['content_type_name']); ?></p>
                    <h2><a href="<?php echo e(url('/content/' . $post['slug'])); ?>"><?php echo e($post['title']); ?></a></h2>
                    <p><?php echo e($post['excerpt'] ?: 'Open the article for the full discussion.'); ?></p>
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
                        <p class="eyebrow">Types</p>
                        <h2>Content types</h2>
                    </div>
                </div>
                <div class="chip-grid">
                    <?php foreach ($contentTypes as $type) : ?>
                        <a class="chip" href="<?php echo e(url('/type/' . $type['slug'])); ?>"><?php echo e($type['name']); ?></a>
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

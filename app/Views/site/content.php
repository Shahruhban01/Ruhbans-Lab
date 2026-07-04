<?php
$categories = isset($categories) && is_array($categories) ? $categories : array();
$tags = isset($tags) && is_array($tags) ? $tags : array();
$relatedPosts = isset($relatedPosts) && is_array($relatedPosts) ? $relatedPosts : array();
$author = isset($author) && is_array($author) ? $author : array();
$seo = isset($seo) && is_array($seo) ? $seo : array();
$publishedAt = !empty($post['published_at']) ? $post['published_at'] : $post['created_at'];
?>
<article class="container content-detail" data-reading-progress-container>
    <section class="page-hero page-hero--split card-surface">
        <div>
            <div class="article-kicker">
                <a href="<?php echo e(url('/type/' . $post['content_type_slug'])); ?>"><?php echo e($post['content_type_name']); ?></a>
                <span><?php echo e($post['reading_time']); ?> min read</span>
            </div>
            <h1><?php echo e($post['title']); ?></h1>
            <p class="lead"><?php echo e($post['excerpt'] ?: 'This article is published in the public knowledge base.'); ?></p>
            <div class="article-meta">
                <span>By <a href="<?php echo e(url('/author/' . $post['author_username'])); ?>"><?php echo e($post['author_name']); ?></a></span>
                <span><?php echo e($publishedAt); ?></span>
                <span><?php echo e($post['reading_time']); ?> min read</span>
            </div>
        </div>
        <?php if (!empty($post['featured_image'])) : ?>
            <img class="article-hero-image" src="<?php echo e(asset($post['featured_image'])); ?>" alt="<?php echo e($post['title']); ?>">
        <?php endif; ?>
    </section>

    <section class="article-layout">
        <div class="article-body card-surface prose-content">
            <div class="article-content">
                <?php echo $post['content']; ?>
            </div>
            <?php if ($metaFields !== array()) : ?>
                <section class="article-meta-fields">
                    <div class="section-head section-head--compact">
                        <div>
                            <p class="eyebrow">Custom fields</p>
                            <h2>Module data</h2>
                        </div>
                    </div>
                    <dl class="meta-list meta-list--grid">
                        <?php foreach ($metaFields as $key => $value) : ?>
                            <div>
                                <dt><?php echo e($key); ?></dt>
                                <dd><?php echo e($value); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </section>
            <?php endif; ?>
            <div class="article-taxonomy">
                <div>
                    <p class="eyebrow">Categories</p>
                    <div class="chip-grid">
                        <?php foreach ($categories as $category) : ?>
                            <a class="chip" href="<?php echo e(url('/category/' . $category['slug'])); ?>"><?php echo e($category['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <p class="eyebrow">Tags</p>
                    <div class="chip-grid">
                        <?php foreach ($tags as $tag) : ?>
                            <a class="chip chip--subtle" href="<?php echo e(url('/tag/' . $tag['slug'])); ?>"><?php echo e($tag['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <section class="share-panel">
                <p class="eyebrow">Share</p>
                <div class="share-links">
                    <a href="https://twitter.com/intent/tweet?url=<?php echo e(url('/content/' . $post['slug'])); ?>&text=<?php echo e(rawurlencode($post['title'])); ?>" rel="noopener noreferrer" target="_blank">X</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo e(url('/content/' . $post['slug'])); ?>" rel="noopener noreferrer" target="_blank">LinkedIn</a>
                    <a href="mailto:?subject=<?php echo e(rawurlencode($post['title'])); ?>&body=<?php echo e(rawurlencode(url('/content/' . $post['slug']))); ?>">Email</a>
                </div>
            </section>
        </div>

        <aside class="sidebar-stack">
            <section class="card-surface panel author-card">
                <p class="eyebrow">Author</p>
                <h2><?php echo e($post['author_name']); ?></h2>
                <p><?php echo e(!empty($author['bio']) ? $author['bio'] : 'Published contributor on Developer Ruhban.'); ?></p>
                <a href="<?php echo e(url('/author/' . $post['author_username'])); ?>">View author page</a>
            </section>

            <section class="card-surface panel">
                <p class="eyebrow">SEO</p>
                <h2>Metadata</h2>
                <dl class="meta-list">
                    <div><dt>Meta title</dt><dd><?php echo e(isset($seo['meta_title']) && $seo['meta_title'] ? $seo['meta_title'] : $post['title']); ?></dd></div>
                    <div><dt>Canonical</dt><dd><?php echo e(isset($seo['canonical_url']) && $seo['canonical_url'] ? $seo['canonical_url'] : url('/content/' . $post['slug'])); ?></dd></div>
                    <div><dt>Robots</dt><dd><?php echo e(isset($seo['robots']) && $seo['robots'] ? $seo['robots'] : 'index, follow'); ?></dd></div>
                    <div><dt>Schema</dt><dd><?php echo e(isset($seo['schema_type']) && $seo['schema_type'] ? $seo['schema_type'] : 'Article'); ?></dd></div>
                </dl>
            </section>
        </aside>
    </section>

    <section class="section-stack">
        <div class="section-head">
            <div>
                <p class="eyebrow">Related</p>
                <h2>Recommended reading</h2>
            </div>
        </div>
        <div class="post-grid">
            <?php if ($relatedPosts === array()) : ?>
                <article class="card-surface empty-state">
                    <h3>No related content yet</h3>
                    <p>Related content will appear once more posts are published in this topic area.</p>
                </article>
            <?php endif; ?>
            <?php foreach ($relatedPosts as $related) : ?>
                <article class="post-card card-surface">
                    <p class="post-card__meta"><?php echo e($related['content_type_name']); ?></p>
                    <h3><a href="<?php echo e(url('/content/' . $related['slug'])); ?>"><?php echo e($related['title']); ?></a></h3>
                    <p><?php echo e($related['excerpt'] ?: 'Continue exploring the topic in detail.'); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</article>

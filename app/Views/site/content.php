<?php
$categories = isset($categories) && is_array($categories) ? $categories : array();
$tags = isset($tags) && is_array($tags) ? $tags : array();
$relatedPosts = isset($relatedPosts) && is_array($relatedPosts) ? $relatedPosts : array();
$author = isset($author) && is_array($author) ? $author : array();
$seo = isset($seo) && is_array($seo) ? $seo : array();
$comments = isset($comments) && is_array($comments) ? $comments : array();
$interactionCounts = isset($interactionCounts) && is_array($interactionCounts) ? $interactionCounts : array('likes' => 0, 'bookmarks' => 0, 'favorites' => 0, 'comments' => 0);
$interactionState = isset($interactionState) && is_array($interactionState) ? $interactionState : array('like' => false, 'bookmark' => false, 'favorite' => false);
$readingHistory = isset($readingHistory) && is_array($readingHistory) ? $readingHistory : array();
$activityFeed = isset($activityFeed) && is_array($activityFeed) ? $activityFeed : array();
$notifications = isset($notifications) && is_array($notifications) ? $notifications : array();
$notificationCount = isset($notificationCount) ? (int) $notificationCount : 0;
$currentUser = isset($currentUser) && is_array($currentUser) ? $currentUser : app()->session()->get(config('auth.session_key', 'auth_user'));
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
                    <button type="button" class="share-copy-button" data-copy-link data-copy-url="<?php echo e(url('/content/' . $post['slug'])); ?>">Copy link</button>
                </div>
            </section>

            <section class="interaction-panel card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Engagement</p>
                        <h2>Reactions</h2>
                    </div>
                </div>
                <div class="reaction-grid">
                    <form method="post" action="<?php echo e(url('/content/' . $post['id'] . '/react/like')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="reaction-button<?php echo !empty($interactionState['like']) ? ' reaction-button--active' : ''; ?>">Like <span><?php echo e(isset($interactionCounts['likes']) ? $interactionCounts['likes'] : 0); ?></span></button>
                    </form>
                    <form method="post" action="<?php echo e(url('/content/' . $post['id'] . '/react/bookmark')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="reaction-button<?php echo !empty($interactionState['bookmark']) ? ' reaction-button--active' : ''; ?>">Bookmark <span><?php echo e(isset($interactionCounts['bookmarks']) ? $interactionCounts['bookmarks'] : 0); ?></span></button>
                    </form>
                    <form method="post" action="<?php echo e(url('/content/' . $post['id'] . '/react/favorite')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="reaction-button<?php echo !empty($interactionState['favorite']) ? ' reaction-button--active' : ''; ?>">Favorite <span><?php echo e(isset($interactionCounts['favorites']) ? $interactionCounts['favorites'] : 0); ?></span></button>
                    </form>
                </div>
                <div class="interaction-stats">
                    <span><?php echo e(isset($interactionCounts['comments']) ? $interactionCounts['comments'] : 0); ?> comments</span>
                    <span><?php echo e($notificationCount); ?> notifications</span>
                </div>
            </section>

            <section class="notifications-panel card-surface panel">
                <div class="section-head section-head--compact">
                    <div>
                        <p class="eyebrow">Notifications</p>
                        <h2>Recent updates</h2>
                    </div>
                </div>
                <div class="post-list-mini">
                    <?php if ($notifications === array()) : ?>
                        <div class="empty-inline">No notifications yet.</div>
                    <?php endif; ?>
                    <?php foreach ($notifications as $notification) : ?>
                        <a href="<?php echo e(!empty($notification['url']) ? $notification['url'] : url('/content/' . $post['slug'])); ?>">
                            <strong><?php echo e($notification['title']); ?></strong>
                            <span><?php echo e($notification['body']); ?></span>
                        </a>
                    <?php endforeach; ?>
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

            <section class="card-surface panel">
                <p class="eyebrow">Reading history</p>
                <h2>Recent reads</h2>
                <div class="post-list-mini">
                    <?php if ($readingHistory === array()) : ?>
                        <div class="empty-inline">No reading history yet.</div>
                    <?php endif; ?>
                    <?php foreach ($readingHistory as $history) : ?>
                        <a href="<?php echo e(url('/content/' . $history['slug'])); ?>">
                            <strong><?php echo e($history['title']); ?></strong>
                            <span><?php echo e(isset($history['content_type_name']) ? $history['content_type_name'] : 'Content'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="card-surface panel">
                <p class="eyebrow">Activity feed</p>
                <h2>Latest engagement</h2>
                <div class="post-list-mini">
                    <?php if ($activityFeed === array()) : ?>
                        <div class="empty-inline">No recent activity yet.</div>
                    <?php endif; ?>
                    <?php foreach ($activityFeed as $activity) : ?>
                        <a href="<?php echo e(!empty($activity['url']) ? $activity['url'] : url('/content/' . $post['slug'])); ?>">
                            <strong><?php echo e($activity['title']); ?></strong>
                            <span><?php echo e(isset($activity['event_type']) ? ucfirst($activity['event_type']) : 'Activity'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </aside>
    </section>

    <section class="section-stack comments-section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Comments</p>
                <h2>Discussion</h2>
            </div>
            <span class="search-summary"><?php echo e(isset($interactionCounts['comments']) ? $interactionCounts['comments'] : 0); ?> comments</span>
        </div>

        <section class="card-surface panel">
            <form class="comment-form" method="post" action="<?php echo e(url('/content/' . $post['id'] . '/comments')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="parent_id" value="0">
                <label>
                    <span>Comment</span>
                    <textarea name="body" rows="5" required maxlength="2000" placeholder="Share a helpful thought, question, or correction."></textarea>
                </label>
                <?php if (!is_array($currentUser)) : ?>
                    <div class="comment-form__grid">
                        <label>
                            <span>Name</span>
                            <input type="text" name="guest_name" maxlength="120" required>
                        </label>
                        <label>
                            <span>Email</span>
                            <input type="email" name="guest_email" maxlength="190" required>
                        </label>
                    </div>
                <?php endif; ?>
                <button class="btn btn-primary" type="submit">Post comment</button>
            </form>
        </section>

        <div class="comment-thread">
            <?php if ($comments === array()) : ?>
                <article class="card-surface empty-state">
                    <h3>No comments yet</h3>
                    <p>Start the discussion with the first comment.</p>
                </article>
            <?php endif; ?>
            <?php foreach ($comments as $comment) : ?>
                <?php echo view('site/partials/comment', array('comment' => $comment, 'post' => $post, 'currentUser' => $currentUser), array('layout' => false)); ?>
            <?php endforeach; ?>
        </div>
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

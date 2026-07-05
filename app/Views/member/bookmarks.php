<div class="panel card-surface">
    <h3 class="fw-bold mb-4">My Bookmarked Items</h3>
    <?php if (empty($bookmarks)) : ?>
        <p class="text-muted">You haven't bookmarked any articles yet. Bookmark them while browsing to see them here.</p>
    <?php else : ?>
        <div class="dashboard-grid">
            <?php foreach ($bookmarks as $item) : ?>
                <a href="<?php echo e(url('/content/' . ($item['slug'] ?? ''))); ?>" class="dashboard-card card-surface">
                    <?php if (!empty($item['content_type_name'])) : ?>
                        <span class="dashboard-card__type"><?php echo e($item['content_type_name']); ?></span>
                    <?php endif; ?>
                    <h5 class="dashboard-card__title"><?php echo e($item['title'] ?? 'Untitled'); ?></h5>
                    <?php if (!empty($item['excerpt'])) : ?>
                        <p class="dashboard-card__excerpt"><?php echo e(mb_substr($item['excerpt'], 0, 100)); ?>…</p>
                    <?php endif; ?>
                    <span class="dashboard-card__date">🔖 Saved <?php echo e(!empty($item['bookmarked_at']) ? date('M d, Y', strtotime($item['bookmarked_at'])) : ''); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

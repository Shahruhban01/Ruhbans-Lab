<div class="panel card-surface">
    <h3 class="fw-bold mb-4">Reading History</h3>
    <?php if (empty($history)) : ?>
        <p class="text-muted">No recently read posts found.</p>
    <?php else : ?>
        <div class="d-flex flex-column gap-1">
            <?php foreach ($history as $item) : ?>
                <a href="<?php echo e(url('/content/' . ($item['slug'] ?? ''))); ?>" class="dashboard-post-row dashboard-post-row--bordered">
                    <div class="dashboard-post-row__info">
                        <span class="dashboard-post-row__title"><?php echo e($item['title'] ?? 'Untitled'); ?></span>
                        <div class="d-flex gap-2 align-items-center mt-1">
                            <?php if (!empty($item['content_type_name'])) : ?>
                                <span class="badge bg-primary rounded-pill" style="font-size:.65rem"><?php echo e($item['content_type_name']); ?></span>
                            <?php endif; ?>
                            <span class="dashboard-post-row__meta"><?php echo e((int) ($item['view_count'] ?? 1)); ?>× read</span>
                        </div>
                    </div>
                    <span class="dashboard-post-row__time text-nowrap"><?php echo e(!empty($item['last_viewed_at']) ? date('M d, Y', strtotime($item['last_viewed_at'])) : ''); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="panel card-surface">
    <h3 class="fw-bold mb-4">My Activity timeline</h3>
    <?php if (empty($activity)) : ?>
        <p class="text-muted">No activity events found.</p>
    <?php else : ?>
        <div class="timeline">
            <?php foreach ($activity as $event) : ?>
                <div class="timeline-item">
                    <div class="timeline-item__dot"></div>
                    <div class="timeline-item__body">
                        <div class="timeline-item__header">
                            <strong class="timeline-item__action">
                                <?php
                                $actionType = $event['action_type'] ?? $event['event_type'] ?? $event['type'] ?? 'action';
                                $actionLabels = array(
                                    'like' => '❤️ Liked', 'bookmark' => '🔖 Bookmarked',
                                    'comment' => '💬 Commented', 'reply' => '↩️ Replied',
                                    'view' => '👁 Viewed', 'download' => '⬇️ Downloaded',
                                    'favorite' => '⭐ Favorited', 'signup' => '🎉 Joined',
                                );
                                echo e($actionLabels[$actionType] ?? ucfirst(str_replace('_', ' ', $actionType)));
                                ?>
                            </strong>
                            <span class="timeline-item__time"><?php echo e(!empty($event['created_at']) ? date('M d, Y H:i', strtotime($event['created_at'])) : ''); ?></span>
                        </div>
                        <?php if (!empty($event['post_title'])) : ?>
                            <p class="timeline-item__desc">
                                <a href="<?php echo e(url('/content/' . ($event['post_slug'] ?? ''))); ?>"><?php echo e($event['post_title']); ?></a>
                            </p>
                        <?php elseif (!empty($event['description'])) : ?>
                            <p class="timeline-item__desc"><?php echo e($event['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

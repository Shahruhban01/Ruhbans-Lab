<div class="panel card-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Notifications</h3>
        <?php if ($unreadCount > 0) : ?>
            <form method="post" action="<?php echo e(url('/account/notifications/read-all')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-secondary btn-sm">Mark All as Read</button>
            </form>
        <?php endif; ?>
    </div>
    <?php if (empty($notifications)) : ?>
        <p class="text-muted">No notifications yet.</p>
    <?php else : ?>
        <div class="d-flex flex-column gap-2">
            <?php foreach ($notifications as $notif) : ?>
                <div class="dashboard-notification <?php echo !(bool)($notif['is_read'] ?? false) ? 'dashboard-notification--unread' : ''; ?>">
                    <div class="dashboard-notification__dot"></div>
                    <div class="dashboard-notification__body">
                        <p class="dashboard-notification__msg"><?php echo e($notif['message'] ?? $notif['type'] ?? 'Notification'); ?></p>
                        <span class="dashboard-notification__time"><?php echo e(!empty($notif['created_at']) ? date('M d, Y H:i', strtotime($notif['created_at'])) : ''); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

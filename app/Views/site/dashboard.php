<?php
$currentUser       = isset($currentUser) && is_array($currentUser) ? $currentUser : array();
$membership        = isset($membership) && is_array($membership) ? $membership : null;
$plans             = isset($plans) && is_array($plans) ? $plans : array();
$history           = isset($history) && is_array($history) ? $history : array();
$bookmarks         = isset($bookmarks) && is_array($bookmarks) ? $bookmarks : array();
$liked             = isset($liked) && is_array($liked) ? $liked : array();
$comments          = isset($comments) && is_array($comments) ? $comments : array();
$notifications     = isset($notifications) && is_array($notifications) ? $notifications : array();
$activity          = isset($activity) && is_array($activity) ? $activity : array();
$achievements      = isset($achievements) && is_array($achievements) ? $achievements : array();
$readCount         = isset($readCount) ? (int) $readCount : 0;
$bookmarkCount     = isset($bookmarkCount) ? (int) $bookmarkCount : 0;
$likeCount         = isset($likeCount) ? (int) $likeCount : 0;
$commentCount      = isset($commentCount) ? (int) $commentCount : 0;
$unreadCount       = isset($unreadCount) ? (int) $unreadCount : 0;
$profileCompletion = isset($profileCompletion) ? (int) $profileCompletion : 0;
$activeTab         = isset($activeTab) ? (string) $activeTab : 'overview';

$planSlug = $membership ? ($membership['plan_slug'] ?? 'free') : 'free';
$planName = $membership ? ($membership['plan_name'] ?? 'Free') : 'Free';
$planStatus = $membership ? ($membership['status'] ?? 'active') : 'active';

$planBadgeClass = 'badge-plan--free';
if ($planSlug === 'pro')      $planBadgeClass = 'badge-plan--pro';
if ($planSlug === 'lifetime') $planBadgeClass = 'badge-plan--lifetime';

$userInitials = '';
$nameParts = explode(' ', trim($currentUser['name'] ?? ''));
foreach ($nameParts as $part) {
    $userInitials .= strtoupper(substr($part, 0, 1));
}
$userInitials = substr($userInitials, 0, 2);

$tabs = array(
    'overview'      => array('icon' => '◉', 'label' => 'Overview'),
    'history'       => array('icon' => '📖', 'label' => 'Reading History'),
    'bookmarks'     => array('icon' => '🔖', 'label' => 'Bookmarks'),
    'liked'         => array('icon' => '❤️', 'label' => 'Liked'),
    'comments'      => array('icon' => '💬', 'label' => 'Comments'),
    'notifications' => array('icon' => '🔔', 'label' => 'Notifications'),
    'activity'      => array('icon' => '⚡', 'label' => 'Activity'),
    'profile'       => array('icon' => '👤', 'label' => 'Profile'),
);
?>

<div class="dashboard-page">

    <!-- Dashboard Hero -->
    <div class="dashboard-hero">
        <div class="container">
            <div class="dashboard-hero__inner">
                <div class="dashboard-avatar" style="width:72px;height:72px;font-size:1.6rem;flex-shrink:0;">
                    <?php if (!empty($currentUser['avatar'])) : ?>
                        <img src="<?php echo e($currentUser['avatar']); ?>" alt="<?php echo e($currentUser['name'] ?? ''); ?>" class="dashboard-avatar__img">
                    <?php else : ?>
                        <div class="dashboard-avatar__initials"><?php echo e($userInitials ?: '?'); ?></div>
                    <?php endif; ?>
                    <span class="dashboard-avatar__status <?php echo $planSlug !== 'free' ? 'is-premium' : ''; ?>"></span>
                </div>
                <div class="dashboard-hero__info">
                    <h1 class="dashboard-hero__name"><?php echo e($currentUser['name'] ?? 'Member'); ?></h1>
                    <p class="dashboard-hero__meta">
                        <span class="text-muted">@<?php echo e($currentUser['username'] ?? ''); ?></span>
                        <span class="dashboard-hero__sep">·</span>
                        <span class="text-muted">Joined <?php echo e(!empty($currentUser['created_at']) ? date('M Y', strtotime($currentUser['created_at'])) : 'Recently'); ?></span>
                    </p>
                    <?php if (!empty($currentUser['bio'])) : ?>
                        <p class="dashboard-hero__bio"><?php echo e($currentUser['bio']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="dashboard-hero__actions">
                    <a href="<?php echo e(url('/membership')); ?>" class="btn btn-primary btn-sm">
                        <?php echo $planSlug === 'free' ? '⚡ Upgrade Plan' : '⭐ View Plans'; ?>
                    </a>
                    <a href="?tab=profile" class="btn btn-secondary btn-sm" data-tab="profile">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container dashboard-body">

        <!-- Top Cards: Membership + Stats + Quick Actions -->
        <div class="dashboard-top-cards">

            <!-- Membership Card -->
            <div class="membership-card membership-card--<?php echo e($planSlug); ?>">
                <div class="membership-card__glow"></div>
                <div class="membership-card__header">
                    <div>
                        <p class="membership-card__eyebrow">Current Plan</p>
                        <h2 class="membership-card__plan"><?php echo e($planName); ?></h2>
                    </div>
                    <span class="badge-plan <?php echo e($planBadgeClass); ?>"><?php echo e(strtoupper($planSlug)); ?></span>
                </div>
                <div class="membership-card__body">
                    <div class="membership-card__stat">
                        <span>Status</span>
                        <strong class="<?php echo $planStatus === 'active' ? 'text-success' : 'text-danger'; ?>">
                            <?php echo e(ucfirst($planStatus)); ?>
                        </strong>
                    </div>
                    <?php if ($membership && !empty($membership['starts_at'])) : ?>
                    <div class="membership-card__stat">
                        <span>Member Since</span>
                        <strong><?php echo e(date('M d, Y', strtotime($membership['starts_at']))); ?></strong>
                    </div>
                    <?php endif; ?>
                    <div class="membership-card__stat">
                        <span>Valid Until</span>
                        <strong>
                            <?php if ($membership && !empty($membership['ends_at'])) : ?>
                                <?php echo e(date('M d, Y', strtotime($membership['ends_at']))); ?>
                            <?php elseif ($planSlug === 'lifetime') : ?>
                                Lifetime ∞
                            <?php else : ?>
                                Always Free
                            <?php endif; ?>
                        </strong>
                    </div>
                </div>
                <?php if ($planSlug === 'free') : ?>
                <div class="membership-card__upgrade">
                    <a href="<?php echo e(url('/membership')); ?>" class="btn btn-primary btn-sm w-100">
                        ⚡ Upgrade to Pro — Unlock Premium Features
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Stats Bar -->
            <div class="dashboard-stats">
                <div class="dashboard-stat">
                    <span class="dashboard-stat__icon">📖</span>
                    <div>
                        <strong class="dashboard-stat__val"><?php echo e(number_format($readCount)); ?></strong>
                        <span class="dashboard-stat__label">Total Reads</span>
                    </div>
                </div>
                <div class="dashboard-stat">
                    <span class="dashboard-stat__icon">🔖</span>
                    <div>
                        <strong class="dashboard-stat__val"><?php echo e(number_format($bookmarkCount)); ?></strong>
                        <span class="dashboard-stat__label">Bookmarks</span>
                    </div>
                </div>
                <div class="dashboard-stat">
                    <span class="dashboard-stat__icon">❤️</span>
                    <div>
                        <strong class="dashboard-stat__val"><?php echo e(number_format($likeCount)); ?></strong>
                        <span class="dashboard-stat__label">Liked</span>
                    </div>
                </div>
                <div class="dashboard-stat">
                    <span class="dashboard-stat__icon">💬</span>
                    <div>
                        <strong class="dashboard-stat__val"><?php echo e(number_format($commentCount)); ?></strong>
                        <span class="dashboard-stat__label">Comments</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-quick-actions">
                <p class="eyebrow mb-3">Quick Actions</p>
                <div class="d-flex flex-column gap-2">
                    <a href="<?php echo e(url('/archive')); ?>" class="dashboard-action-btn"><span>📚</span> Browse Content</a>
                    <a href="<?php echo e(url('/lab')); ?>" class="dashboard-action-btn"><span>🧪</span> Explore Lab</a>
                    <a href="<?php echo e(url('/membership')); ?>" class="dashboard-action-btn"><span>⭐</span> View Plans</a>
                    <a href="?tab=profile" class="dashboard-action-btn" data-tab="profile"><span>✏️</span> Edit Profile</a>
                </div>
            </div>

        </div><!-- /.dashboard-top-cards -->

        <!-- Main Layout: Sidebar + Content -->
        <div class="dashboard-layout">

            <!-- Sidebar Nav -->
            <nav class="dashboard-nav">
                <p class="eyebrow mb-3">Dashboard</p>
                <?php foreach ($tabs as $key => $tab) : ?>
                    <a href="?tab=<?php echo e($key); ?>"
                       class="dashboard-nav__link <?php echo $activeTab === $key ? 'dashboard-nav__link--active' : ''; ?>"
                       data-tab="<?php echo e($key); ?>">
                        <span class="dashboard-nav__icon"><?php echo $tab['icon']; ?></span>
                        <span><?php echo e($tab['label']); ?></span>
                        <?php if ($key === 'notifications' && $unreadCount > 0) : ?>
                            <span class="dashboard-nav__badge"><?php echo e($unreadCount); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Tab Content Panel -->
            <div class="dashboard-content">

                <!-- OVERVIEW -->
                <div class="dashboard-tab <?php echo $activeTab === 'overview' ? 'dashboard-tab--active' : ''; ?>" id="tab-overview">
                    <div class="dashboard-tab__header">
                        <h2>Overview</h2>
                        <p class="text-muted">Your activity summary and achievements.</p>
                    </div>

                    <div class="panel card-surface mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0 fw-semibold">Profile Completion</h4>
                            <strong class="text-primary"><?php echo e($profileCompletion); ?>%</strong>
                        </div>
                        <div class="profile-completion-track">
                            <div class="profile-completion-bar" style="width:<?php echo e($profileCompletion); ?>%;"></div>
                        </div>
                        <?php if ($profileCompletion < 100) : ?>
                            <p class="text-muted small mt-2 mb-0">
                                Add your bio, avatar, and links to reach 100%.
                                <a href="?tab=profile" class="text-primary" data-tab="profile">Edit Profile →</a>
                            </p>
                        <?php else : ?>
                            <p class="text-success small mt-2 mb-0">✅ Profile is complete!</p>
                        <?php endif; ?>
                    </div>

                    <div class="panel card-surface mb-4">
                        <h4 class="mb-4 fw-semibold">Achievements</h4>
                        <?php if (empty($achievements)) : ?>
                            <p class="text-muted mb-0">Start reading, commenting, and engaging to unlock achievements!</p>
                        <?php else : ?>
                            <div class="achievements-grid">
                                <?php foreach ($achievements as $ach) : ?>
                                    <div class="achievement-badge">
                                        <span class="achievement-badge__icon"><?php echo $ach['icon']; ?></span>
                                        <span class="achievement-badge__label"><?php echo e($ach['label']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="panel card-surface">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0 fw-semibold">Recently Read</h4>
                            <a href="?tab=history" class="small text-primary" data-tab="history">View all →</a>
                        </div>
                        <?php if (empty($history)) : ?>
                            <p class="text-muted mb-0">Nothing read yet. <a href="<?php echo e(url('/archive')); ?>">Browse content</a> to get started.</p>
                        <?php else : ?>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach (array_slice($history, 0, 5) as $item) : ?>
                                    <a href="<?php echo e(url('/content/' . ($item['slug'] ?? ''))); ?>" class="dashboard-post-row">
                                        <div class="dashboard-post-row__info">
                                            <span class="dashboard-post-row__title"><?php echo e($item['title'] ?? 'Untitled'); ?></span>
                                            <span class="dashboard-post-row__meta"><?php echo e($item['content_type_name'] ?? ''); ?></span>
                                        </div>
                                        <span class="dashboard-post-row__time"><?php echo e(!empty($item['last_viewed_at']) ? date('M d', strtotime($item['last_viewed_at'])) : ''); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- READING HISTORY -->
                <div class="dashboard-tab <?php echo $activeTab === 'history' ? 'dashboard-tab--active' : ''; ?>" id="tab-history">
                    <div class="dashboard-tab__header">
                        <h2>Reading History</h2>
                        <p class="text-muted"><?php echo e(count($history)); ?> articles read</p>
                    </div>
                    <?php if (empty($history)) : ?>
                        <div class="dashboard-empty">
                            <span class="dashboard-empty__icon">📖</span>
                            <p>You haven't read any content yet.</p>
                            <a href="<?php echo e(url('/archive')); ?>" class="btn btn-primary btn-sm">Browse Content</a>
                        </div>
                    <?php else : ?>
                        <div class="panel card-surface">
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
                        </div>
                    <?php endif; ?>
                </div>

                <!-- BOOKMARKS -->
                <div class="dashboard-tab <?php echo $activeTab === 'bookmarks' ? 'dashboard-tab--active' : ''; ?>" id="tab-bookmarks">
                    <div class="dashboard-tab__header">
                        <h2>Bookmarks</h2>
                        <p class="text-muted"><?php echo e(count($bookmarks)); ?> saved items</p>
                    </div>
                    <?php if (empty($bookmarks)) : ?>
                        <div class="dashboard-empty">
                            <span class="dashboard-empty__icon">🔖</span>
                            <p>No bookmarks yet. Tap the bookmark icon on any article.</p>
                            <a href="<?php echo e(url('/archive')); ?>" class="btn btn-primary btn-sm">Browse Content</a>
                        </div>
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
                                    <span class="dashboard-card__date">🔖 <?php echo e(!empty($item['bookmarked_at']) ? date('M d', strtotime($item['bookmarked_at'])) : ''); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- LIKED -->
                <div class="dashboard-tab <?php echo $activeTab === 'liked' ? 'dashboard-tab--active' : ''; ?>" id="tab-liked">
                    <div class="dashboard-tab__header">
                        <h2>Liked Content</h2>
                        <p class="text-muted"><?php echo e(count($liked)); ?> liked items</p>
                    </div>
                    <?php if (empty($liked)) : ?>
                        <div class="dashboard-empty">
                            <span class="dashboard-empty__icon">❤️</span>
                            <p>Nothing liked yet. Show some love to great content!</p>
                            <a href="<?php echo e(url('/archive')); ?>" class="btn btn-primary btn-sm">Browse Content</a>
                        </div>
                    <?php else : ?>
                        <div class="dashboard-grid">
                            <?php foreach ($liked as $item) : ?>
                                <a href="<?php echo e(url('/content/' . ($item['slug'] ?? ''))); ?>" class="dashboard-card card-surface">
                                    <?php if (!empty($item['content_type_name'])) : ?>
                                        <span class="dashboard-card__type"><?php echo e($item['content_type_name']); ?></span>
                                    <?php endif; ?>
                                    <h5 class="dashboard-card__title"><?php echo e($item['title'] ?? 'Untitled'); ?></h5>
                                    <?php if (!empty($item['excerpt'])) : ?>
                                        <p class="dashboard-card__excerpt"><?php echo e(mb_substr($item['excerpt'], 0, 100)); ?>…</p>
                                    <?php endif; ?>
                                    <span class="dashboard-card__date">❤️ <?php echo e(!empty($item['liked_at']) ? date('M d', strtotime($item['liked_at'])) : ''); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- COMMENTS -->
                <div class="dashboard-tab <?php echo $activeTab === 'comments' ? 'dashboard-tab--active' : ''; ?>" id="tab-comments">
                    <div class="dashboard-tab__header">
                        <h2>My Comments</h2>
                        <p class="text-muted"><?php echo e(count($comments)); ?> comments posted</p>
                    </div>
                    <?php if (empty($comments)) : ?>
                        <div class="dashboard-empty">
                            <span class="dashboard-empty__icon">💬</span>
                            <p>You haven't posted any comments yet. Join a discussion!</p>
                        </div>
                    <?php else : ?>
                        <div class="panel card-surface">
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($comments as $comment) : ?>
                                    <div class="dashboard-comment">
                                        <div class="dashboard-comment__header">
                                            <span class="status-pill <?php echo ($comment['status'] ?? '') === 'published' ? 'status-pill--active' : 'status-pill--inactive'; ?>">
                                                <?php echo e(ucfirst($comment['status'] ?? 'pending')); ?>
                                            </span>
                                            <span class="dashboard-comment__time"><?php echo e(!empty($comment['created_at']) ? date('M d, Y', strtotime($comment['created_at'])) : ''); ?></span>
                                        </div>
                                        <p class="dashboard-comment__body"><?php echo e(mb_substr($comment['body'] ?? '', 0, 200)); ?><?php echo mb_strlen($comment['body'] ?? '') > 200 ? '…' : ''; ?></p>
                                        <?php if (!empty($comment['post_title'])) : ?>
                                            <a href="<?php echo e(url('/content/' . ($comment['post_slug'] ?? ''))); ?>" class="dashboard-comment__post">
                                                On: <?php echo e($comment['post_title']); ?> →
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- NOTIFICATIONS -->
                <div class="dashboard-tab <?php echo $activeTab === 'notifications' ? 'dashboard-tab--active' : ''; ?>" id="tab-notifications">
                    <div class="dashboard-tab__header">
                        <div>
                            <h2>Notifications <?php if ($unreadCount > 0) : ?><span class="dashboard-nav__badge" style="position:static;font-size:.75rem;"><?php echo e($unreadCount); ?></span><?php endif; ?></h2>
                            <p class="text-muted"><?php echo e(count($notifications)); ?> total notifications</p>
                        </div>
                        <?php if ($unreadCount > 0) : ?>
                            <form method="post" action="<?php echo e(url('/dashboard/notifications/read-all')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-secondary btn-sm">✓ Mark All Read</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($notifications)) : ?>
                        <div class="dashboard-empty">
                            <span class="dashboard-empty__icon">🔔</span>
                            <p>No notifications yet. Keep engaging with content!</p>
                        </div>
                    <?php else : ?>
                        <div class="panel card-surface">
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($notifications as $notif) : ?>
                                    <div class="dashboard-notification <?php echo !(bool)($notif['is_read'] ?? false) ? 'dashboard-notification--unread' : ''; ?>">
                                        <div class="dashboard-notification__dot"></div>
                                        <div class="dashboard-notification__body">
                                            <p class="dashboard-notification__msg"><?php echo e($notif['message'] ?? $notif['type'] ?? 'Notification'); ?></p>
                                            <span class="dashboard-notification__time"><?php echo e(!empty($notif['created_at']) ? date('M d, Y · H:i', strtotime($notif['created_at'])) : ''); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ACTIVITY -->
                <div class="dashboard-tab <?php echo $activeTab === 'activity' ? 'dashboard-tab--active' : ''; ?>" id="tab-activity">
                    <div class="dashboard-tab__header">
                        <h2>Activity Timeline</h2>
                        <p class="text-muted">Your recent actions on the platform</p>
                    </div>
                    <?php if (empty($activity)) : ?>
                        <div class="dashboard-empty">
                            <span class="dashboard-empty__icon">⚡</span>
                            <p>No activity recorded yet. Start reading and engaging!</p>
                        </div>
                    <?php else : ?>
                        <div class="panel card-surface">
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
                                                        'like'     => '❤️ Liked',     'bookmark' => '🔖 Bookmarked',
                                                        'comment'  => '💬 Commented', 'reply'    => '↩️ Replied',
                                                        'view'     => '👁 Viewed',    'download' => '⬇️ Downloaded',
                                                        'favorite' => '⭐ Favorited', 'signup'   => '🎉 Joined',
                                                    );
                                                    echo e($actionLabels[$actionType] ?? ucfirst(str_replace('_', ' ', $actionType)));
                                                    ?>
                                                </strong>
                                                <span class="timeline-item__time"><?php echo e(!empty($event['created_at']) ? date('M d, Y · H:i', strtotime($event['created_at'])) : ''); ?></span>
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
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PROFILE -->
                <div class="dashboard-tab <?php echo $activeTab === 'profile' ? 'dashboard-tab--active' : ''; ?>" id="tab-profile">
                    <div class="dashboard-tab__header">
                        <h2>Edit Profile</h2>
                        <p class="text-muted">Update your public profile information</p>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="panel card-surface">
                                <h4 class="fw-semibold mb-4">Profile Information</h4>
                                <form method="post" action="<?php echo e(url('/dashboard/profile')); ?>" class="d-flex flex-column gap-3">
                                    <?php echo csrf_field(); ?>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="<?php echo e($currentUser['name'] ?? ''); ?>" required maxlength="120">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" value="@<?php echo e($currentUser['username'] ?? ''); ?>" disabled>
                                            <small class="text-muted">Username cannot be changed here.</small>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?php echo e($currentUser['email'] ?? ''); ?>" disabled>
                                        <small class="text-muted">Contact support to change your email.</small>
                                    </div>

                                    <div>
                                        <label class="form-label">Bio</label>
                                        <textarea name="bio" class="form-control" rows="3" maxlength="500" placeholder="Tell the world a little about yourself..."><?php echo e($currentUser['bio'] ?? ''); ?></textarea>
                                    </div>

                                    <div>
                                        <label class="form-label">Avatar URL</label>
                                        <input type="url" name="avatar" class="form-control" value="<?php echo e($currentUser['avatar'] ?? ''); ?>" placeholder="https://example.com/avatar.jpg">
                                    </div>

                                    <div>
                                        <label class="form-label">Website</label>
                                        <input type="url" name="website" class="form-control" value="<?php echo e($currentUser['website'] ?? ''); ?>" placeholder="https://yoursite.com">
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">GitHub</label>
                                            <input type="text" name="github" class="form-control" value="<?php echo e($currentUser['github'] ?? ''); ?>" placeholder="username">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Twitter / X</label>
                                            <input type="text" name="twitter" class="form-control" value="<?php echo e($currentUser['twitter'] ?? ''); ?>" placeholder="@handle">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">LinkedIn</label>
                                            <input type="text" name="linkedin" class="form-control" value="<?php echo e($currentUser['linkedin'] ?? ''); ?>" placeholder="profile-slug">
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 mt-2">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <a href="?tab=profile" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="panel card-surface mb-4 text-center">
                                <div class="dashboard-avatar mx-auto mb-3" style="width:80px;height:80px;font-size:1.8rem;">
                                    <?php if (!empty($currentUser['avatar'])) : ?>
                                        <img src="<?php echo e($currentUser['avatar']); ?>" alt="" class="dashboard-avatar__img">
                                    <?php else : ?>
                                        <div class="dashboard-avatar__initials"><?php echo e($userInitials ?: '?'); ?></div>
                                    <?php endif; ?>
                                </div>
                                <h5 class="fw-bold mb-1"><?php echo e($currentUser['name'] ?? ''); ?></h5>
                                <p class="text-muted small mb-2">@<?php echo e($currentUser['username'] ?? ''); ?></p>
                                <span class="badge-plan <?php echo e($planBadgeClass); ?>"><?php echo e(strtoupper($planSlug)); ?></span>
                            </div>

                            <div class="panel card-surface">
                                <h5 class="fw-semibold mb-3">Membership</h5>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Plan</span>
                                        <strong class="small"><?php echo e($planName); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Status</span>
                                        <strong class="small <?php echo $planStatus === 'active' ? 'text-success' : 'text-danger'; ?>"><?php echo e(ucfirst($planStatus)); ?></strong>
                                    </div>
                                    <?php if ($planSlug === 'free') : ?>
                                        <a href="<?php echo e(url('/membership')); ?>" class="btn btn-primary btn-sm mt-2">Upgrade Now</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /.dashboard-content -->
        </div><!-- /.dashboard-layout -->
    </div><!-- /.container.dashboard-body -->
</div><!-- /.dashboard-page -->

<script>
(function () {
    'use strict';
    var tabLinks  = document.querySelectorAll('[data-tab]');
    var tabPanels = document.querySelectorAll('.dashboard-tab');

    function activateTab(key) {
        if (!key) return;
        tabLinks.forEach(function (link) {
            link.classList.toggle('dashboard-nav__link--active', link.getAttribute('data-tab') === key);
        });
        tabPanels.forEach(function (panel) {
            panel.classList.toggle('dashboard-tab--active', panel.id === 'tab-' + key);
        });
    }

    tabLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            var key = this.getAttribute('data-tab');
            if (!key) return;
            e.preventDefault();
            history.pushState(null, '', '?tab=' + key);
            activateTab(key);
        });
    });
}());
</script>

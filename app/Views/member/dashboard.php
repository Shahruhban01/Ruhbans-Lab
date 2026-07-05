<?php
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
?>

<div class="row g-4">
    <!-- Top Cards: Membership + Stats -->
    <div class="col-lg-4">
        <div class="membership-card membership-card--<?php echo e($planSlug); ?> h-100">
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
                <a href="<?php echo e(url('/account/pricing')); ?>" class="btn btn-primary btn-sm w-100">
                    ⚡ Upgrade to Pro — Unlock Premium
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="dashboard-stats h-100">
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
                    <span class="dashboard-stat__label">Liked Content</span>
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
    </div>

    <!-- Main Workspace Section -->
    <div class="col-lg-8">
        <div class="panel card-surface mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 fw-semibold">Recently Read</h4>
                <a href="<?php echo e(url('/account/history')); ?>" class="small text-primary">View all History →</a>
            </div>
            <?php if (empty($history)) : ?>
                <p class="text-muted mb-0">Nothing read yet. <a href="<?php echo e(url('/')); ?>">Browse content</a> to get started.</p>
            <?php else : ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($history as $item) : ?>
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

    <!-- Profile Completion -->
    <div class="col-lg-4">
        <div class="panel card-surface h-100">
            <h4 class="mb-3 fw-semibold">Profile Completion</h4>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Progress</span>
                <strong class="text-primary"><?php echo e($profileCompletion); ?>%</strong>
            </div>
            <div class="profile-completion-track mb-3">
                <div class="profile-completion-bar" style="width:<?php echo e($profileCompletion); ?>%;"></div>
            </div>
            <p class="text-muted small mb-0">
                Complete your bio, profile links and details to access full community options.
                <a href="<?php echo e(url('/account/profile')); ?>" class="text-primary mt-2 d-block">Update Profile →</a>
            </p>
        </div>
    </div>
</div>

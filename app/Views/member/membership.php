<?php
$planSlug = $membership ? ($membership['plan_slug'] ?? 'free') : 'free';
$planName = $membership ? ($membership['plan_name'] ?? 'Free') : 'Free';
$planStatus = $membership ? ($membership['status'] ?? 'active') : 'active';
?>
<div class="panel card-surface">
    <h3 class="fw-bold mb-4">Membership Plan Details</h3>
    <div class="row align-items-center g-4">
        <div class="col-md-6">
            <div class="membership-card membership-card--<?php echo e($planSlug); ?>">
                <div class="membership-card__glow"></div>
                <div class="membership-card__header">
                    <div>
                        <p class="membership-card__eyebrow">Active Plan</p>
                        <h2 class="membership-card__plan"><?php echo e($planName); ?></h2>
                    </div>
                </div>
                <div class="membership-card__body mt-3">
                    <div class="membership-card__stat">
                        <span>Plan Status</span>
                        <strong class="text-success"><?php echo e(ucfirst($planStatus)); ?></strong>
                    </div>
                    <div class="membership-card__stat">
                        <span>Expires / Renews</span>
                        <strong>
                            <?php if ($membership && !empty($membership['ends_at'])) : ?>
                                <?php echo e(date('M d, Y', strtotime($membership['ends_at']))); ?>
                            <?php elseif ($planSlug === 'lifetime') : ?>
                                Lifetime Validity
                            <?php else : ?>
                                Standard Free Access
                            <?php endif; ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h5 class="fw-bold mb-2">Upgrade Options</h5>
            <p class="text-muted">Pro and Lifetime subscriptions unlock custom source files, download links, version histories, and prioritized support.</p>
            <a href="<?php echo e(url('/account/pricing')); ?>" class="btn btn-primary mt-2">View Subscriptions & pricing</a>
        </div>
    </div>
</div>

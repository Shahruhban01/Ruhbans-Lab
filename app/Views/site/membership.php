<?php
$plans = isset($plans) ? $plans : array();
$activePlanSlug = isset($activeMembership['plan_slug']) ? $activeMembership['plan_slug'] : null;
$currentUser = isset($currentUser) && is_array($currentUser) ? $currentUser : null;
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <p class="eyebrow text-primary">Membership Plans</p>
        <h1 class="display-5 fw-bold mb-3">Unlock Premium Developer Content</h1>
        <p class="lead text-muted max-width-600 mx-auto">Get access to professional-grade tutorials, source code, downloads, and direct developer support.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <?php foreach ($plans as $plan) : 
            $planSlug = $plan['slug'];
            $priceStr = $plan['price_cents'] > 0 ? '$' . number_format($plan['price_cents']/100, 2) : 'Free';
            $periodStr = $planSlug === 'lifetime' ? 'once' : ($planSlug === 'pro' ? '/ month' : '');
            $isCurrent = $activePlanSlug === $planSlug || (!$activePlanSlug && $planSlug === 'free' && $currentUser);
            $featuresList = json_decode((string)($plan['features'] ?? '[]'), true);
            if (!is_array($featuresList)) {
                $featuresList = array();
            }
        ?>
            <div class="col-md-4">
                <div class="card-surface p-4 h-100 d-flex flex-column border <?php echo $planSlug === 'pro' ? 'border-primary shadow-sm' : ''; ?>">
                    <?php if ($planSlug === 'pro') : ?>
                        <span class="badge bg-primary align-self-start mb-3 rounded-pill px-3 py-2">Most Popular</span>
                    <?php endif; ?>
                    <h3 class="h4 fw-bold mb-2"><?php echo e($plan['name']); ?></h3>
                    <p class="text-muted small mb-4"><?php echo e($plan['description']); ?></p>
                    
                    <div class="mb-4">
                        <span class="h1 fw-bold"><?php echo e($priceStr); ?></span>
                        <span class="text-muted small"><?php echo e($periodStr); ?></span>
                    </div>

                    <div class="flex-grow-1 mb-4">
                        <p class="text-muted small fw-semibold text-uppercase mb-3">What's included:</p>
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <?php foreach ($featuresList as $feature) : 
                                $featureLabel = '';
                                switch ($feature) {
                                    case 'read_general':
                                        $featureLabel = 'Access to public articles';
                                        break;
                                    case 'comment':
                                        $featureLabel = 'Join discussions and comments';
                                        break;
                                    case 'read_premium':
                                        $featureLabel = 'Read premium articles & tutorials';
                                        break;
                                    case 'download_assets':
                                        $featureLabel = 'Download project files & code assets';
                                        break;
                                    case 'access_beta':
                                        $featureLabel = 'Access to beta apps & tools';
                                        break;
                                    case 'priority_support':
                                        $featureLabel = 'Priority email/chat support';
                                        break;
                                    default:
                                        $featureLabel = ucfirst(str_replace('_', ' ', $feature));
                                }
                            ?>
                                <li class="small d-flex align-items-center gap-2">
                                    <svg class="text-success" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022z"/></svg>
                                    <?php echo e($featureLabel); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div>
                        <?php if ($isCurrent) : ?>
                            <button class="btn btn-outline-secondary w-100 disabled" disabled>Your Current Plan</button>
                        <?php elseif (!$currentUser) : ?>
                            <a href="<?php echo e(url('/signup')); ?>" class="btn <?php echo $planSlug === 'pro' ? 'btn-primary' : 'btn-secondary'; ?> w-100">Sign Up to Get Started</a>
                        <?php else : ?>
                            <form method="post" action="<?php echo e(url('/admin/memberships/assign')); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="user_id" value="<?php echo e($currentUser['id']); ?>">
                                <input type="hidden" name="plan_id" value="<?php echo e($plan['id']); ?>">
                                <button type="submit" class="btn <?php echo $planSlug === 'pro' ? 'btn-primary' : 'btn-secondary'; ?> w-100">Activate Plan</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

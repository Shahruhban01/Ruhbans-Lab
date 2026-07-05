<div class="panel card-surface">
    <h3 class="fw-bold mb-4 text-center">Upgrade Your Account</h3>
    <p class="text-muted text-center mb-5">Choose the plan that fits your growth and development needs.</p>
    <div class="row g-4">
        <?php foreach ($plans as $p) : 
            $planSlug = $p['slug'];
            $isCurrent = $activeMembership && $activeMembership['plan_id'] === $p['id'];
        ?>
            <div class="col-md-4">
                <div class="card h-100 p-4 border <?php echo $isCurrent ? 'border-primary border-2 shadow-sm' : ''; ?>">
                    <h5 class="fw-bold"><?php echo e($p['name']); ?></h5>
                    <p class="text-muted small"><?php echo e($p['description'] ?? 'Plan details'); ?></p>
                    <div class="my-4">
                        <span class="h2 fw-bold">$<?php echo e(number_format($p['price_cents']/100, 2)); ?></span>
                        <span class="text-muted">/ <?php echo e($p['billing_period']); ?></span>
                    </div>
                    <?php if ($isCurrent) : ?>
                        <button class="btn btn-secondary w-100" disabled>Active Subscription</button>
                    <?php else : ?>
                        <a href="https://example.com/checkout/<?php echo e($p['id']); ?>" target="_blank" class="btn btn-primary w-100">Upgrade to <?php echo e($p['name']); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

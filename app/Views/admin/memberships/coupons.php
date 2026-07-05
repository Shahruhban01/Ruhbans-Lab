<div class="panel card-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="eyebrow">Commerce Setup</p>
            <h2 class="fw-bold m-0">Discount Coupons</h2>
        </div>
    </div>

    <!-- Create Coupon Form -->
    <div class="card p-4 border mb-5 bg-light">
        <h4 class="fw-bold mb-3">Create New Coupon</h4>
        <form method="post" action="<?php echo e(url('/admin/memberships/coupons/create')); ?>">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Coupon Code</label>
                    <input type="text" name="code" class="form-control" placeholder="E.g., SUMMER50" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Discount Percentage (%)</label>
                    <input type="number" name="discount_percentage" class="form-control" min="1" max="100" placeholder="E.g., 50" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Expiry Date (Optional)</label>
                    <input type="datetime-local" name="expires_at" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Create Coupon</button>
        </form>
    </div>

    <!-- Active Coupons List -->
    <h4 class="fw-bold mb-3">Active Coupons</h4>
    <?php if (empty($coupons)) : ?>
        <p class="text-muted">No coupons configured yet.</p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $c) : ?>
                        <tr>
                            <td><code class="fw-bold text-dark" style="font-size: 1.1rem;"><?php echo e($c['code']); ?></code></td>
                            <td><?php echo e($c['discount_percentage']); ?>% OFF</td>
                            <td class="text-muted small"><?php echo e($c['expires_at'] ? date('M d, Y H:i', strtotime($c['expires_at'])) : 'Never Expires'); ?></td>
                            <td>
                                <?php if ($c['is_active']) : ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else : ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <form method="post" action="<?php echo e(url('/admin/memberships/coupons/' . $c['id'] . '/delete')); ?>" onsubmit="return confirm('Remove this coupon code?')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

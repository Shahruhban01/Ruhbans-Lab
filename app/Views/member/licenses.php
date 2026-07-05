<div class="panel card-surface">
    <h3 class="fw-bold mb-4">My Product Licenses</h3>
    <p class="text-muted small mb-4">Use the active license keys listed below to register your codebase copies and verify API connections.</p>
    
    <?php if (empty($licenses)) : ?>
        <p class="text-muted">You do not have any active licenses yet. Purchase a package or upgrade your plan to generate license keys.</p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>License Key</th>
                        <th>Status</th>
                        <th>Expires At</th>
                        <th>Generated On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($licenses as $l) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(url('/lab/' . ($l['product_slug'] ?? ''))); ?>" class="fw-semibold">
                                    <?php echo e($l['product_name']); ?>
                                </a>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 border rounded"><?php echo e($l['license_key']); ?></code>
                            </td>
                            <td>
                                <span class="badge bg-success rounded-pill"><?php echo e(ucfirst($l['status'])); ?></span>
                            </td>
                            <td class="text-muted small">
                                <?php echo e($l['expires_at'] ? date('M d, Y', strtotime($l['expires_at'])) : 'Lifetime / Never'); ?>
                            </td>
                            <td class="text-muted small"><?php echo e(date('M d, Y', strtotime($l['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

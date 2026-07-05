<div class="panel card-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="eyebrow">Commerce Verification</p>
            <h2 class="fw-bold m-0">Issued Product Licenses</h2>
        </div>
    </div>

    <!-- Licenses Inspector Table -->
    <?php if (empty($licenses)) : ?>
        <p class="text-muted">No licenses issued yet.</p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>License Key</th>
                        <th>User Name</th>
                        <th>Target Product</th>
                        <th>Status</th>
                        <th>Expires At</th>
                        <th>Generated On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($licenses as $l) : ?>
                        <tr>
                            <td><code class="bg-light px-2 py-1 border rounded fw-semibold"><?php echo e($l['license_key']); ?></code></td>
                            <td>
                                <strong><?php echo e($l['user_name']); ?></strong>
                                <span class="text-muted small d-block"><?php echo e($l['user_email']); ?></span>
                            </td>
                            <td><?php echo e($l['product_title']); ?></td>
                            <td><span class="badge bg-success rounded-pill"><?php echo e(ucfirst($l['status'])); ?></span></td>
                            <td class="text-muted small"><?php echo e($l['expires_at'] ? date('M d, Y', strtotime($l['expires_at'])) : 'Lifetime'); ?></td>
                            <td class="text-muted small"><?php echo e(date('M d, Y H:i', strtotime($l['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

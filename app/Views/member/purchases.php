<div class="panel card-surface">
    <h3 class="fw-bold mb-4">My Purchase History</h3>
    
    <?php if (empty($purchases)) : ?>
        <p class="text-muted mb-4">You have not made any purchases yet.</p>
    <?php else : ?>
        <div class="table-responsive mb-5">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Amount Paid</th>
                        <th>Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $p) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(url('/lab/' . ($p['slug'] ?? ''))); ?>" class="fw-bold text-dark">
                                    <?php echo e($p['title']); ?>
                                </a>
                            </td>
                            <td><span class="badge bg-secondary rounded-pill"><?php echo e(ucfirst(str_replace('_', ' ', $p['purchase_type']))); ?></span></td>
                            <td>$<?php echo e(number_format(($p['total_amount'] ?? 0) / 100, 2)); ?></td>
                            <td class="text-muted small"><?php echo e(date('M d, Y H:i', strtotime($p['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h4 class="fw-bold mb-3 mt-4">Order Receipts</h4>
    <?php if (empty($orders)) : ?>
        <p class="text-muted">No receipts found.</p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
                        <th>Coupon</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o) : ?>
                        <tr>
                            <td>#ORD-<?php echo e($o['id']); ?></td>
                            <td>$<?php echo e(number_format($o['total_amount'] / 100, 2)); ?></td>
                            <td><?php echo e($o['coupon_code'] ? e($o['coupon_code']) : 'None'); ?></td>
                            <td><span class="badge bg-success"><?php echo e($o['status']); ?></span></td>
                            <td class="text-muted small"><?php echo e(date('M d, Y H:i', strtotime($o['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

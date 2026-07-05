<div class="panel card-surface">
    <h3 class="fw-bold mb-4">Billing & Invoices</h3>

    <!-- Subscription status overview card -->
    <div class="card p-4 mb-5 border-0 bg-light rounded-4">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <span class="text-uppercase text-primary font-monospace small">Active Workspace Plan</span>
                <h4 class="fw-bold mt-1 mb-2"><?php echo e($activeMembership ? $activeMembership['plan_name'] : 'Free Plan'); ?></h4>
                <p class="text-muted small mb-0">
                    <?php if ($activeMembership && !empty($activeMembership['ends_at'])) : ?>
                        Your membership plan is scheduled to renew on <strong><?php echo e(date('F d, Y', strtotime($activeMembership['ends_at']))); ?></strong>.
                    <?php else : ?>
                        No billing schedule exists. You are currently on the Free or Lifetime tier.
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?php echo e(url('/account/pricing')); ?>" class="btn btn-primary rounded-pill px-4">Change Workspace Plan</a>
            </div>
        </div>
    </div>

    <!-- Invoice Receipts -->
    <h4 class="fw-bold mb-3">Billing Invoice History</h4>
    <?php if (empty($orders)) : ?>
        <p class="text-muted">No billing invoices found.</p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Billed Amount</th>
                        <th>Billing Status</th>
                        <th>Billing Date</th>
                        <th>Receipt Document</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o) : ?>
                        <tr>
                            <td>#INV-<?php echo e($o['id']); ?></td>
                            <td>$<?php echo e(number_format($o['total_amount'] / 100, 2)); ?></td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1"><?php echo e(ucfirst($o['status'])); ?></span>
                            </td>
                            <td class="text-muted small"><?php echo e(date('M d, Y H:i', strtotime($o['created_at']))); ?></td>
                            <td>
                                <span class="text-muted small font-monospace">receipt_inv_<?php echo e($o['id']); ?>.pdf</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

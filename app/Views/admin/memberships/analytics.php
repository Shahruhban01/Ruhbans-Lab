<div class="panel card-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="eyebrow">Super Admin Dashboard</p>
            <h2 class="fw-bold m-0">Payment Analytics Dashboard</h2>
        </div>
    </div>

    <!-- Revenue Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 border bg-light">
                <span class="text-muted small text-uppercase">Revenue Today</span>
                <h3 class="fw-bold mt-1 text-primary">$<?php echo e(number_format($revToday, 2)); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border bg-light">
                <span class="text-muted small text-uppercase">Revenue This Week</span>
                <h3 class="fw-bold mt-1">$<?php echo e(number_format($revWeek, 2)); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border bg-light">
                <span class="text-muted small text-uppercase">Revenue This Month</span>
                <h3 class="fw-bold mt-1 text-success">$<?php echo e(number_format($revMonth, 2)); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border bg-light">
                <span class="text-muted small text-uppercase">Revenue This Year</span>
                <h3 class="fw-bold mt-1">$<?php echo e(number_format($revYear, 2)); ?></h3>
            </div>
        </div>
    </div>

    <!-- Categories & Logs Status -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card p-4 border h-100">
                <h4 class="fw-bold mb-3">Revenue Share</h4>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Membership Upgrades</span>
                        <span class="fw-bold text-primary">$<?php echo e(number_format($membershipRev, 2)); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Product One-Time Purchases</span>
                        <span class="fw-bold text-success">$<?php echo e(number_format($productRev, 2)); ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-4 border h-100">
                <h4 class="fw-bold mb-3">Transaction Failures & States</h4>
                <div class="row text-center g-2 mt-2">
                    <div class="col-4">
                        <h4 class="fw-bold text-warning"><?php echo e($pendingCount); ?></h4>
                        <span class="text-muted small text-uppercase">Pending</span>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold text-danger"><?php echo e($failedCount); ?></h4>
                        <span class="text-muted small text-uppercase">Failed</span>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold text-info"><?php echo e($refundedCount); ?></h4>
                        <span class="text-muted small text-uppercase">Refunded</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Clients & Gateway Status -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card p-4 border h-100">
                <h4 class="fw-bold mb-3">Top Customers Spend</h4>
                <?php if (empty($topCustomers)) : ?>
                    <p class="text-muted small">No customer transactions logged.</p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Email</th>
                                    <th class="text-end">Spend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topCustomers as $c) : ?>
                                    <tr>
                                        <td><strong><?php echo e($c['name']); ?></strong></td>
                                        <td class="text-muted small"><?php echo e($c['email']); ?></td>
                                        <td class="text-end fw-bold">$<?php echo e(number_format($c['spend'] / 100, 2)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-4 border h-100">
                <h4 class="fw-bold mb-3">Gateway Connection Status</h4>
                <ul class="list-group list-group-flush">
                    <?php foreach ($gatewayStatus as $g) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold"><?php echo e(ucfirst($g['gateway_key'])); ?></span>
                            <?php if ($g['is_active']) : ?>
                                <span class="badge bg-success rounded-pill px-3 py-1">Online & Active</span>
                            <?php else : ?>
                                <span class="badge bg-secondary rounded-pill px-3 py-1">Disabled</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

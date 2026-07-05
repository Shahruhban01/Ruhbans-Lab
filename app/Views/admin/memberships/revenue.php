<div class="panel card-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="eyebrow">Commerce Reports</p>
            <h2 class="fw-bold m-0">Revenue & Membership Analytics</h2>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card p-4 border bg-light">
                <span class="text-muted small text-uppercase">Simulated Revenue</span>
                <h3 class="display-6 fw-bold mt-2 text-primary">$<?php echo e(number_format($totalRevenue, 2)); ?></h3>
                <p class="text-muted small mb-0">Total volume from simulated purchases.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border bg-light">
                <span class="text-muted small text-uppercase">Active Memberships</span>
                <h3 class="display-6 fw-bold mt-2"><?php echo e($activeSubscriptions); ?></h3>
                <p class="text-muted small mb-0">Users with active paid tiers.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border bg-light">
                <span class="text-muted small text-uppercase">Total Orders</span>
                <h3 class="display-6 fw-bold mt-2"><?php echo e($totalOrders); ?></h3>
                <p class="text-muted small mb-0">All mock order invoice checkout count.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Popular Downloads -->
        <div class="col-lg-6">
            <div class="card p-4 border h-100">
                <h4 class="fw-bold mb-3">Popular Premium Downloads</h4>
                <?php if (empty($popularProducts)) : ?>
                    <p class="text-muted">No premium downloads recorded yet.</p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-end">Downloads</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popularProducts as $p) : ?>
                                    <tr>
                                        <td><?php echo e($p['title']); ?></td>
                                        <td class="text-end fw-bold"><?php echo e($p['downloads_count']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-6">
            <div class="card p-4 border h-100">
                <h4 class="fw-bold mb-3">Recent Transactions</h4>
                <?php if (empty($transactions)) : ?>
                    <p class="text-muted">No transactions logged.</p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $t) : ?>
                                    <tr>
                                        <td><?php echo e($t['user_name']); ?></td>
                                        <td><code class="small"><?php echo e(substr($t['transaction_reference'], 0, 12)); ?>...</code></td>
                                        <td>$<?php echo e(number_format($t['total_amount'] / 100, 2)); ?></td>
                                        <td class="text-muted small"><?php echo e(date('M d, H:i', strtotime($t['created_at']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

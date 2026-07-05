<?php
$membershipsData = isset($memberships['data']) ? $memberships['data'] : array();
$pagination = isset($memberships['pagination']) ? $memberships['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
$plans = isset($plans) ? $plans : array();
$usersList = isset($users) ? $users : array();
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Administration</p>
            <h2>Memberships Foundation</h2>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="panel card-surface h-100">
                <h3 class="h5 fw-bold mb-4">Assign Membership Plan</h3>
                <form method="post" action="<?php echo e(url('/admin/memberships/assign')); ?>" class="d-flex flex-column gap-3">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Select User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Choose User --</option>
                            <?php foreach ($usersList as $u) : ?>
                                <option value="<?php echo e($u['id']); ?>"><?php echo e($u['name']); ?> (<?php echo e($u['email']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Select Plan</label>
                        <select name="plan_id" class="form-select" required>
                            <option value="">-- Choose Plan --</option>
                            <?php foreach ($plans as $p) : ?>
                                <option value="<?php echo e($p['id']); ?>"><?php echo e($p['name']); ?> - <?php echo e($p['billing_period']); ?> ($<?php echo e(number_format($p['price_cents']/100, 2)); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Expiration Date (Leave blank for Lifetime)</label>
                        <input type="datetime-local" name="ends_at" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">Update Assignment</button>
                </form>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="panel card-surface h-100">
                <h3 class="h5 fw-bold mb-4">Active Member Subscriptions</h3>
                <div class="table-responsive">
                    <table class="table admin-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Current Plan</th>
                                <th>Status</th>
                                <th>Expires On</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($membershipsData === array()) : ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No custom membership plans assigned.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($membershipsData as $m) : ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-dark d-block"><?php echo e($m['user_name']); ?></span>
                                        <span class="text-muted small"><?php echo e($m['user_email']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border px-3 py-2"><?php echo e($m['plan_name']); ?></span>
                                    </td>
                                    <td>
                                        <span class="status-pill <?php echo $m['status'] === 'active' ? 'status-pill--active' : 'status-pill--inactive'; ?>">
                                            <?php echo e(ucfirst($m['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted small"><?php echo e($m['ends_at'] ? date('Y-m-d H:i', strtotime($m['ends_at'])) : 'Lifetime / No Expiry'); ?></span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($m['status'] === 'active') : ?>
                                            <form method="post" action="<?php echo e(url('/admin/memberships/cancel')); ?>" class="d-inline" onsubmit="return confirm('Cancel membership subscription?')">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="user_id" value="<?php echo e($m['user_id']); ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-pill">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <span class="text-muted small">Showing Page <?php echo e($pagination['page']); ?> of <?php echo e($pagination['pages']); ?></span>
                    <nav aria-label="Memberships page pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $pagination['pages']; $i++) : ?>
                                <li class="page-item <?php echo $i === $pagination['page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo e(url('/admin/memberships?page=' . $i)); ?>"><?php echo e($i); ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </section>
        </div>
    </div>
</section>

<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">User management</p>
            <h2>Users</h2>
        </div>
        <form method="get" action="<?php echo e(url('/admin/users')); ?>" class="search-form">
            <input type="search" name="search" value="<?php echo e($search); ?>" placeholder="Search users">
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
    </div>

    <section class="panel card-surface">
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users['data'] as $user) : ?>
                        <tr>
                            <td>
                                <strong><?php echo e($user['name']); ?></strong>
                                <span><?php echo e($user['email']); ?></span>
                            </td>
                            <td>
                                <form method="post" action="<?php echo e(url('/admin/users/update-role')); ?>" class="inline-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="user_id" value="<?php echo e($user['id']); ?>">
                                    <select name="role_id">
                                        <?php foreach ($roles as $role) : ?>
                                            <option value="<?php echo e($role['id']); ?>"<?php echo (int) $user['role_id'] === (int) $role['id'] ? ' selected' : ''; ?>><?php echo e($role['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-ghost">Save</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="<?php echo e(url('/admin/users/toggle-status')); ?>" class="inline-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="user_id" value="<?php echo e($user['id']); ?>">
                                    <input type="hidden" name="is_active" value="<?php echo e((int) $user['is_active'] ? 0 : 1); ?>">
                                    <span class="status-pill <?php echo (int) $user['is_active'] ? 'status-pill--active' : 'status-pill--inactive'; ?>">
                                        <?php echo (int) $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <button type="submit" class="btn btn-ghost"><?php echo (int) $user['is_active'] ? 'Disable' : 'Enable'; ?></button>
                                </form>
                            </td>
                            <td><?php echo e(isset($user['last_login']) ? $user['last_login'] : '-'); ?></td>
                            <td><?php echo e(isset($user['created_at']) ? $user['created_at'] : '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination-summary">
            <span>Page <?php echo e($users['pagination']['page']); ?> of <?php echo e($users['pagination']['pages']); ?></span>
            <span>Total users: <?php echo e($users['pagination']['total']); ?></span>
        </div>
    </section>
</section>

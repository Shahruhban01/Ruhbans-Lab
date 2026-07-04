<section class="page-stack">
    <div class="page-header">
        <div>
            <p class="eyebrow">Dashboard overview</p>
            <h2>Statistics</h2>
        </div>
    </div>

    <div class="stat-grid">
        <?php foreach ($stats as $stat) : ?>
            <?php echo view('admin/partials/stat-card', $stat, array('layout' => false)); ?>
        <?php endforeach; ?>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Recent activity</h3>
                <a href="<?php echo e(url('/admin/activity-logs')); ?>">View all</a>
            </div>
            <div class="activity-list">
                <?php foreach ($recentActivity as $item) : ?>
                    <?php echo view('admin/partials/activity-item', array('item' => $item), array('layout' => false)); ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Recent users</h3>
                <a href="<?php echo e(url('/admin/users')); ?>">Manage users</a>
            </div>
            <div class="list-stack">
                <?php foreach ($recentUsers as $user) : ?>
                    <article class="list-row">
                        <div>
                            <strong><?php echo e($user['name']); ?></strong>
                            <p><?php echo e($user['email']); ?></p>
                        </div>
                        <span><?php echo e(isset($user['role_name']) ? $user['role_name'] : ''); ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</section>

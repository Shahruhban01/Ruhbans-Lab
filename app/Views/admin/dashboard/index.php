<?php
$stats = isset($stats) && is_array($stats) ? $stats : array();
$recentActivity = isset($recentActivity) && is_array($recentActivity) ? $recentActivity : array();
$recentUsers = isset($recentUsers) && is_array($recentUsers) ? $recentUsers : array();

$statValue = function (string $label, $default = 0) use ($stats) {
    foreach ($stats as $stat) {
        if (isset($stat['label']) && $stat['label'] === $label) {
            return $stat['value'];
        }
    }

    return $default;
};

$quickActions = array(
    array('label' => 'New content', 'href' => url('/admin/content/create'), 'description' => 'Start a draft or publish immediately.'),
    array('label' => 'Review drafts', 'href' => url('/admin/content/drafts'), 'description' => 'Pick up work in progress.'),
    array('label' => 'Open analytics', 'href' => url('/admin/analytics'), 'description' => 'Check content, search, and system signals.'),
    array('label' => 'Manage media', 'href' => url('/admin/content/media'), 'description' => 'Upload and organize assets.'),
);

$workflowCards = array(
    array('label' => 'Total users', 'value' => $statValue('Total Users', 0), 'note' => 'Registered accounts across the platform'),
    array('label' => 'Active users', 'value' => $statValue('Active Users', 0), 'note' => 'Accounts with active status'),
    array('label' => 'Admins', 'value' => $statValue('Admins', 0), 'note' => 'People with elevated permissions'),
    array('label' => 'Activity today', 'value' => $statValue('Today\'s Logs', 0), 'note' => 'Admin operations recorded today'),
);
?>
<section class="page-stack admin-dashboard">
    <section class="admin-dashboard-hero card-surface">
        <div class="admin-dashboard-hero__copy">
            <p class="eyebrow">Workspace overview</p>
            <h2>Welcome back. This is the center of your publishing workflow.</h2>
            <p class="lead">Use the command palette, quick actions, and activity signals to move from monitoring to publishing faster.</p>
            <div class="content-actions">
                <a class="btn btn-primary" href="<?php echo e(url('/admin/content/create')); ?>">New content</a>
                <a class="btn btn-secondary" href="<?php echo e(url('/admin/content/drafts')); ?>">Draft queue</a>
                <a class="btn btn-secondary" href="<?php echo e(url('/admin/analytics')); ?>">Open analytics</a>
            </div>
        </div>
        <div class="admin-dashboard-hero__rail">
            <div class="workflow-card">
                <span class="eyebrow">Quick actions</span>
                <div class="quick-action-list">
                    <?php foreach ($quickActions as $action) : ?>
                        <a class="quick-action" href="<?php echo e($action['href']); ?>">
                            <strong><?php echo e($action['label']); ?></strong>
                            <span><?php echo e($action['description']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-section">
        <div class="section-head section-head--compact">
            <div>
                <p class="eyebrow">Dashboard signals</p>
                <h2>At a glance</h2>
            </div>
        </div>
        <div class="stat-grid">
            <?php foreach ($stats as $stat) : ?>
                <?php echo view('admin/partials/stat-card', $stat, array('layout' => false)); ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="admin-section grid-two admin-workflow-grid">
        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Recent activity</h3>
                <a href="<?php echo e(url('/admin/activity-logs')); ?>">View all</a>
            </div>
            <div class="activity-list activity-list--dense timeline-list">
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
                        <span class="status-pill status-pill--active"><?php echo e(isset($user['role_name']) ? $user['role_name'] : ''); ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </section>

    <section class="admin-section grid-two">
        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Publishing shortcuts</h3>
                <a href="<?php echo e(url('/admin/content')); ?>">Content index</a>
            </div>
            <div class="workflow-grid">
                <?php foreach ($workflowCards as $card) : ?>
                    <article class="workflow-card workflow-card--compact">
                        <p class="eyebrow"><?php echo e($card['label']); ?></p>
                        <strong><?php echo e($card['value']); ?></strong>
                        <span><?php echo e($card['note']); ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Operational links</h3>
            </div>
            <div class="quick-link-grid">
                <a class="quick-link" href="<?php echo e(url('/admin/content/categories')); ?>"><strong>Categories</strong><span>Structure the taxonomy.</span></a>
                <a class="quick-link" href="<?php echo e(url('/admin/content/tags')); ?>"><strong>Tags</strong><span>Maintain the label system.</span></a>
                <a class="quick-link" href="<?php echo e(url('/admin/content/media')); ?>"><strong>Media</strong><span>Review storage and uploads.</span></a>
                <a class="quick-link" href="<?php echo e(url('/admin/settings')); ?>"><strong>Settings</strong><span>Branding, SEO, and system controls.</span></a>
            </div>
        </section>
    </section>
</section>

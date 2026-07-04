<?php
$cards = isset($cards) && is_array($cards) ? $cards : array();
$content = isset($content) && is_array($content) ? $content : array();
$media = isset($media) && is_array($media) ? $media : array();
$search = isset($search) && is_array($search) ? $search : array();
$users = isset($users) && is_array($users) ? $users : array();
$activity = isset($activity) && is_array($activity) ? $activity : array();
$systemInfo = isset($systemInfo) && is_array($systemInfo) ? $systemInfo : array();

$contentTotal = 0;
$publishedTotal = 0;
foreach ($content as $row) {
    $contentTotal += isset($row['total']) ? (int) $row['total'] : 0;
    $publishedTotal += isset($row['published_total']) ? (int) $row['published_total'] : 0;
}

$mediaTotal = isset($media['totals']['count']) ? (int) $media['totals']['count'] : 0;
$mediaBytes = isset($media['totals']['bytes']) ? (int) $media['totals']['bytes'] : 0;
$searchTotal = isset($search['totals']['count']) ? (int) $search['totals']['count'] : 0;
$userTotal = 0;
foreach (isset($users['by_role']) ? $users['by_role'] : array() as $row) {
    $userTotal += isset($row['total']) ? (int) $row['total'] : 0;
}
?>
<section class="page-stack admin-analytics">
    <section class="admin-dashboard-hero card-surface">
        <div class="admin-dashboard-hero__copy">
            <p class="eyebrow">Administration</p>
            <h2>Analytics dashboard</h2>
            <p class="lead">Track content health, search behavior, media growth, and system status from a single operational view.</p>
        </div>
        <div class="admin-dashboard-hero__rail">
            <div class="workflow-card workflow-card--compact">
                <p class="eyebrow">Snapshot</p>
                <strong><?php echo e($contentTotal); ?></strong>
                <span>Total content items with <?php echo e($publishedTotal); ?> published.</span>
            </div>
        </div>
    </section>

    <div class="stat-grid">
        <?php foreach ($cards as $stat) : ?>
            <?php echo view('admin/partials/stat-card', $stat, array('layout' => false)); ?>
        <?php endforeach; ?>
    </div>

    <div class="grid-two admin-metrics-grid">
        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Content health</h3>
                <span class="status-pill status-pill--active"><?php echo e($contentTotal); ?> items</span>
            </div>
            <div class="metric-list">
                <?php foreach ($content as $row) : ?>
                    <div class="metric-row">
                        <div>
                            <strong><?php echo e($row['name']); ?></strong>
                            <p><?php echo e($row['slug']); ?></p>
                        </div>
                        <div class="metric-row__value">
                            <span><?php echo e($row['total']); ?></span>
                            <small><?php echo e($row['published_total']); ?> published</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Media overview</h3>
                <span class="status-pill status-pill--active"><?php echo e($mediaTotal); ?> files</span>
            </div>
            <div class="metric-list">
                <div class="metric-row">
                    <div>
                        <strong>Library size</strong>
                        <p>Storage footprint across uploaded assets.</p>
                    </div>
                    <div class="metric-row__value"><span><?php echo e(number_format($mediaBytes)); ?></span><small>bytes</small></div>
                </div>
                <?php foreach (isset($media['by_type']) ? $media['by_type'] : array() as $row) : ?>
                    <div class="metric-row">
                        <div>
                            <strong><?php echo e($row['mime_type']); ?></strong>
                            <p>Distribution by file type</p>
                        </div>
                        <div class="metric-row__value"><span><?php echo e($row['total']); ?></span><small><?php echo e($row['bytes']); ?> bytes</small></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="grid-two admin-metrics-grid">
        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Search analytics</h3>
                <span class="status-pill status-pill--active"><?php echo e($searchTotal); ?> queries</span>
            </div>
            <div class="metric-list">
                <?php foreach (isset($search['popular']) ? $search['popular'] : array() as $row) : ?>
                    <div class="metric-row">
                        <div>
                            <strong><?php echo e($row['normalized_term']); ?></strong>
                            <p>Average result quality</p>
                        </div>
                        <div class="metric-row__value"><span><?php echo e($row['total']); ?></span><small>avg <?php echo e((int) $row['avg_results']); ?></small></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <div class="panel__head">
                <h3>User activity</h3>
                <span class="status-pill status-pill--active"><?php echo e($userTotal); ?> users</span>
            </div>
            <div class="metric-list">
                <?php foreach (isset($users['by_role']) ? $users['by_role'] : array() as $row) : ?>
                    <div class="metric-row">
                        <div>
                            <strong><?php echo e($row['role_name']); ?></strong>
                            <p>Accounts with this role</p>
                        </div>
                        <div class="metric-row__value"><span><?php echo e($row['total']); ?></span><small>role total</small></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <section class="panel card-surface">
        <div class="panel__head">
            <h3>Activity timeline</h3>
            <span class="status-pill status-pill--active"><?php echo e(isset($activity['today']['audit_logs']) ? $activity['today']['audit_logs'] : 0); ?> logs today</span>
        </div>
        <div class="activity-list activity-list--dense timeline-list">
            <?php foreach (isset($activity['recent_audit']) ? $activity['recent_audit'] : array() as $row) : ?>
                <article class="activity-item">
                    <header>
                        <strong><?php echo e($row['action']); ?></strong>
                        <time><?php echo e($row['created_at']); ?></time>
                    </header>
                    <p><?php echo e($row['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel card-surface">
        <div class="panel__head">
            <h3>System status</h3>
            <span class="status-pill status-pill--active">Live environment</span>
        </div>
        <div class="system-info-grid">
            <?php foreach ($systemInfo as $label => $value) : ?>
                <article class="system-info-card">
                    <p class="eyebrow"><?php echo e(str_replace('_', ' ', $label)); ?></p>
                    <strong><?php echo e($value); ?></strong>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</section>
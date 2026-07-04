<?php
$cards = isset($cards) && is_array($cards) ? $cards : array();
$content = isset($content) && is_array($content) ? $content : array();
$media = isset($media) && is_array($media) ? $media : array();
$search = isset($search) && is_array($search) ? $search : array();
$users = isset($users) && is_array($users) ? $users : array();
$activity = isset($activity) && is_array($activity) ? $activity : array();
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Administration</p>
            <h2>Analytics dashboard</h2>
        </div>
    </div>

    <div class="stat-grid">
        <?php foreach ($cards as $stat) : ?>
            <?php echo view('admin/partials/stat-card', $stat, array('layout' => false)); ?>
        <?php endforeach; ?>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Content statistics</h3>
            <div class="tree-list">
                <?php foreach ($content as $row) : ?>
                    <div class="tree-row">
                        <span><?php echo e($row['name']); ?> (<?php echo e($row['slug']); ?>)</span>
                        <span><?php echo e($row['total']); ?> total · <?php echo e($row['published_total']); ?> published</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <h3>Media statistics</h3>
            <div class="tree-list">
                <?php foreach (isset($media['by_type']) ? $media['by_type'] : array() as $row) : ?>
                    <div class="tree-row">
                        <span><?php echo e($row['mime_type']); ?></span>
                        <span><?php echo e($row['total']); ?> files · <?php echo e($row['bytes']); ?> bytes</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Search statistics</h3>
            <div class="tree-list">
                <?php foreach (isset($search['popular']) ? $search['popular'] : array() as $row) : ?>
                    <div class="tree-row">
                        <span><?php echo e($row['normalized_term']); ?></span>
                        <span><?php echo e($row['total']); ?> queries · avg results <?php echo e((int) $row['avg_results']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <h3>User statistics</h3>
            <div class="tree-list">
                <?php foreach (isset($users['by_role']) ? $users['by_role'] : array() as $row) : ?>
                    <div class="tree-row">
                        <span><?php echo e($row['role_name']); ?></span>
                        <span><?php echo e($row['total']); ?> users</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <section class="panel card-surface">
        <h3>Activity statistics</h3>
        <div class="tree-list">
            <div class="tree-row">
                <span>Audit logs today</span>
                <span><?php echo e(isset($activity['today']['audit_logs']) ? $activity['today']['audit_logs'] : 0); ?></span>
            </div>
            <div class="tree-row">
                <span>Engagement events today</span>
                <span><?php echo e(isset($activity['today']['activity_events']) ? $activity['today']['activity_events'] : 0); ?></span>
            </div>
        </div>
        <div class="activity-list activity-list--dense">
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
</section>
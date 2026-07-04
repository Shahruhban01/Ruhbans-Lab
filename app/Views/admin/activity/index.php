<section class="page-stack">
    <div class="page-header">
        <div>
            <p class="eyebrow">Audit trail</p>
            <h2>Activity logs</h2>
        </div>
    </div>

    <section class="panel card-surface">
        <div class="activity-list activity-list--dense">
            <?php foreach ($logs as $item) : ?>
                <?php echo view('admin/partials/activity-item', array('item' => $item), array('layout' => false)); ?>
            <?php endforeach; ?>
        </div>
    </section>
</section>

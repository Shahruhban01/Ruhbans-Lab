<?php
$revisionList = isset($revisions) && is_array($revisions) ? $revisions : array();
$postData = isset($post) && is_array($post) ? $post : array();
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Version control</p>
            <h2>Revisions</h2>
        </div>
        <a class="btn btn-secondary" href="<?php echo e(url('/admin/content/' . (isset($postData['id']) ? $postData['id'] : '') . '/edit')); ?>">Back to editor</a>
    </div>

    <section class="panel card-surface">
        <div class="revision-list">
            <?php foreach ($revisionList as $revision) : ?>
                <article class="revision-item revision-item--stacked">
                    <div>
                        <strong><?php echo e($revision['label']); ?></strong>
                        <p><?php echo e($revision['created_at']); ?></p>
                    </div>
                    <form method="post" action="<?php echo e(url('/admin/content/' . $postData['id'] . '/revisions/' . $revision['id'] . '/restore')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-secondary">Restore</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</section>

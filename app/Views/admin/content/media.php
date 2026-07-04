<?php
$mediaData = isset($media['data']) ? $media['data'] : array();
$pagination = isset($media['pagination']) ? $media['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Asset library</p>
            <h2>Media Manager</h2>
        </div>
    </div>

    <section class="panel card-surface">
        <form method="get" action="<?php echo e(url('/admin/content/media')); ?>" class="search-form">
            <input type="search" name="search" value="<?php echo e(isset($_GET['search']) ? $_GET['search'] : ''); ?>" placeholder="Search media">
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
    </section>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Upload media</h3>
            <form method="post" action="<?php echo e(url('/admin/content/media')); ?>" enctype="multipart/form-data" class="auth-form">
                <?php echo csrf_field(); ?>
                <label><span>File</span><input type="file" name="file" accept="image/*,application/pdf" required></label>
                <label><span>ALT text</span><input type="text" name="alt_text" value=""></label>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </section>

        <section class="panel card-surface">
            <h3>Library</h3>
            <div class="media-grid">
                <?php foreach ($mediaData as $item) : ?>
                    <article class="media-card">
                        <div class="media-card__preview">
                            <span><?php echo e($item['mime_type']); ?></span>
                        </div>
                        <strong><?php echo e($item['original_name']); ?></strong>
                        <small><?php echo e($item['path']); ?></small>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="pagination-summary">
                <span>Page <?php echo e($pagination['page']); ?> of <?php echo e($pagination['pages']); ?></span>
                <span>Total files: <?php echo e($pagination['total']); ?></span>
            </div>
        </section>
    </div>
</section>

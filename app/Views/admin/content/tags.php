<?php
$tagList = isset($tags) && is_array($tags) ? $tags : array();
$form = isset($form) && is_array($form) ? $form : array();
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Taxonomy</p>
            <h2>Tags</h2>
        </div>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Tag list</h3>
            <div class="tree-list">
                <?php foreach ($tagList as $tag) : ?>
                    <div class="tree-row">
                        <span><?php echo e($tag['name']); ?></span>
                        <span><?php echo e($tag['slug']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <h3>Create or update tag</h3>
            <form method="post" action="<?php echo e(url('/admin/content/tags')); ?>" class="auth-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e(isset($form['id']) ? $form['id'] : ''); ?>">
                <label><span>Name</span><input type="text" name="name" value="<?php echo e(isset($form['name']) ? $form['name'] : ''); ?>" required></label>
                <label><span>Slug</span><input type="text" name="slug" value="<?php echo e(isset($form['slug']) ? $form['slug'] : ''); ?>"></label>
                <button type="submit" class="btn btn-primary">Save tag</button>
            </form>
        </section>
    </div>
</section>

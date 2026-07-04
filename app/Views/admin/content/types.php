<?php
$typeList = isset($contentTypes) && is_array($contentTypes) ? $contentTypes : array();
$form = isset($form) && is_array($form) ? $form : array();
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Taxonomy</p>
            <h2>Content Types</h2>
        </div>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Content types</h3>
            <div class="tree-list">
                <?php foreach ($typeList as $type) : ?>
                    <div class="tree-row">
                        <span><?php echo e($type['name']); ?></span>
                        <span><?php echo e($type['slug']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <h3>Create or update type</h3>
            <form method="post" action="<?php echo e(url('/admin/content/types')); ?>" class="auth-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e(isset($form['id']) ? $form['id'] : ''); ?>">
                <label><span>Name</span><input type="text" name="name" value="<?php echo e(isset($form['name']) ? $form['name'] : ''); ?>" required></label>
                <label><span>Slug</span><input type="text" name="slug" value="<?php echo e(isset($form['slug']) ? $form['slug'] : ''); ?>"></label>
                <label><span>Description</span><textarea name="description" rows="3"><?php echo e(isset($form['description']) ? $form['description'] : ''); ?></textarea></label>
                <label><span>Icon</span><input type="text" name="icon" value="<?php echo e(isset($form['icon']) ? $form['icon'] : ''); ?>"></label>
                <button type="submit" class="btn btn-primary">Save content type</button>
            </form>
        </section>
    </div>
</section>

<?php
$categoryTree = isset($categories) && is_array($categories) ? $categories : array();
$form = isset($form) && is_array($form) ? $form : array();
$errors = isset($errors) && is_array($errors) ? $errors : array();

$renderTree = function (array $items, int $depth = 0) use (&$renderTree) {
    foreach ($items as $item) {
        echo '<div class="tree-row">';
        echo '<span style="padding-left:' . (int) ($depth * 20) . 'px">' . e($item['name']) . '</span>';
        echo '<span>' . e($item['slug']) . '</span>';
        echo '</div>';
        if (!empty($item['children'])) {
            $renderTree($item['children'], $depth + 1);
        }
    }
};
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Taxonomy</p>
            <h2>Categories</h2>
        </div>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Category tree</h3>
            <div class="tree-list">
                <?php $renderTree($categoryTree); ?>
            </div>
        </section>

        <section class="panel card-surface">
            <h3>Create or update category</h3>
            <form method="post" action="<?php echo e(url('/admin/content/categories')); ?>" class="auth-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e(isset($form['id']) ? $form['id'] : ''); ?>">
                <label><span>Name</span><input type="text" name="name" value="<?php echo e(isset($form['name']) ? $form['name'] : ''); ?>" required></label>
                <label><span>Slug</span><input type="text" name="slug" value="<?php echo e(isset($form['slug']) ? $form['slug'] : ''); ?>"></label>
                <label><span>Parent ID</span><input type="number" name="parent_id" value="<?php echo e(isset($form['parent_id']) ? $form['parent_id'] : ''); ?>"></label>
                <label><span>Description</span><textarea name="description" rows="3"><?php echo e(isset($form['description']) ? $form['description'] : ''); ?></textarea></label>
                <label><span>Icon</span><input type="text" name="icon" value="<?php echo e(isset($form['icon']) ? $form['icon'] : ''); ?>"></label>
                <label><span>Featured image</span><input type="text" name="featured_image" value="<?php echo e(isset($form['featured_image']) ? $form['featured_image'] : ''); ?>"></label>
                <button type="submit" class="btn btn-primary">Save category</button>
            </form>
        </section>
    </div>
</section>

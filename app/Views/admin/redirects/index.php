<?php
$redirects = isset($redirects) && is_array($redirects) ? $redirects : array();
$form = isset($form) && is_array($form) ? $form : array();
$errors = isset($errors) && is_array($errors) ? $errors : array();
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">SEO</p>
            <h2>Redirect manager</h2>
        </div>
    </div>

    <?php if (!empty($errors['general'])) : ?>
        <div class="flash-message flash-message--error"><?php echo e($errors['general']); ?></div>
    <?php endif; ?>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Redirects</h3>
            <div class="tree-list">
                <?php if ($redirects === array()) : ?>
                    <div class="empty-inline">No redirects have been created yet.</div>
                <?php endif; ?>
                <?php foreach ($redirects as $redirect) : ?>
                    <div class="tree-row">
                        <span><?php echo e($redirect['source_path']); ?> → <?php echo e($redirect['target_path']); ?></span>
                        <span><?php echo e($redirect['status_code']); ?><?php echo !empty($redirect['is_active']) ? ' active' : ' inactive'; ?></span>
                        <form method="post" action="<?php echo e(url('/admin/redirects/' . $redirect['id'] . '/delete')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-secondary">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <h3>Create or update redirect</h3>
            <form method="post" action="<?php echo e(url('/admin/redirects')); ?>" class="auth-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e(isset($form['id']) ? $form['id'] : ''); ?>">
                <label><span>Source path</span><input type="text" name="source_path" value="<?php echo e(isset($form['source_path']) ? $form['source_path'] : ''); ?>" placeholder="/old-article" required></label>
                <label><span>Target path</span><input type="text" name="target_path" value="<?php echo e(isset($form['target_path']) ? $form['target_path'] : ''); ?>" placeholder="/content/new-article"></label>
                <label><span>Status code</span>
                    <select name="status_code">
                        <option value="301"<?php echo isset($form['status_code']) && (string) $form['status_code'] === '301' ? ' selected' : ''; ?>>301</option>
                        <option value="302"<?php echo isset($form['status_code']) && (string) $form['status_code'] === '302' ? ' selected' : ''; ?>>302</option>
                        <option value="410"<?php echo isset($form['status_code']) && (string) $form['status_code'] === '410' ? ' selected' : ''; ?>>410</option>
                    </select>
                </label>
                <label><span>Reason</span><input type="text" name="reason" value="<?php echo e(isset($form['reason']) ? $form['reason'] : ''); ?>"></label>
                <label class="search-checkbox"><input type="checkbox" name="is_active" value="1"<?php echo empty($form) || !empty($form['is_active']) ? ' checked' : ''; ?>> <span>Active</span></label>
                <button type="submit" class="btn btn-primary">Save redirect</button>
            </form>
        </section>
    </div>
</section>
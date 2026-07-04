<?php
$role = is_array($currentUser) && isset($currentUser['role_name']) ? $currentUser['role_name'] : 'Guest';
?>
<aside class="admin-sidebar" data-admin-sidebar>
    <div class="admin-sidebar__brand">
        <a class="brand" href="<?php echo e(url('/admin')); ?>"><?php echo e(config('app.name', 'Developer Ruhban')); ?></a>
        <span class="admin-sidebar__role"><?php echo e($role); ?></span>
    </div>
    <nav class="admin-nav" aria-label="Admin navigation">
        <a class="admin-nav__link" href="<?php echo e(url('/admin')); ?>">Dashboard</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/content')); ?>">Content</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/content/drafts')); ?>">Drafts</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/content/categories')); ?>">Categories</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/content/tags')); ?>">Tags</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/content/types')); ?>">Content Types</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/content/media')); ?>">Media</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/analytics')); ?>">Analytics</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/settings')); ?>">Settings</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/redirects')); ?>">Redirects</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/users')); ?>">Users</a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/activity-logs')); ?>">Activity Logs</a>
        <form method="post" action="<?php echo e(url('/admin/logout')); ?>" class="admin-nav__logout">
            <?php echo csrf_field(); ?>
            <button type="submit" class="admin-nav__link admin-nav__link--button">Logout</button>
        </form>
    </nav>
</aside>

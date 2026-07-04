<?php
$role = is_array($currentUser) && isset($currentUser['role_name']) ? $currentUser['role_name'] : 'Guest';
?>
<aside class="admin-sidebar" data-admin-sidebar>
    <div class="admin-sidebar__brand">
        <a class="brand" href="<?php echo e(url('/admin')); ?>"><?php echo e(config('app.name', 'Developer Ruhban')); ?></a>
        <span class="admin-sidebar__role mt-2"><?php echo e($role); ?></span>
    </div>
    <nav class="admin-nav" aria-label="Admin navigation">
        <a class="admin-nav__link" href="<?php echo e(url('/admin')); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            Dashboard
        </a>
        <a class="admin-nav__link" data-bs-toggle="collapse" href="#contentSubmenu" role="button" aria-expanded="false" aria-controls="contentSubmenu">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <span>Content Stack</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ms-auto"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </a>
        <div class="collapse show" id="contentSubmenu">
            <div class="ps-3 d-flex flex-column gap-1 mt-1">
                <a class="admin-nav__link py-2" href="<?php echo e(url('/admin/content')); ?>">
                    Content Listing
                </a>
                <a class="admin-nav__link py-2" href="<?php echo e(url('/admin/content/drafts')); ?>">
                    Drafts
                </a>
                <a class="admin-nav__link py-2" href="<?php echo e(url('/admin/content/categories')); ?>">
                    Categories
                </a>
                <a class="admin-nav__link py-2" href="<?php echo e(url('/admin/content/tags')); ?>">
                    Tags
                </a>
                <a class="admin-nav__link py-2" href="<?php echo e(url('/admin/content/media-manager')); ?>">
                    Media Manager
                </a>
            </div>
        </div>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/analytics')); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            Analytics
        </a>
        <a class="admin-nav__link" href="<?php echo e(url('/admin/settings')); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            Settings
        </a>
        <form method="post" action="<?php echo e(url('/admin/logout')); ?>" class="admin-nav__logout">
            <?php echo csrf_field(); ?>
            <button type="submit" class="admin-nav__link admin-nav__link--button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
            </button>
        </form>
    </nav>
</aside>

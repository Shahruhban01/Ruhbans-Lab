<?php
$name = is_array($currentUser) && isset($currentUser['name']) ? $currentUser['name'] : 'Admin';
$email = is_array($currentUser) && isset($currentUser['email']) ? $currentUser['email'] : '';
?>
<header class="admin-navbar">
    <div class="admin-navbar__left">
        <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
        <div>
            <p class="admin-navbar__eyebrow">Admin Panel</p>
            <h1 class="admin-navbar__title"><?php echo e($name); ?></h1>
        </div>
    </div>
    <div class="admin-navbar__search">
        <input type="search" placeholder="Type '/' to search, Ctrl + K for actions" aria-label="Search admin">
    </div>
    <div class="admin-navbar__actions">
        <button type="button" class="btn btn-secondary btn-sm" data-command-palette-open>Actions</button>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle theme">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        </button>
    </div>
</header>

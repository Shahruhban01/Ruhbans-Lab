<?php
$name = is_array($currentUser) && isset($currentUser['name']) ? $currentUser['name'] : 'Admin';
$email = is_array($currentUser) && isset($currentUser['email']) ? $currentUser['email'] : '';
?>
<header class="admin-navbar">
    <div class="admin-navbar__left">
        <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar">Menu</button>
        <div>
            <p class="admin-navbar__eyebrow">Admin panel</p>
            <h1 class="admin-navbar__title"><?php echo e($name); ?></h1>
        </div>
    </div>
    <div class="admin-navbar__search">
        <input type="search" placeholder="Search content, users, settings" aria-label="Search admin">
    </div>
    <div class="admin-navbar__user">
        <div>
            <strong><?php echo e($name); ?></strong>
            <span><?php echo e($email); ?></span>
        </div>
    </div>
</header>

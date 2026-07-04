<?php

$title = $meta['title'] ?? 'Admin Dashboard';
$description = $meta['description'] ?? 'Admin dashboard for Developer Ruhban.';
$canonical = $meta['canonical'] ?? url(request()->path());
$robots = $meta['robots'] ?? 'noindex, nofollow';
$schemaType = 'WebPage';
$theme = $_COOKIE['theme'] ?? 'system';
$currentUser = isset($currentUser) ? $currentUser : app()->session()->get(config('auth.session_key', 'auth_user'));
$flashSuccess = app()->session()->pullFlash('success');
$flashError = app()->session()->pullFlash('error');
$commandHint = 'Search content, settings, analytics, backups...';

?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo e($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?></title>
    <meta name="description" content="<?php echo e($description); ?>">
    <meta name="robots" content="<?php echo e($robots); ?>">
    <link rel="canonical" href="<?php echo e($canonical); ?>">
    <meta property="og:title" content="<?php echo e($title); ?>">
    <meta property="og:description" content="<?php echo e($description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e($canonical); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/core.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/components.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/admin.css')); ?>">
    <script type="application/ld+json">
    <?php echo json_encode(array(
        '@context' => 'https://schema.org',
        '@type' => $schemaType,
        'name' => config('app.name', 'Developer Ruhban'),
        'url' => $canonical,
        'description' => $description,
    ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    </script>
</head>
<body class="admin-shell">
<div class="admin-layout">
    <?php echo view('admin/partials/sidebar', array('currentUser' => $currentUser), array('layout' => false)); ?>
    <div class="admin-main">
        <?php echo view('admin/partials/navbar', array('currentUser' => $currentUser), array('layout' => false)); ?>
        <main class="admin-content container admin-content--shell">
            <?php if ($flashSuccess) : ?>
                <div class="alert alert-success m-3"><?php echo e($flashSuccess); ?></div>
            <?php endif; ?>
            <?php if ($flashError) : ?>
                <div class="alert alert-danger m-3"><?php echo e($flashError); ?></div>
            <?php endif; ?>
            <div class="admin-quickbar card-surface panel mb-4">
                <div>
                    <p class="eyebrow">Command search</p>
                    <h2><?php echo e($commandHint); ?></h2>
                </div>
                <div class="content-actions">
                    <a class="btn btn-primary" href="<?php echo e(url('/admin/content/create')); ?>">New post</a>
                    <a class="btn btn-secondary" href="<?php echo e(url('/admin/content/media')); ?>">Upload media</a>
                    <a class="btn btn-secondary" href="<?php echo e(url('/admin/settings')); ?>">Settings</a>
                </div>
            </div>
            <?php echo $content; ?>
        </main>
    </div>
</div>
<div class="admin-command-palette" data-command-palette hidden>
    <div class="admin-command-palette__backdrop" data-command-palette-close></div>
    <div class="admin-command-palette__dialog card-surface" role="dialog" aria-modal="true" aria-label="Command palette">
        <div class="admin-command-palette__head">
            <div>
                <p class="eyebrow">Command palette</p>
                <h2>Search actions and destinations</h2>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" data-command-palette-close>Close</button>
        </div>
        <input type="search" class="admin-command-palette__search" data-command-palette-input placeholder="Type a command or destination" autocomplete="off">
        <div class="admin-command-palette__list" data-command-palette-list>
            <a class="admin-command-item" data-command-item data-keywords="dashboard overview home" href="<?php echo e(url('/admin')); ?>">
                <strong>Dashboard</strong>
                <span>Open overview and activity</span>
            </a>
            <a class="admin-command-item" data-command-item data-keywords="new content create post" href="<?php echo e(url('/admin/content/create')); ?>">
                <strong>New content</strong>
                <span>Start a draft</span>
            </a>
            <a class="admin-command-item" data-command-item data-keywords="drafts unpublished" href="<?php echo e(url('/admin/content/drafts')); ?>">
                <strong>Drafts</strong>
                <span>Review unpublished items</span>
            </a>
            <a class="admin-command-item" data-command-item data-keywords="media assets library upload" href="<?php echo e(url('/admin/content/media')); ?>">
                <strong>Media manager</strong>
                <span>Browse and upload assets</span>
            </a>
            <a class="admin-command-item" data-command-item data-keywords="analytics search seo reports" href="<?php echo e(url('/admin/analytics')); ?>">
                <strong>Analytics</strong>
                <span>Open traffic and search insights</span>
            </a>
            <a class="admin-command-item" data-command-item data-keywords="settings configuration brand" href="<?php echo e(url('/admin/settings')); ?>">
                <strong>Settings</strong>
                <span>Open configuration</span>
            </a>
        </div>
        <p class="admin-command-palette__hint">Tip: press Ctrl + K anywhere in the admin area.</p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?php echo e(asset('assets/js/app.js')); ?>" defer></script>
</body>
</html>

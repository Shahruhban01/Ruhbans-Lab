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
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
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
                <div class="flash-message flash-message--success"><?php echo e($flashSuccess); ?></div>
            <?php endif; ?>
            <?php if ($flashError) : ?>
                <div class="flash-message flash-message--error"><?php echo e($flashError); ?></div>
            <?php endif; ?>
            <?php echo $content; ?>
        </main>
    </div>
</div>
<script src="<?php echo e(asset('assets/js/app.js')); ?>" defer></script>
</body>
</html>

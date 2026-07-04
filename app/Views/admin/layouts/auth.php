<?php

$title = $meta['title'] ?? 'Admin Login';
$description = $meta['description'] ?? 'Secure admin authentication.';
$canonical = $meta['canonical'] ?? url(request()->path());
$robots = $meta['robots'] ?? 'noindex, nofollow';
$dbDefaultTheme = (new \App\Repositories\SettingRepository(app()->database()->connection()))->value('theme.default', 'light');
$theme = $_COOKIE['theme'] ?? $dbDefaultTheme;
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
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
</head>
<body class="admin-shell admin-shell--auth">
    <main class="auth-shell">
        <div class="auth-card">
            <div class="auth-card__brand">
                <a class="brand" href="<?php echo e(url('/')); ?>"><?php echo e(config('app.name', 'Developer Ruhban')); ?></a>
                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle theme">Theme</button>
            </div>
            <?php if ($flashSuccess) : ?>
                <div class="flash-message flash-message--success"><?php echo e($flashSuccess); ?></div>
            <?php endif; ?>
            <?php if ($flashError) : ?>
                <div class="flash-message flash-message--error"><?php echo e($flashError); ?></div>
            <?php endif; ?>
            <?php echo $content; ?>
        </div>
    </main>
    <script src="<?php echo e(asset('assets/js/app.js')); ?>" defer></script>
</body>
</html>

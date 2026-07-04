<?php

$title = $meta['title'] ?? $siteName ?? config('app.name', 'Developer Ruhban');
$description = $meta['description'] ?? 'Developer Ruhban is a scalable public developer knowledge platform.';
$canonical = $meta['canonical'] ?? url(request()->path());
$schemaType = $meta['schemaType'] ?? 'WebSite';
$robots = $meta['robots'] ?? 'index, follow';
$theme = $_COOKIE['theme'] ?? 'system';
$breadcrumbs = isset($breadcrumbs) && is_array($breadcrumbs) ? $breadcrumbs : array();
$currentPath = trim(request()->path(), '/');
$flashSuccess = app()->session()->pullFlash('success');
$flashError = app()->session()->pullFlash('error');
$navigation = array(
    array('label' => 'Home', 'href' => url('/')),
    array('label' => 'Search', 'href' => url('/search')),
    array('label' => 'Archive', 'href' => url('/archive')),
    array('label' => 'About', 'href' => url('/about')),
    array('label' => 'Contact', 'href' => url('/contact')),
    array('label' => 'Privacy', 'href' => url('/privacy-policy')),
    array('label' => 'Terms', 'href' => url('/terms-and-conditions')),
);

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
    <meta property="og:site_name" content="<?php echo e(config('app.name', 'Developer Ruhban')); ?>">
    <meta property="og:title" content="<?php echo e($title); ?>">
    <meta property="og:description" content="<?php echo e($description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e($canonical); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($title); ?>">
    <meta name="twitter:description" content="<?php echo e($description); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <script type="application/ld+json">
    <?php echo json_encode([
        '@context' => 'https://schema.org',
        '@type' => $schemaType,
        'name' => config('app.name', 'Developer Ruhban'),
        'url' => $canonical,
        'description' => $description,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    </script>
</head>
<body>
<a class="skip-link" href="#content">Skip to content</a>
<div class="reading-progress" data-reading-progress></div>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="<?php echo e(url('/')); ?>"><?php echo e(config('app.name', 'Developer Ruhban')); ?></a>
        <nav class="site-nav" aria-label="Primary">
            <?php foreach ($navigation as $item) : ?>
                <a class="site-nav__link<?php echo trim(parse_url($item['href'], PHP_URL_PATH), '/') === $currentPath ? ' site-nav__link--active' : ''; ?>" href="<?php echo e($item['href']); ?>"><?php echo e($item['label']); ?></a>
            <?php endforeach; ?>
        </nav>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle theme">Theme</button>
    </div>
</header>
<?php if ($breadcrumbs !== array()) : ?>
    <div class="container breadcrumbs-wrap">
        <nav class="breadcrumbs" aria-label="Breadcrumbs">
            <?php foreach ($breadcrumbs as $index => $crumb) : ?>
                <?php if ($index > 0) : ?><span class="breadcrumbs__separator">/</span><?php endif; ?>
                <?php if (!empty($crumb['url']) && $index < count($breadcrumbs) - 1) : ?>
                    <a href="<?php echo e($crumb['url']); ?>"><?php echo e($crumb['label']); ?></a>
                <?php else : ?>
                    <span><?php echo e($crumb['label']); ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>
<?php endif; ?>
<main id="content" class="site-main">
    <?php if ($flashSuccess) : ?><div class="container"><div class="flash-message flash-message--success"><?php echo e($flashSuccess); ?></div></div><?php endif; ?>
    <?php if ($flashError) : ?><div class="container"><div class="flash-message flash-message--error"><?php echo e($flashError); ?></div></div><?php endif; ?>
    <?php echo $content; ?>
</main>
<footer class="site-footer">
    <div class="container">
        <div class="site-footer__grid">
            <div>
                <a class="brand" href="<?php echo e(url('/')); ?>"><?php echo e(config('app.name', 'Developer Ruhban')); ?></a>
                <p class="site-footer__text">Content-first developer publishing with SEO, speed, and reusable architecture.</p>
            </div>
            <form class="newsletter-form" method="post" action="<?php echo e(url('/newsletter/subscribe')); ?>">
                <?php echo csrf_field(); ?>
                <p class="eyebrow">Newsletter</p>
                <div class="newsletter-form__row">
                    <input type="email" name="email" placeholder="Email address" required>
                    <button class="btn btn-primary" type="submit">Subscribe</button>
                </div>
            </form>
            <div class="site-footer__links">
                <a href="<?php echo e(url('/search')); ?>">Search</a>
                <a href="<?php echo e(url('/archive')); ?>">Archive</a>
                <a href="<?php echo e(url('/about')); ?>">About</a>
                <a href="<?php echo e(url('/contact')); ?>">Contact</a>
                <a href="<?php echo e(url('/privacy-policy')); ?>">Privacy</a>
                <a href="<?php echo e(url('/terms-and-conditions')); ?>">Terms</a>
            </div>
        </div>
        <p class="site-footer__copyright">&copy; <?php echo date('Y'); ?> <?php echo e(config('app.name', 'Developer Ruhban')); ?></p>
    </div>
</footer>
<script src="<?php echo e(asset('assets/js/app.js')); ?>" defer></script>
</body>
</html>

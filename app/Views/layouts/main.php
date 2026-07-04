<?php

$title = $meta['title'] ?? $siteName ?? config('app.name', 'Developer Ruhban');
$description = $meta['description'] ?? 'Developer Ruhban is a scalable public developer knowledge platform.';
$canonical = $meta['canonical'] ?? url(request()->path());
$schemaType = $meta['schemaType'] ?? 'WebSite';
$robots = $meta['robots'] ?? 'index, follow';
$ogTitle = $meta['ogTitle'] ?? $title;
$ogDescription = $meta['ogDescription'] ?? $description;
$ogImage = $meta['ogImage'] ?? asset('assets/images/seo-card.svg');
$ogType = $meta['ogType'] ?? ($schemaType === 'Article' || $schemaType === 'BlogPosting' ? 'article' : 'website');
$twitterCard = $meta['twitterCard'] ?? 'summary_large_image';
$twitterCreator = $meta['twitterCreator'] ?? '@developerruhban';
$generator = $meta['generator'] ?? 'Developer Ruhban CMS';
$theme = $_COOKIE['theme'] ?? 'system';
$breadcrumbs = isset($breadcrumbs) && is_array($breadcrumbs) ? $breadcrumbs : array();
$schemaData = isset($meta['schema']) && is_array($meta['schema']) ? $meta['schema'] : array();
$currentPath = trim(request()->path(), '/');
$flashSuccess = app()->session()->pullFlash('success');
$flashError = app()->session()->pullFlash('error');
$navigation = array(
    array('label' => 'Home', 'href' => url('/')),
    array('label' => 'Archive', 'href' => url('/archive')),
    array('label' => 'Search', 'href' => url('/search')),
    array('label' => 'About', 'href' => url('/about')),
    array('label' => 'Contact', 'href' => url('/contact')),
);

?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo e($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?></title>
    <meta name="generator" content="<?php echo e($generator); ?>">
    <meta name="description" content="<?php echo e($description); ?>">
    <meta name="robots" content="<?php echo e($robots); ?>">
    <link rel="canonical" href="<?php echo e($canonical); ?>">
    <meta property="og:site_name" content="<?php echo e(config('app.name', 'Developer Ruhban')); ?>">
    <meta property="og:title" content="<?php echo e($ogTitle); ?>">
    <meta property="og:description" content="<?php echo e($ogDescription); ?>">
    <meta property="og:type" content="<?php echo e($ogType); ?>">
    <meta property="og:url" content="<?php echo e($canonical); ?>">
    <meta property="og:image" content="<?php echo e($ogImage); ?>">
    <meta name="twitter:card" content="<?php echo e($twitterCard); ?>">
    <meta name="twitter:title" content="<?php echo e($ogTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($ogDescription); ?>">
    <meta name="twitter:image" content="<?php echo e($ogImage); ?>">
    <meta name="twitter:creator" content="<?php echo e($twitterCreator); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <link rel="alternate" type="application/rss+xml" title="RSS Feed" href="<?php echo e(url('/feed.xml')); ?>">
    <?php
    $defaultSchemas = array();

    if ($schemaData === array()) {
        $defaultSchemas[] = array(
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'name' => $title,
            'url' => $canonical,
            'description' => $description,
        );

        if ($schemaType === 'WebSite' || $schemaType === 'WebPage') {
            $defaultSchemas[] = array(
                '@context' => 'https://schema.org',
                '@type' => 'SearchAction',
                'target' => url('/search?q={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            );
        }
    }

    if ($breadcrumbs !== array()) {
        $breadcrumbSchema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(),
        );

        foreach ($breadcrumbs as $index => $crumb) {
            $breadcrumbSchema['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['label'],
                'item' => !empty($crumb['url']) ? $crumb['url'] : $canonical,
            );
        }

        $defaultSchemas[] = $breadcrumbSchema;
    }

    $schemas = array_merge($defaultSchemas, $schemaData);

    foreach ($schemas as $schemaItem) :
        if (!is_array($schemaItem)) {
            continue;
        }
    ?>
    <script type="application/ld+json"><?php echo json_encode($schemaItem, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    <?php endforeach; ?>
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
        <div class="site-header__actions">
            <a class="btn btn-secondary btn-sm" href="<?php echo e(url('/search')); ?>">Search</a>
            <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle theme">Theme</button>
        </div>
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
                <p class="site-footer__text">Content-first developer publishing with SEO, speed, structured data, and reusable architecture.</p>
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

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
$dbDefaultTheme = (new \App\Repositories\SettingRepository(app()->database()->connection()))->value('theme.default', 'light');
$theme = $_COOKIE['theme'] ?? $dbDefaultTheme;
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
    array('label' => 'Pricing', 'href' => url('/pricing')),
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/core.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/components.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/public.css')); ?>">
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
        <button class="site-header__toggle" type="button" aria-label="Toggle navigation" onclick="document.querySelector('.site-nav').classList.toggle('show')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
        <nav class="site-nav" aria-label="Primary">
            <a class="site-nav__link" href="<?php echo e(url('/lab')); ?>">Lab</a>
            <?php foreach ($navigation as $item) : ?>
                <a class="site-nav__link<?php echo trim(parse_url($item['href'], PHP_URL_PATH), '/') === $currentPath ? ' site-nav__link--active' : ''; ?>" href="<?php echo e($item['href']); ?>"><?php echo e($item['label']); ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="site-header__actions">
            <?php 
            $sessionUser = app()->session()->get(config('auth.session_key', 'auth_user'));
            if ($sessionUser) : 
                $userInitials = '';
                $nameParts = explode(' ', trim($sessionUser['name'] ?? ''));
                foreach ($nameParts as $part) {
                    $userInitials .= strtoupper(substr($part, 0, 1));
                }
                $userInitials = substr($userInitials, 0, 2);
            ?>
                <div class="dropdown d-inline-block">
                    <button class="btn btn-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($sessionUser['avatar'])) : ?>
                            <img src="<?php echo e($sessionUser['avatar']); ?>" alt="" style="width:20px; height:20px; border-radius:50%; object-fit:cover;">
                        <?php else : ?>
                            <span class="badge bg-primary text-white rounded-circle" style="width:20px; height:20px; font-size:10px; display:inline-flex; align-items:center; justify-content:center;"><?php echo e($userInitials); ?></span>
                        <?php endif; ?>
                        <?php echo e(explode(' ', trim($sessionUser['name'] ?? ''))[0]); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenuDropdown">
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/dashboard')); ?>">🖥️ Dashboard</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/profile')); ?>">👤 Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/collections')); ?>">📂 My Collections</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/bookmarks')); ?>">🔖 My Bookmarks</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/downloads')); ?>">⬇️ Downloads</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/notifications')); ?>">🔔 Notifications</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/membership')); ?>">⭐ Membership</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/pricing')); ?>">💳 Pricing</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(url('/account/settings')); ?>">⚙️ Settings</a></li>
                        <?php 
                        $allowedRoles = (array) config('auth.allowed_roles', array('admin', 'editor', 'author'));
                        if (in_array($sessionUser['role'] ?? '', $allowedRoles, true)) : 
                        ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-primary" href="<?php echo e(url('/admin')); ?>">🛠️ Admin CMS</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo e(url('/logout')); ?>">🚪 Logout</a></li>
                    </ul>
                </div>
            <?php else : ?>
                <a class="btn btn-secondary btn-sm" href="<?php echo e(url('/login')); ?>">Sign in</a>
                <a class="btn btn-primary btn-sm" href="<?php echo e(url('/signup')); ?>">Sign up</a>
            <?php endif; ?>
            <a class="btn btn-secondary btn-sm" href="<?php echo e(url('/search')); ?>">Search</a>
            <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle theme">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            </button>
        </div>
    </div>
</header>
<?php if ($breadcrumbs !== array()) : ?>
    <div class="container breadcrumbs-wrap">
        <nav class="breadcrumbs" aria-label="Breadcrumbs">
            <?php foreach ($breadcrumbs as $index => $crumb) : ?>
                <?php if ($index > 0) : ?><span class="breadcrumbs__separator">&nbsp;&middot;&nbsp;</span><?php endif; ?>
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
    <?php if ($flashSuccess) : ?><div class="container mb-4"><div class="flash-message alert alert-success"><?php echo e($flashSuccess); ?></div></div><?php endif; ?>
    <?php if ($flashError) : ?><div class="container mb-4"><div class="flash-message alert alert-danger"><?php echo e($flashError); ?></div></div><?php endif; ?>
    <?php echo $content; ?>
</main>
<footer class="site-footer">
    <div class="container">
        <div class="site-footer__grid">
            <div>
                <a class="brand" href="<?php echo e(url('/')); ?>"><?php echo e(config('app.name', 'Developer Ruhban')); ?></a>
                <p class="site-footer__text mt-3">Content-first developer publishing with SEO, speed, structured data, and reusable architecture.</p>
            </div>
            <div>
                <p class="eyebrow">Explore</p>
                <div class="site-footer__links d-flex flex-column gap-2 mt-2">
                    <a href="<?php echo e(url('/search')); ?>">Search</a>
                    <a href="<?php echo e(url('/archive')); ?>">Archive</a>
                    <a href="<?php echo e(url('/about')); ?>">About</a>
                    <a href="<?php echo e(url('/pricing')); ?>">Pricing</a>
                    <a href="<?php echo e(url('/contact')); ?>">Contact</a>
                </div>
            </div>
            <form class="newsletter-form" method="post" action="<?php echo e(url('/newsletter/subscribe')); ?>">
                <?php echo csrf_field(); ?>
                <p class="eyebrow">Newsletter</p>
                <div class="newsletter-form__row d-flex gap-2 mt-2">
                    <input type="email" name="email" class="form-control" placeholder="Email address" required>
                    <button class="btn btn-primary" type="submit">Subscribe</button>
                </div>
            </form>
        </div>
        <p class="site-footer__copyright">&copy; <?php echo date('Y'); ?> <?php echo e(config('app.name', 'Developer Ruhban')); ?></p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?php echo e(asset('assets/js/app.js')); ?>" defer></script>
</body>
</html>

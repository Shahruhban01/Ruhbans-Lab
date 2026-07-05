<?php

$title = $meta['title'] ?? 'Member Portal';
$description = $meta['description'] ?? 'Member Portal for Developer Ruhban.';
$canonical = $meta['canonical'] ?? url(request()->path());
$robots = $meta['robots'] ?? 'noindex, nofollow';
$dbDefaultTheme = (new \App\Repositories\SettingRepository(app()->database()->connection()))->value('theme.default', 'light');
$theme = $_COOKIE['theme'] ?? $dbDefaultTheme;
$currentUser = app()->session()->get(config('auth.session_key', 'auth_user'));
$flashSuccess = app()->session()->pullFlash('success');
$flashError = app()->session()->pullFlash('error');
$currentPath = trim(request()->path(), '/');

$memberMenu = array(
    array('label' => 'Dashboard', 'href' => url('/account/dashboard'), 'icon' => '◉'),
    array('label' => 'Profile', 'href' => url('/account/profile'), 'icon' => '👤'),
    array('label' => 'Settings', 'href' => url('/account/settings'), 'icon' => '✏️'),
    array('label' => 'Security', 'href' => url('/account/security'), 'icon' => '🔒'),
    array('label' => 'Bookmarks', 'href' => url('/account/bookmarks'), 'icon' => '🔖'),
    array('label' => 'Collections', 'href' => url('/account/collections'), 'icon' => '📂'),
    array('label' => 'Downloads', 'href' => url('/account/downloads'), 'icon' => '⬇️'),
    array('label' => 'My Purchases', 'href' => url('/account/purchases'), 'icon' => '💰'),
    array('label' => 'Licenses & Keys', 'href' => url('/account/licenses'), 'icon' => '🔑'),
    array('label' => 'Billing & Invoices', 'href' => url('/account/billing'), 'icon' => '📄'),
    array('label' => 'Reading History', 'href' => url('/account/history'), 'icon' => '📖'),
    array('label' => 'Notifications', 'href' => url('/account/notifications'), 'icon' => '🔔'),
    array('label' => 'Membership Plan', 'href' => url('/account/membership'), 'icon' => '⭐'),
    array('label' => 'Pricing & Plans', 'href' => url('/account/pricing'), 'icon' => '💳'),
    array('label' => 'Support Help', 'href' => url('/account/support'), 'icon' => '❓'),
    array('label' => 'Activity Feed', 'href' => url('/account/activity'), 'icon' => '⚡'),
);

?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo e($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?> - Member Portal</title>
    <meta name="description" content="<?php echo e($description); ?>">
    <meta name="robots" content="<?php echo e($robots); ?>">
    <link rel="canonical" href="<?php echo e($canonical); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/core.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/components.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ui/public.css')); ?>">
    <style>
        .member-portal-body {
            background-color: var(--color-background, #f9fafb);
            min-height: 100vh;
        }
        .member-portal-shell {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }
        .member-sidebar {
            background-color: var(--color-surface, #ffffff);
            border-right: 1px solid var(--color-border, #e5e7eb);
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
        }
        .member-sidebar__title {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: var(--color-primary, #4f46e5);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .member-sidebar__menu {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .member-sidebar__link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            border-radius: var(--radius, 6px);
            font-size: 0.9rem;
            color: var(--color-text-muted, #4b5563);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .member-sidebar__link:hover {
            background-color: color-mix(in srgb, var(--color-primary) 8%, transparent);
            color: var(--color-text, #111827);
            text-decoration: none;
        }
        .member-sidebar__link--active {
            background-color: color-mix(in srgb, var(--color-primary) 12%, transparent);
            color: var(--color-primary, #4f46e5);
            font-weight: 600;
        }
        .member-main {
            display: flex;
            flex-direction: column;
        }
        .member-navbar {
            background-color: var(--color-surface, #ffffff);
            border-bottom: 1px solid var(--color-border, #e5e7eb);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .member-content-area {
            padding: 2.5rem 2rem;
            flex: 1;
        }
        @media (max-width: 991px) {
            .member-portal-shell {
                grid-template-columns: 1fr;
            }
            .member-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body class="member-portal-body">
<div class="member-portal-shell">
    <!-- Sidebar -->
    <aside class="member-sidebar">
        <div class="member-sidebar__title">
            <span>🧪</span> Ruhban's Lab
        </div>
        <nav class="member-sidebar__menu">
            <?php foreach ($memberMenu as $item) : 
                $isActive = trim(parse_url($item['href'], PHP_URL_PATH), '/') === $currentPath;
            ?>
                <a class="member-sidebar__link<?php echo $isActive ? ' member-sidebar__link--active' : ''; ?>" href="<?php echo e($item['href']); ?>">
                    <span><?php echo e($item['icon']); ?></span>
                    <?php echo e($item['label']); ?>
                </a>
            <?php endforeach; ?>
            <hr class="my-4">
            <a class="member-sidebar__link" href="<?php echo e(url('/')); ?>">
                <span>🏠</span> Return to Site
            </a>
            <a class="member-sidebar__link text-danger" href="<?php echo e(url('/logout')); ?>">
                <span>🚪</span> Sign Out
            </a>
        </nav>
    </aside>

    <!-- Main Section -->
    <div class="member-main">
        <!-- Top Navbar -->
        <header class="member-navbar">
            <div class="d-flex align-items-center gap-3">
                <span class="fw-bold">Member Workspace</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Logged in as <?php echo e($currentUser['name'] ?? 'Member'); ?></span>
                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle theme">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </button>
            </div>
        </header>

        <!-- Content Area -->
        <main class="member-content-area" id="content">
            <?php if ($flashSuccess) : ?><div class="alert alert-success mb-4"><?php echo e($flashSuccess); ?></div><?php endif; ?>
            <?php if ($flashError) : ?><div class="alert alert-danger mb-4"><?php echo e($flashError); ?></div><?php endif; ?>
            <?php echo $content; ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?php echo e(asset('assets/js/app.js')); ?>" defer></script>
</body>
</html>

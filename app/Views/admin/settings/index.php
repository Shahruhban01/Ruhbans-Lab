<?php
$settings = isset($settings) && is_array($settings) ? $settings : array();
$general = isset($settings['general']) ? $settings['general'] : array();
$seo = isset($settings['seo']) ? $settings['seo'] : array();
$smtp = isset($settings['smtp']) ? $settings['smtp'] : array();
$branding = isset($settings['branding']) ? $settings['branding'] : array();
$theme = isset($settings['theme']) ? $settings['theme'] : array();
$social = isset($settings['social']) ? $settings['social'] : array();
$system = isset($settings['system']) ? $settings['system'] : array();
$backupFiles = isset($backupFiles) && is_array($backupFiles) ? $backupFiles : array();
$systemInfo = isset($systemInfo) && is_array($systemInfo) ? $systemInfo : array();
$maintenanceMode = !empty($maintenanceMode);
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Administration</p>
            <h2>Settings</h2>
        </div>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>General settings</h3>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/save/general')); ?>">
                <?php echo csrf_field(); ?>
                <label><span>App name</span><input type="text" name="general__app_name" value="<?php echo e(isset($general['general.app_name']) ? $general['general.app_name'] : ''); ?>"></label>
                <label><span>App URL</span><input type="url" name="general__app_url" value="<?php echo e(isset($general['general.app_url']) ? $general['general.app_url'] : ''); ?>"></label>
                <label><span>Timezone</span><input type="text" name="general__app_timezone" value="<?php echo e(isset($general['general.app_timezone']) ? $general['general.app_timezone'] : 'UTC'); ?>"></label>
                <label><span>Locale</span><input type="text" name="general__app_locale" value="<?php echo e(isset($general['general.app_locale']) ? $general['general.app_locale'] : 'en'); ?>"></label>
                <button class="btn btn-primary" type="submit">Save general settings</button>
            </form>
        </section>

        <section class="panel card-surface">
            <h3>SEO settings</h3>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/save/seo')); ?>">
                <?php echo csrf_field(); ?>
                <label><span>Default meta title</span><input type="text" name="seo__meta_title" value="<?php echo e(isset($seo['seo.meta_title']) ? $seo['seo.meta_title'] : ''); ?>"></label>
                <label><span>Default meta description</span><textarea name="seo__meta_description" rows="3"><?php echo e(isset($seo['seo.meta_description']) ? $seo['seo.meta_description'] : ''); ?></textarea></label>
                <label><span>Default robots</span><input type="text" name="seo__default_robots" value="<?php echo e(isset($seo['seo.default_robots']) ? $seo['seo.default_robots'] : 'index, follow'); ?>"></label>
                <label><span>Twitter creator</span><input type="text" name="seo__twitter_creator" value="<?php echo e(isset($seo['seo.twitter_creator']) ? $seo['seo.twitter_creator'] : ''); ?>"></label>
                <label><span>OG image URL</span><input type="text" name="seo__og_image" value="<?php echo e(isset($seo['seo.og_image']) ? $seo['seo.og_image'] : ''); ?>"></label>
                <button class="btn btn-primary" type="submit">Save SEO settings</button>
            </form>
        </section>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>SMTP settings</h3>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/save/smtp')); ?>">
                <?php echo csrf_field(); ?>
                <label><span>Host</span><input type="text" name="smtp__host" value="<?php echo e(isset($smtp['smtp.host']) ? $smtp['smtp.host'] : ''); ?>"></label>
                <label><span>Port</span><input type="number" name="smtp__port" value="<?php echo e(isset($smtp['smtp.port']) ? $smtp['smtp.port'] : '587'); ?>"></label>
                <label><span>Username</span><input type="text" name="smtp__username" value="<?php echo e(isset($smtp['smtp.username']) ? $smtp['smtp.username'] : ''); ?>"></label>
                <label><span>Password</span><input type="password" name="smtp__password" value="<?php echo e(isset($smtp['smtp.password']) ? $smtp['smtp.password'] : ''); ?>"></label>
                <label><span>Encryption</span><input type="text" name="smtp__encryption" value="<?php echo e(isset($smtp['smtp.encryption']) ? $smtp['smtp.encryption'] : 'tls'); ?>"></label>
                <label><span>From email</span><input type="email" name="smtp__from_email" value="<?php echo e(isset($smtp['smtp.from_email']) ? $smtp['smtp.from_email'] : ''); ?>"></label>
                <label><span>From name</span><input type="text" name="smtp__from_name" value="<?php echo e(isset($smtp['smtp.from_name']) ? $smtp['smtp.from_name'] : ''); ?>"></label>
                <button class="btn btn-primary" type="submit">Save SMTP settings</button>
            </form>
        </section>

        <section class="panel card-surface">
            <h3>Branding and theme</h3>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/save/branding')); ?>">
                <?php echo csrf_field(); ?>
                <label><span>Logo URL</span><input type="text" name="branding__logo_url" value="<?php echo e(isset($branding['branding.logo_url']) ? $branding['branding.logo_url'] : ''); ?>"></label>
                <label><span>Favicon URL</span><input type="text" name="branding__favicon_url" value="<?php echo e(isset($branding['branding.favicon_url']) ? $branding['branding.favicon_url'] : ''); ?>"></label>
                <label><span>Tagline</span><input type="text" name="branding__tagline" value="<?php echo e(isset($branding['branding.tagline']) ? $branding['branding.tagline'] : ''); ?>"></label>
                <button class="btn btn-primary" type="submit">Save branding</button>
            </form>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/save/theme')); ?>">
                <?php echo csrf_field(); ?>
                <label>
                    <span>Default theme</span>
                    <select name="theme__default" class="form-select">
                        <option value="light"<?php echo (isset($theme['theme.default']) ? $theme['theme.default'] : 'light') === 'light' ? ' selected' : ''; ?>>Light</option>
                        <option value="dark"<?php echo (isset($theme['theme.default']) ? $theme['theme.default'] : 'light') === 'dark' ? ' selected' : ''; ?>>Dark</option>
                        <option value="system"<?php echo (isset($theme['theme.default']) ? $theme['theme.default'] : 'light') === 'system' ? ' selected' : ''; ?>>System Default</option>
                    </select>
                </label>
                <label><span>Accent color</span><input type="text" name="theme__accent_color" value="<?php echo e(isset($theme['theme.accent_color']) ? $theme['theme.accent_color'] : '#f97316'); ?>"></label>
                <label><span>Surface style</span><input type="text" name="theme__surface_style" value="<?php echo e(isset($theme['theme.surface_style']) ? $theme['theme.surface_style'] : 'soft'); ?>"></label>
                <button class="btn btn-primary" type="submit">Save theme settings</button>
            </form>
        </section>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Social links</h3>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/save/social')); ?>">
                <?php echo csrf_field(); ?>
                <label><span>X</span><input type="url" name="social__x" value="<?php echo e(isset($social['social.x']) ? $social['social.x'] : ''); ?>"></label>
                <label><span>LinkedIn</span><input type="url" name="social__linkedin" value="<?php echo e(isset($social['social.linkedin']) ? $social['social.linkedin'] : ''); ?>"></label>
                <label><span>GitHub</span><input type="url" name="social__github" value="<?php echo e(isset($social['social.github']) ? $social['social.github'] : ''); ?>"></label>
                <label><span>YouTube</span><input type="url" name="social__youtube" value="<?php echo e(isset($social['social.youtube']) ? $social['social.youtube'] : ''); ?>"></label>
                <label><span>Facebook</span><input type="url" name="social__facebook" value="<?php echo e(isset($social['social.facebook']) ? $social['social.facebook'] : ''); ?>"></label>
                <label><span>Telegram</span><input type="url" name="social__telegram" value="<?php echo e(isset($social['social.telegram']) ? $social['social.telegram'] : ''); ?>"></label>
                <button class="btn btn-primary" type="submit">Save social links</button>
            </form>
        </section>

        <section class="panel card-surface">
            <h3>System controls</h3>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/maintenance')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="enabled" value="<?php echo e($maintenanceMode ? '0' : '1'); ?>">
                <input type="hidden" name="system__cache_ttl_minutes" value="<?php echo e(isset($system['system.cache_ttl_minutes']) ? $system['system.cache_ttl_minutes'] : 60); ?>">
                <p>Maintenance mode is currently <strong><?php echo e($maintenanceMode ? 'enabled' : 'disabled'); ?></strong>.</p>
                <button class="btn btn-primary" type="submit"><?php echo e($maintenanceMode ? 'Disable maintenance mode' : 'Enable maintenance mode'); ?></button>
            </form>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/cache/clear')); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-secondary" type="submit">Clear cache</button>
            </form>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/backup')); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-secondary" type="submit">Create backup snapshot</button>
            </form>
            <form class="auth-form" method="post" action="<?php echo e(url('/admin/settings/restore')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <label><span>Restore backup (JSON)</span><input type="file" name="backup_file" accept="application/json" required></label>
                <button class="btn btn-secondary" type="submit">Restore backup</button>
            </form>
        </section>
    </div>

    <div class="grid-two">
        <section class="panel card-surface">
            <h3>Available backups</h3>
            <div class="tree-list">
                <?php if ($backupFiles === array()) : ?>
                    <div class="empty-inline">No backups found in storage/backups.</div>
                <?php endif; ?>
                <?php foreach ($backupFiles as $file) : ?>
                    <div class="tree-row">
                        <span><?php echo e($file['name']); ?></span>
                        <span><?php echo e($file['modified_at']); ?> · <?php echo e($file['size']); ?> bytes</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel card-surface">
            <h3>System information</h3>
            <div class="tree-list">
                <?php foreach ($systemInfo as $key => $value) : ?>
                    <div class="tree-row">
                        <span><?php echo e(str_replace('_', ' ', ucfirst($key))); ?></span>
                        <span><?php echo e($value); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</section>
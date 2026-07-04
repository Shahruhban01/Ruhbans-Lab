<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Repositories\SettingRepository;

final class SettingsService
{
    private SettingRepository $settings;

    public function __construct(SettingRepository $settings)
    {
        $this->settings = $settings;
    }

    public function defaults(): array
    {
        return array(
            'general' => array(
                'general.app_name' => config('app.name', 'Developer Ruhban'),
                'general.app_url' => config('app.url', 'http://localhost'),
                'general.app_timezone' => config('app.timezone', 'UTC'),
                'general.app_locale' => config('app.locale', 'en'),
            ),
            'seo' => array(
                'seo.meta_title' => 'Developer Ruhban',
                'seo.meta_description' => 'Content-first developer knowledge platform.',
                'seo.default_robots' => 'index, follow',
                'seo.twitter_creator' => '@developerruhban',
                'seo.og_image' => asset('assets/images/seo-card.svg'),
            ),
            'smtp' => array(
                'smtp.host' => '',
                'smtp.port' => '587',
                'smtp.username' => '',
                'smtp.password' => '',
                'smtp.encryption' => 'tls',
                'smtp.from_email' => '',
                'smtp.from_name' => config('app.name', 'Developer Ruhban'),
            ),
            'branding' => array(
                'branding.logo_url' => '',
                'branding.favicon_url' => '',
                'branding.tagline' => 'Build with clarity.',
            ),
            'theme' => array(
                'theme.default' => 'system',
                'theme.accent_color' => '#f97316',
                'theme.surface_style' => 'soft',
            ),
            'social' => array(
                'social.x' => '',
                'social.linkedin' => '',
                'social.github' => '',
                'social.youtube' => '',
                'social.facebook' => '',
                'social.telegram' => '',
            ),
            'system' => array(
                'system.maintenance_mode' => false,
                'system.cache_ttl_minutes' => 60,
            ),
        );
    }

    public function all(): array
    {
        $defaults = $this->defaults();
        $stored = $this->settings->allGrouped();

        foreach ($defaults as $group => $values) {
            if (!isset($stored[$group])) {
                $stored[$group] = array();
            }

            $stored[$group] = array_merge($values, $stored[$group]);
        }

        return $stored;
    }

    public function saveGroup(string $group, array $input, ?int $userId = null): void
    {
        $defaults = $this->defaults();

        if (!isset($defaults[$group])) {
            throw new \InvalidArgumentException('Invalid settings group.');
        }

        $allowed = array_keys($defaults[$group]);
        $payload = array();

        foreach ($allowed as $key) {
            $field = str_replace('.', '__', $key);
            $defaultValue = $defaults[$group][$key];

            if (is_bool($defaultValue)) {
                $payload[$key] = isset($input[$field]) && (string) $input[$field] === '1';
                continue;
            }

            if (is_int($defaultValue)) {
                $payload[$key] = max(0, (int) ($input[$field] ?? $defaultValue));
                continue;
            }

            $payload[$key] = trim((string) ($input[$field] ?? $defaultValue));
        }

        $this->settings->saveGroup($group, $payload, $userId);
    }
}
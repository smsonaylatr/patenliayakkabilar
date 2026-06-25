<?php

declare(strict_types=1);


function yonetim_brand_reset_cache(): void
{
    unset($GLOBALS['yonetim_brand_cache']);
}


function yonetim_brand(): array
{
    if (isset($GLOBALS['yonetim_brand_cache']) && is_array($GLOBALS['yonetim_brand_cache'])) {
        return $GLOBALS['yonetim_brand_cache'];
    }

    $siteName = 'Laykids';
    $panel = [
        'name' => '',
        'logo_sm' => '',
        'logo_light' => '',
        'logo_dark' => '',
        'favicon' => '',
        'footer_line' => '',
    ];

    $pdo = ded_pdo();
    if ($pdo && ded_db_ready()) {
        require_once dirname(__DIR__, 2) . '/lib/katalogdepo.php';
        require_once dirname(__DIR__, 2) . '/lib/vitrinlayout.php';
        $site = ded_site_row($pdo);
        $siteName = trim((string) ($site['name'] ?? '')) ?: 'Laykids';
        $L = ded_vitrin_layout_load($pdo);
        $stored = $L['panel'] ?? [];
        if (is_array($stored)) {
            $panel = array_merge($panel, $stored);
        }
    }

    $displayName = trim((string) ($panel['name'] ?? '')) ?: $siteName;

    $brand = [
        'name' => $displayName,
        'logo_sm' => trim((string) ($panel['logo_sm'] ?? '')),
        'logo_light' => trim((string) ($panel['logo_light'] ?? '')),
        'logo_dark' => trim((string) ($panel['logo_dark'] ?? '')),
        'favicon' => trim((string) ($panel['favicon'] ?? '')),
        'footer_line' => trim((string) ($panel['footer_line'] ?? '')),
        'logo_sm_url' => yonetim_brand_asset_url(trim((string) ($panel['logo_sm'] ?? '')), 'assets/images/logo-sm.png'),
        'logo_light_url' => yonetim_brand_asset_url(trim((string) ($panel['logo_light'] ?? '')), 'assets/images/logo-light.png'),
        'logo_dark_url' => yonetim_brand_asset_url(trim((string) ($panel['logo_dark'] ?? '')), 'assets/images/logo-dark.png'),
        'favicon_url' => yonetim_brand_asset_url(trim((string) ($panel['favicon'] ?? '')), 'assets/images/favicon.ico'),
    ];

    $GLOBALS['yonetim_brand_cache'] = $brand;

    return $brand;
}

function yonetim_brand_name(): string
{
    return yonetim_brand()['name'];
}

function yonetim_brand_footer_line(): string
{
    $brand = yonetim_brand();
    if ($brand['footer_line'] !== '') {
        return $brand['footer_line'];
    }

    return '© ' . (int) date('Y') . ' ' . $brand['name'];
}

function yonetim_brand_asset_url(string $path, string $defaultRizzRelative): string
{
    $path = trim($path);
    if ($path === '') {
        return yonetim_rizz_asset($defaultRizzRelative);
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (str_starts_with($path, '/')) {
        return $path;
    }
    if (str_starts_with($path, 'assets/rizz/')) {
        return yonetim_rizz_asset(substr($path, strlen('assets/rizz/')));
    }
    if (str_starts_with($path, 'rizz/')) {
        return yonetim_rizz_asset(substr($path, strlen('rizz/')));
    }
    if (str_starts_with($path, 'assets/')) {
        return yonetim_panel_asset(substr($path, strlen('assets/')));
    }

    require_once dirname(__DIR__, 2) . '/lib/vitrin.php';

    return ded_storefront_image_src($path, 'index');
}

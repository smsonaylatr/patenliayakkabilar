<?php declare(strict_types=1);

function yonetim_rizz_asset(string $relative): string
{
    $relative = ltrim(str_replace('\\', '/', $relative), '/');
    if (str_starts_with($relative, 'assets/')) {
        $relative = substr($relative, strlen('assets/'));
    }

    return 'assets/rizz/' . $relative;
}

function yonetim_theme_storage_key(): string
{
    return 'yonetim-bs-theme';
}

function yonetim_theme_restore_script(): void
{
    $key = yonetim_theme_storage_key();
    ?>
<script>
(function () {
  try {
    var t = localStorage.getItem(<?= json_encode($key, JSON_THROW_ON_ERROR) ?>);
    if (t === 'dark' || t === 'light') {
      document.documentElement.setAttribute('data-bs-theme', t);
    }
  } catch (e) {}
})();
</script>
    <?php
}

function yonetim_nav_page_id(): string
{
    static $scriptToPage = null;
    if ($scriptToPage === null) {
        $scriptToPage = [];
        foreach (array_keys(yonetim_route_paths()) as $page) {
            $file = yonetim_route_php_dosyasi($page);
            $base = preg_replace('/\.php$/i', '', $file) ?? $file;
            $scriptToPage[$base] = $page;
        }
    }

    $raw = yonetim_current_page();
    if (isset($scriptToPage[$raw])) {
        return yonetim_route_normalize_page($scriptToPage[$raw]);
    }

    return yonetim_route_normalize_page($raw);
}

function yonetim_nav_item_active(string $pageId): bool
{
    $cur = yonetim_nav_page_id();
    if ($cur === $pageId) {
        return true;
    }

    static $parentOf = [
        'product' => 'products',
        'collection' => 'collections',
        'page' => 'pages',
        'order' => 'orders',
        'customer' => 'customers',
        'coupon' => 'coupons',
    ];

    return ($parentOf[$cur] ?? '') === $pageId;
}

function yonetim_rizz_nav_item(string $href, string $label, string $icon, ?string $pageId = null, bool $sub = false): string
{
    $pid = $pageId ?? yonetim_route_normalize_page($href);
    $active = yonetim_nav_item_active($pid) ? ' active' : '';
    $subCls = $sub ? ' yun-nav-sub-link' : '';

    return '<li class="nav-item">'
        . '<a class="nav-link' . $subCls . $active . '" href="' . ded_h(yonetim_url($href)) . '">'
        . '<i class="' . ded_h($icon) . ' menu-icon"></i>'
        . '<span>' . ded_h($label) . '</span>'
        . '</a></li>';
}

function yonetim_nav_group_active(array $pageIds): bool
{
    $cur = yonetim_nav_page_id();
    if (in_array($cur, $pageIds, true)) {
        return true;
    }

    static $parentOf = [
        'product' => 'products',
        'collection' => 'collections',
        'page' => 'pages',
        'order' => 'orders',
        'customer' => 'customers',
        'coupon' => 'coupons',
    ];
    $parent = $parentOf[$cur] ?? '';

    return $parent !== '' && in_array($parent, $pageIds, true);
}

function yonetim_rizz_nav_group(string $id, string $label, string $icon, array $items): string
{
    if ($items === []) {
        return '';
    }

    $pageIds = [];
    foreach ($items as $item) {
        $pageIds[] = isset($item['page'])
            ? yonetim_route_normalize_page((string) $item['page'])
            : yonetim_route_normalize_page((string) $item['href']);
    }

    $open = yonetim_nav_group_active($pageIds);
    $collapseId = 'yun-nav-' . $id;

    $html = '<li class="nav-item yun-nav-group">'
        . '<a class="nav-link yun-nav-group-toggle' . ($open ? '' : ' collapsed') . '"'
        . ' data-bs-toggle="collapse" href="#' . ded_h($collapseId) . '" role="button"'
        . ' aria-expanded="' . ($open ? 'true' : 'false') . '"'
        . ' aria-controls="' . ded_h($collapseId) . '">'
        . '<i class="' . ded_h($icon) . ' menu-icon"></i>'
        . '<span>' . ded_h($label) . '</span>'
        . '<i class="iconoir-nav-arrow-down yun-nav-chevron ms-auto"></i>'
        . '</a>'
        . '<div class="collapse' . ($open ? ' show' : '') . '" id="' . ded_h($collapseId) . '">'
        . '<ul class="nav flex-column yun-nav-sub mb-1">';

    foreach ($items as $item) {
        $html .= yonetim_rizz_nav_item(
            (string) $item['href'],
            (string) $item['label'],
            (string) $item['icon'],
            isset($item['page']) ? (string) $item['page'] : null,
            true,
        );
    }

    return $html . '</ul></div></li>';
}

function yonetim_render_sidebar_nav(bool $shopOk): void
{
    echo yonetim_rizz_nav_item('dashboard', 'Özet', 'iconoir-home-simple');

    echo yonetim_rizz_nav_group('site', 'Site', 'iconoir-page-star', [
        ['href' => 'vitrin', 'label' => 'Vitrin düzeni', 'icon' => 'iconoir-page-star'],
        ['href' => 'site', 'label' => 'Site & marka', 'icon' => 'iconoir-page'],
        ['href' => 'pages', 'label' => 'Sayfalar', 'icon' => 'iconoir-journal-page'],
    ]);

    $catalogItems = [
        ['href' => 'products', 'label' => 'Ürünler', 'icon' => 'iconoir-cart'],
        ['href' => 'collections', 'label' => 'Koleksiyonlar', 'icon' => 'iconoir-view-grid'],
        ['href' => 'media', 'label' => 'Medya', 'icon' => 'iconoir-media-image'],
    ];
    if ($shopOk) {
        $catalogItems[] = ['href' => 'brands', 'label' => 'Markalar', 'icon' => 'iconoir-tag'];
        $catalogItems[] = ['href' => 'stock', 'label' => 'Stok', 'icon' => 'iconoir-warning-triangle'];
    }
    echo yonetim_rizz_nav_group('catalog', 'Katalog', 'iconoir-cart', $catalogItems);

    if (!$shopOk) {
        return;
    }

    echo yonetim_rizz_nav_group('sales', 'Satış', 'iconoir-shopping-bag', [
        ['href' => 'orders', 'label' => 'Siparişler', 'icon' => 'iconoir-shopping-bag'],
        ['href' => 'customers', 'label' => 'Müşteriler', 'icon' => 'iconoir-group'],
        ['href' => 'returns', 'label' => 'İadeler', 'icon' => 'iconoir-undo'],
        ['href' => 'coupons', 'label' => 'Kuponlar', 'icon' => 'iconoir-percentage-circle'],
        ['href' => 'bestsellers', 'label' => 'Çok satanlar', 'icon' => 'iconoir-stats-up-square'],
    ]);

    echo yonetim_rizz_nav_group('finance', 'Finans', 'iconoir-hand-cash', [
        ['href' => 'accounting', 'label' => 'Muhasebe', 'icon' => 'iconoir-hand-cash'],
        ['href' => 'reports', 'label' => 'Raporlar', 'icon' => 'iconoir-reports'],
    ]);

    echo yonetim_rizz_nav_group('comms', 'İletişim', 'iconoir-send-mail', [
        ['href' => 'notifications', 'label' => 'Bildirimler', 'icon' => 'iconoir-bell'],
        ['href' => 'bulk_messages', 'label' => 'Toplu mesaj', 'icon' => 'iconoir-send-mail'],
        ['href' => 'newsletter', 'label' => 'Bülten', 'icon' => 'iconoir-mail-open'],
        ['href' => 'faq', 'label' => 'SSS', 'icon' => 'iconoir-help-circle'],
        ['href' => 'reviews', 'label' => 'Yorumlar', 'icon' => 'iconoir-star'],
    ]);

    echo yonetim_rizz_nav_item('seo', 'SEO', 'iconoir-google');
    echo yonetim_rizz_nav_item('hosting', 'Sunucu', 'iconoir-server');

    echo yonetim_rizz_nav_group('settings', 'Ayarlar', 'iconoir-settings', [
        ['href' => 'settings', 'label' => 'Genel', 'icon' => 'iconoir-settings', 'page' => 'settings'],
        ['href' => 'settings_checkout', 'label' => 'Ödeme', 'icon' => 'iconoir-credit-card', 'page' => 'settings_checkout'],
        ['href' => 'settings_paytr', 'label' => 'PayTR', 'icon' => 'iconoir-wallet', 'page' => 'settings_paytr'],
        ['href' => 'settings_shipping', 'label' => 'Kargo', 'icon' => 'iconoir-delivery-truck', 'page' => 'settings_shipping'],
        ['href' => 'settings_smtp', 'label' => 'E-posta', 'icon' => 'iconoir-mail', 'page' => 'settings_smtp'],
        ['href' => 'settings_sms', 'label' => 'SMS', 'icon' => 'iconoir-phone', 'page' => 'settings_sms'],
        ['href' => 'settings_notifications', 'label' => 'Bildirimler', 'icon' => 'iconoir-bell-notification', 'page' => 'settings_notifications'],
        ['href' => 'settings_contact', 'label' => 'İletişim', 'icon' => 'iconoir-phone-plus', 'page' => 'settings_contact'],
    ]);
}

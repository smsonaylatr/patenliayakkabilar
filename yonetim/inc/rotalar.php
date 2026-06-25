<?php declare(strict_types=1);

function yonetim_route_paths(): array
{
    return [
        'index' => 'giris',
        'dashboard' => 'ozet',
        'site' => 'site',
        'vitrin' => 'vitrin',
        'products' => 'urunler',
        'product' => 'urun',
        'collections' => 'koleksiyonlar',
        'collection' => 'koleksiyon',
        'pages' => 'sayfalar',
        'page' => 'sayfa',
        'orders' => 'siparisler',
        'order' => 'siparis',
        'accounting' => 'muhasebe',
        'bestsellers' => 'cok-satanlar',
        'coupons' => 'kuponlar',
        'coupon' => 'kupon',
        'notifications' => 'bildirimler',
        'bulk_messages' => 'toplu-mesaj',
        'settings' => 'ayarlar',
        'settings_checkout' => 'ayarlar/odeme',
        'settings_paytr' => 'ayarlar/paytr',
        'settings_shipping' => 'ayarlar/kargo',
        'settings_smtp' => 'ayarlar/eposta',
        'settings_sms' => 'ayarlar/sms',
        'settings_notifications' => 'ayarlar/bildirimler',
        'settings_contact' => 'ayarlar/iletisim',
        'password' => 'sifre',
        'logout' => 'cikis',
        'shop_settings' => 'ayarlar',
        'customers' => 'musteriler',
        'customer' => 'musteri',
        'stock' => 'stok',
        'returns' => 'iadeler',
        'media' => 'medya',
        'reports' => 'raporlar',
        'brands' => 'markalar',
        'order_export' => 'siparisler/disa-aktar',
        'newsletter' => 'bulten',
        'faq' => 'sss',
        'reviews' => 'yorumlar',
        'seo' => 'seo',
        'hosting' => 'hosting',
    ];
}

function yonetim_route_php_dosyasi(string $page): string
{
    $map = [
        'index' => 'giris.php',
        'dashboard' => 'ozet.php',
        'site' => 'site.php',
        'vitrin' => 'vitrin.php',
        'products' => 'urunler.php',
        'product' => 'urun.php',
        'collections' => 'koleksiyonlar.php',
        'collection' => 'koleksiyon.php',
        'pages' => 'sayfalar.php',
        'page' => 'sayfa.php',
        'orders' => 'siparisler.php',
        'order' => 'siparis.php',
        'accounting' => 'muhasebe.php',
        'bestsellers' => 'coksatanlar.php',
        'coupons' => 'kuponlar.php',
        'coupon' => 'kupon.php',
        'notifications' => 'bildirimler.php',
        'bulk_messages' => 'toplumesaj.php',
        'settings' => 'ayarlar.php',
        'settings_checkout' => 'ayarodeme.php',
        'settings_paytr' => 'ayarpaytr.php',
        'settings_shipping' => 'ayarkargo.php',
        'settings_smtp' => 'ayareposta.php',
        'settings_sms' => 'ayarsms.php',
        'settings_notifications' => 'ayarbildirim.php',
        'settings_contact' => 'ayariletisim.php',
        'password' => 'sifre.php',
        'logout' => 'cikis.php',
        'customers' => 'musteriler.php',
        'customer' => 'musteri.php',
        'stock' => 'stok.php',
        'returns' => 'iadeler.php',
        'media' => 'medya.php',
        'reports' => 'raporlar.php',
        'brands' => 'markalar.php',
        'order_export' => 'siparisaktar.php',
        'newsletter' => 'bulten.php',
        'faq' => 'sss.php',
        'reviews' => 'yorumlar.php',
        'seo' => 'seo.php',
        'hosting' => 'hosting.php',
    ];
    $page = yonetim_route_normalize_page($page);

    return $map[$page] ?? $page . '.php';
}

function yonetim_route_reverse(): array
{
    static $rev = null;
    if ($rev !== null) {
        return $rev;
    }
    $rev = [];
    foreach (yonetim_route_paths() as $page => $path) {
        $rev[$path] = $page;
    }

    return $rev;
}

function yonetim_route_legacy_aliases(): array
{
    $paths = yonetim_route_paths();
    $aliases = [];
    foreach ($paths as $page => $path) {
        $aliases[$page] = $page;
        if (str_contains($page, '_')) {
            $aliases[str_replace('_', '-', $page)] = $page;
        }
    }
    $aliases['shop_settings'] = 'settings';

    return $aliases;
}

function yonetim_route_normalize_page(string $page): string
{
    $page = trim($page);
    $page = preg_replace('/\.php$/i', '', $page) ?? $page;
    if ($page === '' || $page === 'index') {
        return 'index';
    }

    $rev = yonetim_route_reverse();
    if (isset($rev[$page])) {
        return $rev[$page];
    }

    $aliases = yonetim_route_legacy_aliases();
    if (isset($aliases[$page])) {
        return $aliases[$page];
    }

    return $page;
}

function yonetim_query_param_map_tr(): array
{
    return [
        'slug' => 'ad',
        'id' => 'kimlik',
        'new' => 'yeni',
        'status' => 'durum',
        'days' => 'gun',
        'esik' => 'esik',
    ];
}

function yonetim_query_param_map_en(): array
{
    return array_flip(yonetim_query_param_map_tr());
}

function yonetim_query_to_tr(array $query): array
{
    $map = yonetim_query_param_map_tr();
    $out = [];
    foreach ($query as $k => $v) {
        $out[$map[$k] ?? $k] = $v;
    }

    return $out;
}

function yonetim_request_normalize_query(): void
{
    $map = yonetim_query_param_map_en();
    foreach ($map as $tr => $en) {
        if (isset($_GET[$tr]) && !isset($_GET[$en])) {
            $_GET[$en] = $_GET[$tr];
        }
    }
    if (isset($_GET['slug']) && !isset($_GET['ad'])) {
        $_GET['ad'] = $_GET['slug'];
    }
}

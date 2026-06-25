<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';
require_once DED_ROOT . '/lib/katalogdepo.php';

function ded_normalize_asset_path(string $url): string
{
    $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $url = preg_replace('#^//+#', 'https://', $url) ?? $url;
    if (preg_match('#^https?://(?:www\.)?laykidsofficial\.com\.tr/(.+)$#i', $url, $m)) {
        return $m[1];
    }
    if (str_starts_with($url, '../')) {
        return ltrim(preg_replace('#^\.\./+#', '', $url) ?? $url, '/');
    }
    return $url;
}

function ded_meta_content(string $html, string $prop): ?string
{
    if (preg_match('/<meta\s+[^>]*property=["\']' . preg_quote($prop, '/') . '["\'][^>]*content=["\']([^"\']*)["\']/i', $html, $m)) {
        return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    if (preg_match('/<meta\s+[^>]*content=["\']([^"\']*)["\'][^>]*property=["\']' . preg_quote($prop, '/') . '["\']/i', $html, $m)) {
        return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return null;
}

function ded_meta_name(string $html, string $name): ?string
{
    if (preg_match('/<meta\s+[^>]*name=["\']' . preg_quote($name, '/') . '["\'][^>]*content=["\']([^"\']*)["\']/i', $html, $m)) {
        return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return null;
}

function ded_title(string $html): ?string
{
    if (preg_match('/<title>([^<]+)<\/title>/i', $html, $m)) {
        return html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return null;
}

function ded_first_json_ld_product(string $html): ?array
{
    if (!preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $blocks)) {
        return null;
    }
    foreach ($blocks[1] as $raw) {
        $raw = trim($raw);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            continue;
        }
        if (($data['@type'] ?? '') === 'Product') {
            return $data;
        }
    }
    return null;
}

function ded_collection_products(string $html): array
{
    $slugs = [];
    if (preg_match_all('#href=["\']\.\./products/([a-z0-9\-_]+)\.html["\']#i', $html, $m)) {
        foreach ($m[1] as $slug) {
            $slugs[$slug] = true;
        }
    }
    return array_keys($slugs);
}

$productFiles = [];
foreach (glob(DED_ROOT . '/products/*.html') ?: [] as $p) {
    if (basename($p) !== 'POST.html') {
        $productFiles[] = $p;
    }
}
$colFiles = [];
foreach (glob(DED_ROOT . '/collections/*.html') ?: [] as $p) {
    $b = basename($p);
    if (!in_array($b, ['POST.html', 'all.html'], true)) {
        $colFiles[] = $p;
    }
}

if ($productFiles === [] && $colFiles === []) {
    $pdo = ded_pdo();
    if ($pdo && ded_db_ready()) {
        $cat = ded_catalog_fetch($pdo);
        $n = count($cat['products'] ?? []);
        $msg = 'HTML kaynağı yok; MySQL katalogda ' . $n . ' ürün var.';
    } else {
        $msg = 'HTML kaynağı yok ve veritabanı katalogu hazır değil. config.local.php ve schema.sql içe aktarımını yapın.';
        if (php_sapi_name() === 'cli') {
            fwrite(STDERR, $msg . PHP_EOL);
            exit(1);
        }
        header('Content-Type: text/plain; charset=utf-8', true, 500);
        echo $msg;
        exit(1);
    }
    if (php_sapi_name() === 'cli') {
        echo $msg . PHP_EOL;
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo $msg;
    }
    exit(0);
}

$products = [];
$productDir = DED_ROOT . '/products';
if (is_dir($productDir)) {
    foreach ($productFiles as $path) {
        $base = basename($path);
        $slug = basename($path, '.html');
        $html = (string) file_get_contents($path);
        $ld = ded_first_json_ld_product($html);
        if (!$ld) {
            continue;
        }
        $ogImage = ded_meta_content($html, 'og:image');
        $images = [];
        if (!empty($ld['image']['url'])) {
            $images[] = ded_normalize_asset_path((string) $ld['image']['url']);
        } elseif (!empty($ld['image']['image'])) {
            $images[] = ded_normalize_asset_path((string) $ld['image']['image']);
        }
        if ($ogImage) {
            $norm = ded_normalize_asset_path($ogImage);
            if (!in_array($norm, $images, true)) {
                array_unshift($images, $norm);
            }
        }
        $variants = [];
        $offers = $ld['offers'] ?? [];
        if (isset($offers['@type'])) {
            $offers = [$offers];
        }
        foreach ($offers as $o) {
            if (!is_array($o)) {
                continue;
            }
            $variants[] = [
                'name' => (string) ($o['name'] ?? ''),
                'price' => isset($o['price']) ? (float) $o['price'] : 0.0,
                'currency' => (string) ($o['priceCurrency'] ?? 'TRY'),
                'sku' => isset($o['sku']) ? (string) $o['sku'] : null,
                'inStock' => ($o['availability'] ?? '') !== 'https://schema.org/OutOfStock',
            ];
        }
        $products[] = [
            'id' => (string) ($ld['productID'] ?? $slug),
            'slug' => $slug,
            'title' => (string) ($ld['name'] ?? $slug),
            'description' => (string) ($ld['description'] ?? ''),
            'brand' => is_array($ld['brand'] ?? null) ? (string) ($ld['brand']['name'] ?? '') : '',
            'price' => $variants[0]['price'] ?? 0.0,
            'currency' => $variants[0]['currency'] ?? 'TRY',
            'images' => array_values(array_unique($images)),
            'variants' => $variants,
            'sourceHtml' => '',
        ];
    }
}

$collections = [];
$colDir = DED_ROOT . '/collections';
if (is_dir($colDir)) {
    foreach ($colFiles as $path) {
        $base = basename($path);
        $id = basename($path, '.html');
        $html = (string) file_get_contents($path);
        $title = ded_title($html) ?? $id;
        $desc = ded_meta_name($html, 'description') ?? '';
        $img = ded_meta_content($html, 'og:image');
        $collections[] = [
            'id' => $id,
            'title' => $title,
            'description' => $desc,
            'image' => $img ? ded_normalize_asset_path($img) : '',
            'productSlugs' => ded_collection_products($html),
            'sourceHtml' => '',
        ];
    }
}

$indexHtml = DED_ROOT . '/index.html';
$site = [
    'name' => 'Laykidsofficial',
    'title' => 'Laykids Official',
    'description' => '',
    'homeCollectionHeading' => 'YETİŞKİNLER VE ÇOCUKLAR İÇİN',
    'homeCollectionSubtext' => 'Ayakkabılarını değiştirmeyi unut! Sadece tek bir düğmeye basarak, yürüyüşün tadını çıkarmanı sağlayacak şık spor ayakkabılara ve patenlere sahip olacaksın.',
    'homeCollectionMoreUrl' => '#',
];
if (is_readable($indexHtml)) {
    $html = (string) file_get_contents($indexHtml);
    $t = ded_title($html);
    $d = ded_meta_name($html, 'description');
    if ($t) {
        $site['title'] = $t;
    }
    if ($d) {
        $site['description'] = $d;
    }
}

$pages = [];
$pagesDir = DED_ROOT . '/pages';
if (is_dir($pagesDir)) {
    foreach (glob($pagesDir . '/*.html') ?: [] as $path) {
        $base = basename($path);
        if ($base === 'POST.html') {
            continue;
        }
        $slug = basename($path, '.html');
        $html = (string) file_get_contents($path);
        $pages[] = [
            'slug' => $slug,
            'title' => ded_title($html) ?? $slug,
            'description' => ded_meta_name($html, 'description') ?? '',
            'sourceHtml' => '',
        ];
    }
}

$catalog = [
    'version' => 1,
    'importedAt' => gmdate('c'),
    'site' => $site,
    'products' => $products,
    'collections' => $collections,
    'pages' => $pages,
];

$pdo = ded_pdo();
if (!$pdo || !ded_db_ready()) {
    $msg = 'MySQL gerekli. config.local.php ve schema.sql ile elle kurulum yapın.';
    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, $msg . PHP_EOL);
        exit(1);
    }
    header('Content-Type: text/plain; charset=utf-8', true, 500);
    echo $msg;
    exit(1);
}
try {
    ded_catalog_save($pdo, $catalog);
    $msg = 'OK: MySQL — ürün: ' . count($products) . ', koleksiyon: ' . count($collections) . ', sayfa: ' . count($pages);
} catch (Throwable $e) {
    $msg = 'MySQL kayıt hatası: ' . $e->getMessage();
}
if (php_sapi_name() === 'cli') {
    echo $msg . PHP_EOL;
} else {
    header('Content-Type: text/plain; charset=utf-8');
    echo $msg;
}

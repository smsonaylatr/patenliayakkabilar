<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/magazadepo.php';
require_once __DIR__ . '/katalogdepo.php';
require_once __DIR__ . '/vitrinrotalar.php';

function ded_seo_context_default(): array
{
    return [
        'type' => 'website',
        'route' => '',
        'title' => '',
        'description' => '',
        'image' => '',
        'url' => '',
        'noindex' => false,
        'breadcrumbs' => [],
        'jsonld' => [],
        'extra' => [],
    ];
}

function ded_seo_set_context(array $ctx): void
{
    $GLOBALS['ded_seo_ctx'] = array_replace(ded_seo_context_default(), $ctx);
}

function ded_seo_get_context(): array
{
    return $GLOBALS['ded_seo_ctx'] ?? ded_seo_context_default();
}

function ded_seo_clip(string $s, int $max): string
{
    $s = trim(preg_replace('/\s+/u', ' ', $s) ?? $s);
    if ($s === '') {
        return '';
    }
    if (function_exists('mb_strlen') && mb_strlen($s, 'UTF-8') > $max) {
        return rtrim(mb_substr($s, 0, $max - 1, 'UTF-8')) . '…';
    }
    if (strlen($s) > $max) {
        return rtrim(substr($s, 0, $max - 1)) . '…';
    }

    return $s;
}

function ded_seo_absolute_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $url)) {
        return $url;
    }
    $base = rtrim(ded_storefront_public_url(), '/');
    if (str_starts_with($url, '/')) {
        return $base . $url;
    }

    return $base . '/' . $url;
}

function ded_seo_canonical_url(): string
{
    $base = rtrim(ded_storefront_public_url(), '/');
    $path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if ($path === '') {
        $path = '/';
    }
    $webBase = '';
    if (function_exists('ded_vitrin_web_base')) {
        $webBase = ded_vitrin_web_base();
    }
    if ($webBase !== '' && str_starts_with($path, $webBase)) {
        $path = substr($path, strlen($webBase));
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . $path;
        }
    }
    $path = rtrim($path, '/');
    if ($path === '') {
        $path = '/';
    }

    return $base . $path;
}

function ded_seo_site_settings(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $pdo = ded_pdo();
    $site = ['name' => '', 'title' => '', 'description' => ''];
    $shop = [];
    $layout = [];
    if ($pdo) {
        try {
            $site = ded_site_row($pdo) + $site;
        } catch (Throwable) {
        }
        if (ded_shop_ready($pdo)) {
            $shop = ded_shop_settings_get($pdo);
        }
        try {
            require_once __DIR__ . '/vitrinlayout.php';
            $layout = ded_vitrin_layout_load($pdo);
        } catch (Throwable) {
        }
    }
    $cache = [
        'site' => $site,
        'shop' => $shop,
        'layout' => $layout,
    ];

    return $cache;
}

function ded_seo_brand_name(): string
{
    $s = ded_seo_site_settings()['site'];
    $n = trim((string) ($s['name'] ?? ''));
    if ($n !== '') {
        return $n;
    }
    $t = trim((string) ($s['title'] ?? ''));

    return $t !== '' ? $t : 'Mağaza';
}

function ded_seo_default_og_image(): string
{
    $S = ded_seo_site_settings();
    $img = trim((string) ($S['shop']['og_default_image'] ?? ''));
    if ($img === '') {
        $img = trim((string) ($S['layout']['logo_path'] ?? ''));
    }

    return $img === '' ? '' : ded_seo_absolute_url($img);
}

function ded_seo_org_profile(): array
{
    $S = ded_seo_site_settings();
    $shop = $S['shop'];
    $social = [];
    $raw = trim((string) ($shop['social_links'] ?? ''));
    if ($raw !== '') {
        foreach (preg_split('/[\r\n,]+/', $raw) ?: [] as $line) {
            $u = trim((string) $line);
            if ($u !== '' && preg_match('#^https?://#i', $u)) {
                $social[] = $u;
            }
        }
    }

    return [
        'phone' => trim((string) ($shop['store_phone'] ?? '')),
        'email' => trim((string) ($shop['store_email'] ?? '')),
        'address' => trim((string) ($shop['store_address'] ?? '')),
        'social' => $social,
    ];
}

function ded_seo_jsonld(array $data): string
{
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

function ded_seo_build_organization(): array
{
    $S = ded_seo_site_settings();
    $name = ded_seo_brand_name();
    $org = ded_seo_org_profile();
    $logo = ded_seo_default_og_image();
    $node = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $name,
        'url' => rtrim(ded_storefront_public_url(), '/') . '/',
    ];
    if ($logo !== '') {
        $node['logo'] = $logo;
    }
    if ($org['social'] !== []) {
        $node['sameAs'] = $org['social'];
    }
    if ($org['phone'] !== '' || $org['email'] !== '') {
        $contact = ['@type' => 'ContactPoint', 'contactType' => 'customer service'];
        if ($org['phone'] !== '') {
            $contact['telephone'] = $org['phone'];
        }
        if ($org['email'] !== '') {
            $contact['email'] = $org['email'];
        }
        $contact['areaServed'] = 'TR';
        $contact['availableLanguage'] = ['Turkish'];
        $node['contactPoint'] = [$contact];
    }
    $S = $S;

    return $node;
}

function ded_seo_build_website(): array
{
    $name = ded_seo_brand_name();
    $home = rtrim(ded_storefront_public_url(), '/') . '/';
    $searchUrl = $home . 'arama?q={search_term_string}';

    return [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $name,
        'url' => $home,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $searchUrl],
            'query-input' => 'required name=search_term_string',
        ],
    ];
}

function ded_seo_build_breadcrumb(array $items): array
{
    $list = [];
    foreach ($items as $i => $it) {
        $entry = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => (string) ($it['name'] ?? ''),
        ];
        $url = (string) ($it['url'] ?? '');
        if ($url !== '') {
            $entry['item'] = ded_seo_absolute_url($url);
        }
        $list[] = $entry;
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $list,
    ];
}

function ded_seo_build_product(array $p, array $images, array $variants): array
{
    $name = (string) ($p['title'] ?? '');
    $desc = ded_seo_clip(strip_tags((string) ($p['description'] ?? '')), 5000);
    $slug = (string) ($p['slug'] ?? '');
    $url = ded_seo_absolute_url(ded_vitrin_url('product', ['slug' => $slug]));
    $cur = (string) ($p['currency'] ?? 'TRY');
    $price = (float) ($p['price'] ?? 0);

    $imgUrls = [];
    foreach ($images as $img) {
        $imgUrls[] = ded_seo_absolute_url((string) $img);
    }

    $node = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $name,
        'url' => $url,
        'sku' => $slug,
    ];
    if ($desc !== '') {
        $node['description'] = $desc;
    }
    if ($imgUrls !== []) {
        $node['image'] = $imgUrls;
    }
    $brand = trim((string) ($p['brand'] ?? ''));
    if ($brand !== '') {
        $node['brand'] = ['@type' => 'Brand', 'name' => $brand];
    }

    if ($variants !== []) {
        $offers = [];
        $prices = [];
        $anyInStock = false;
        foreach ($variants as $v) {
            $vp = (float) ($v['price'] ?? $price);
            $prices[] = $vp;
            $inStock = (bool) ($v['inStock'] ?? ($v['in_stock'] ?? true));
            $anyInStock = $anyInStock || $inStock;
            $offers[] = [
                '@type' => 'Offer',
                'price' => number_format($vp, 2, '.', ''),
                'priceCurrency' => (string) ($v['currency'] ?? $cur),
                'availability' => $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => $url,
                'sku' => (string) ($v['sku'] ?? $slug),
                'itemCondition' => 'https://schema.org/NewCondition',
            ];
        }
        if (count($offers) > 1) {
            $node['offers'] = [
                '@type' => 'AggregateOffer',
                'priceCurrency' => $cur,
                'lowPrice' => number_format(min($prices) ?: $price, 2, '.', ''),
                'highPrice' => number_format(max($prices) ?: $price, 2, '.', ''),
                'offerCount' => count($offers),
                'availability' => $anyInStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'offers' => $offers,
            ];
        } else {
            $node['offers'] = $offers[0];
        }
    } else {
        $node['offers'] = [
            '@type' => 'Offer',
            'price' => number_format($price, 2, '.', ''),
            'priceCurrency' => $cur,
            'availability' => 'https://schema.org/InStock',
            'url' => $url,
            'itemCondition' => 'https://schema.org/NewCondition',
        ];
    }

    return $node;
}

function ded_seo_build_itemlist(string $name, array $products): array
{
    $items = [];
    foreach ($products as $i => $p) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => (string) ($p['name'] ?? ''),
            'url' => ded_seo_absolute_url((string) ($p['url'] ?? '')),
        ];
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => $name,
        'itemListElement' => $items,
    ];
}

function ded_seo_build_faq(array $faq): array
{
    $items = [];
    foreach ($faq as $q) {
        $items[] = [
            '@type' => 'Question',
            'name' => (string) ($q['question'] ?? ''),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags((string) ($q['answer'] ?? ''))],
        ];
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $items,
    ];
}

function ded_seo_build_head(): string
{
    $ctx = ded_seo_get_context();
    $S = ded_seo_site_settings();
    $brand = ded_seo_brand_name();
    $defaultTitle = trim((string) ($S['site']['title'] ?? '')) ?: $brand;
    $defaultDesc = trim((string) ($S['site']['description'] ?? ''));

    $title = trim((string) ($ctx['title'] ?? '')) ?: $defaultTitle;
    if ($title !== '' && $brand !== '' && stripos($title, $brand) === false && ($ctx['type'] ?? '') !== 'home') {
        $title .= ' | ' . $brand;
    }
    $title = ded_seo_clip($title, 65);

    $desc = trim((string) ($ctx['description'] ?? '')) ?: $defaultDesc;
    $desc = ded_seo_clip(strip_tags($desc), 160);

    $url = trim((string) ($ctx['url'] ?? '')) ?: ded_seo_canonical_url();
    $img = trim((string) ($ctx['image'] ?? '')) ?: ded_seo_default_og_image();
    if ($img !== '') {
        $img = ded_seo_absolute_url($img);
    }

    $type = (string) ($ctx['type'] ?? 'website');
    $ogType = match ($type) {
        'product' => 'product',
        'article', 'page' => 'article',
        default => 'website',
    };

    $robots = !empty($ctx['noindex']) ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    $twitterSite = trim((string) ($S['shop']['twitter_site'] ?? ''));
    $twitterCreator = trim((string) ($S['shop']['twitter_creator'] ?? ''));

    $out = '';
    $out .= '<title>' . ded_h($title) . '</title>' . "\n";
    if ($desc !== '') {
        $out .= '<meta name="description" content="' . ded_attr($desc) . '">' . "\n";
    }
    $out .= '<meta name="robots" content="' . ded_attr($robots) . '">' . "\n";
    $kw = trim((string) ($S['shop']['seo_default_keywords'] ?? ''));
    if ($kw !== '') {
        $out .= '<meta name="keywords" content="' . ded_attr($kw) . '">' . "\n";
    }
    $out .= '<link rel="canonical" href="' . ded_attr($url) . '">' . "\n";
    $out .= '<meta property="og:site_name" content="' . ded_attr($brand) . '">' . "\n";
    $out .= '<meta property="og:locale" content="tr_TR">' . "\n";
    $out .= '<meta property="og:type" content="' . ded_attr($ogType) . '">' . "\n";
    $out .= '<meta property="og:title" content="' . ded_attr($title) . '">' . "\n";
    if ($desc !== '') {
        $out .= '<meta property="og:description" content="' . ded_attr($desc) . '">' . "\n";
    }
    $out .= '<meta property="og:url" content="' . ded_attr($url) . '">' . "\n";
    if ($img !== '') {
        $out .= '<meta property="og:image" content="' . ded_attr($img) . '">' . "\n";
        $out .= '<meta property="og:image:alt" content="' . ded_attr($title) . '">' . "\n";
    }
    $out .= '<meta name="twitter:card" content="' . ded_attr($img !== '' ? 'summary_large_image' : 'summary') . '">' . "\n";
    $out .= '<meta name="twitter:title" content="' . ded_attr($title) . '">' . "\n";
    if ($desc !== '') {
        $out .= '<meta name="twitter:description" content="' . ded_attr($desc) . '">' . "\n";
    }
    if ($img !== '') {
        $out .= '<meta name="twitter:image" content="' . ded_attr($img) . '">' . "\n";
    }
    if ($twitterSite !== '') {
        $out .= '<meta name="twitter:site" content="' . ded_attr($twitterSite) . '">' . "\n";
    }
    if ($twitterCreator !== '') {
        $out .= '<meta name="twitter:creator" content="' . ded_attr($twitterCreator) . '">' . "\n";
    }

    if (!empty($ctx['extra']['product_price'])) {
        $out .= '<meta property="product:price:amount" content="' . ded_attr((string) $ctx['extra']['product_price']) . '">' . "\n";
        $out .= '<meta property="product:price:currency" content="' . ded_attr((string) ($ctx['extra']['product_currency'] ?? 'TRY')) . '">' . "\n";
        if (isset($ctx['extra']['product_availability'])) {
            $out .= '<meta property="product:availability" content="' . ded_attr((string) $ctx['extra']['product_availability']) . '">' . "\n";
        }
    }

    $jsonld = [];
    $jsonld[] = ded_seo_build_organization();
    $jsonld[] = ded_seo_build_website();
    if (!empty($ctx['breadcrumbs']) && is_array($ctx['breadcrumbs'])) {
        $jsonld[] = ded_seo_build_breadcrumb($ctx['breadcrumbs']);
    }
    foreach (($ctx['jsonld'] ?? []) as $extra) {
        if (is_array($extra) && $extra !== []) {
            $jsonld[] = $extra;
        }
    }

    foreach ($jsonld as $node) {
        $out .= ded_seo_jsonld($node) . "\n";
    }

    return $out;
}

function ded_seo_overrides_empty(): array
{
    return ['title' => '', 'description' => '', 'image' => '', 'noindex' => false];
}

function ded_seo_ensure_overrides_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    try {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS ded_seo_overrides (
                target_type VARCHAR(10) NOT NULL,
                target_key VARCHAR(181) NOT NULL,
                seo_title VARCHAR(255) NOT NULL DEFAULT "",
                seo_description VARCHAR(320) NOT NULL DEFAULT "",
                seo_image VARCHAR(1024) NOT NULL DEFAULT "",
                noindex TINYINT(1) NOT NULL DEFAULT 0,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (target_type, target_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    } catch (Throwable) {
    }
}

function ded_seo_overrides_get(PDO $pdo, string $type, string $key): array
{
    if ($key === '') {
        return ded_seo_overrides_empty();
    }
    ded_seo_ensure_overrides_schema($pdo);
    try {
        $st = $pdo->prepare('SELECT seo_title, seo_description, seo_image, noindex FROM ded_seo_overrides WHERE target_type = ? AND target_key = ? LIMIT 1');
        $st->execute([$type, $key]);
        $r = $st->fetch();
        if ($r === false) {
            return ded_seo_overrides_empty();
        }

        return [
            'title' => trim((string) ($r['seo_title'] ?? '')),
            'description' => trim((string) ($r['seo_description'] ?? '')),
            'image' => trim((string) ($r['seo_image'] ?? '')),
            'noindex' => (bool) (int) ($r['noindex'] ?? 0),
        ];
    } catch (Throwable) {
        return ded_seo_overrides_empty();
    }
}

function ded_seo_overrides_save(PDO $pdo, string $type, string $key, array $data): void
{
    if ($key === '') {
        return;
    }
    ded_seo_ensure_overrides_schema($pdo);
    $title = trim((string) ($data['title'] ?? ''));
    $desc = trim((string) ($data['description'] ?? ''));
    $img = trim((string) ($data['image'] ?? ''));
    $noindex = !empty($data['noindex']) ? 1 : 0;
    if ($title === '' && $desc === '' && $img === '' && $noindex === 0) {
        $st = $pdo->prepare('DELETE FROM ded_seo_overrides WHERE target_type = ? AND target_key = ?');
        $st->execute([$type, $key]);

        return;
    }
    $st = $pdo->prepare(
        'INSERT INTO ded_seo_overrides (target_type, target_key, seo_title, seo_description, seo_image, noindex)
         VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE seo_title = VALUES(seo_title), seo_description = VALUES(seo_description),
            seo_image = VALUES(seo_image), noindex = VALUES(noindex)'
    );
    $st->execute([$type, $key, $title, $desc, $img, $noindex]);
}

function ded_seo_overrides_rename(PDO $pdo, string $type, string $oldKey, string $newKey): void
{
    if ($oldKey === '' || $newKey === '' || $oldKey === $newKey) {
        return;
    }
    ded_seo_ensure_overrides_schema($pdo);
    try {
        $st = $pdo->prepare('UPDATE ded_seo_overrides SET target_key = ? WHERE target_type = ? AND target_key = ?');
        $st->execute([$newKey, $type, $oldKey]);
    } catch (Throwable) {
    }
}

function ded_seo_product_overrides(PDO $pdo, string $slug): array
{
    return ded_seo_overrides_get($pdo, 'product', $slug);
}

function ded_seo_collection_overrides(PDO $pdo, string $id): array
{
    return ded_seo_overrides_get($pdo, 'collection', $id);
}

function ded_seo_page_overrides(PDO $pdo, string $slug): array
{
    return ded_seo_overrides_get($pdo, 'page', $slug);
}

function ded_seo_strip_existing_meta(string $html): string
{
    $patterns = [
        '#<title>.*?</title>#is',
        '#<link\b[^>]*\brel=["\']canonical["\'][^>]*>#i',
        '#<meta\b[^>]*\bname=["\']description["\'][^>]*>#i',
        '#<meta\b[^>]*\bname=["\']robots["\'][^>]*>#i',
        '#<meta\b[^>]*\bproperty=["\']og:[^"\']+["\'][^>]*>#i',
        '#<meta\b[^>]*\bname=["\']twitter:[^"\']+["\'][^>]*>#i',
        '#<meta\b[^>]*\bproperty=["\']product:[^"\']+["\'][^>]*>#i',
        '#<script\b[^>]*\btype=["\']application/ld\+json["\'][^>]*>[\s\S]*?</script>#i',
    ];
    foreach ($patterns as $p) {
        $html = preg_replace($p, '', $html) ?? $html;
    }

    return $html;
}

function ded_seo_apply_image_perf(string $html): string
{
    $idx = 0;
    $html = preg_replace_callback('#<img\b([^>]*)>#i', static function (array $m) use (&$idx): string {
        $attrs = $m[1];
        if (preg_match('/\bdata-no-perf\b/i', $attrs)) {
            return $m[0];
        }
        $isEager = $idx === 0
            || preg_match('/\bloading\s*=\s*"eager"/i', $attrs)
            || preg_match('/\bfetchpriority\s*=\s*"high"/i', $attrs)
            || preg_match('/\b(hero|logo|product-gallery__image|content-over-media)\b/i', $attrs);
        if (!preg_match('/\bloading\s*=/i', $attrs)) {
            $attrs .= $isEager ? ' loading="eager"' : ' loading="lazy"';
        }
        if (!preg_match('/\bdecoding\s*=/i', $attrs)) {
            $attrs .= ' decoding="async"';
        }
        if ($isEager && !preg_match('/\bfetchpriority\s*=/i', $attrs)) {
            $attrs .= ' fetchpriority="high"';
        }
        $idx++;

        return '<img' . $attrs . '>';
    }, $html) ?? $html;

    $html = preg_replace_callback('#<a\b([^>]*?)href="(https?://[^"]+)"([^>]*)>#i', static function (array $m): string {
        $pre = $m[1];
        $url = $m[2];
        $post = $m[3];
        $host = (string) parse_url($url, PHP_URL_HOST);
        $base = (string) parse_url(ded_storefront_public_url(), PHP_URL_HOST);
        if ($host === '' || strcasecmp($host, $base) === 0) {
            return $m[0];
        }
        $all = $pre . $post;
        if (!preg_match('/\brel\s*=/i', $all)) {
            $post .= ' rel="noopener noreferrer"';
        }
        if (!preg_match('/\btarget\s*=/i', $all)) {
            $post .= ' target="_blank"';
        }

        return '<a' . $pre . 'href="' . $url . '"' . $post . '>';
    }, $html) ?? $html;

    return $html;
}

function ded_seo_inject(string $html): string
{
    $html = ded_seo_strip_existing_meta($html);
    $head = ded_seo_build_head();
    $ctx = ded_seo_get_context();
    if (!empty($ctx['image'])) {
        $abs = ded_seo_absolute_url((string) $ctx['image']);
        $head = '<link rel="preload" as="image" href="' . ded_attr($abs) . '" fetchpriority="high">' . "\n" . $head;
    }
    $html = preg_replace_callback('#<html\b([^>]*)>#i', static function (array $m): string {
        $attrs = $m[1];
        if (preg_match('/\blang\s*=/', $attrs)) {
            $attrs = preg_replace('/\blang\s*=\s*"[^"]*"/i', 'lang="tr"', $attrs) ?? $attrs;
        } else {
            $attrs .= ' lang="tr"';
        }
        if (!preg_match('/\bdir\s*=/', $attrs)) {
            $attrs .= ' dir="ltr"';
        }

        return '<html' . $attrs . '>';
    }, $html, 1) ?? $html;
    $html = preg_replace('#</head>#i', $head . '</head>', $html, 1) ?? $html;

    return ded_seo_apply_image_perf($html);
}

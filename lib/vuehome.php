<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/katalogdepo.php';
require_once __DIR__ . '/para.php';
require_once __DIR__ . '/vitrin.php';
require_once __DIR__ . '/vitrinrotalar.php';

const DED_VUE_HOME_DIST = DED_ROOT . '/vue-home/dist';
const DED_VUE_HOME_MANIFEST = DED_VUE_HOME_DIST . '/.vite/manifest.json';

function ded_vue_home_enabled(): bool
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $cfg = ded_config();
    if (is_array($cfg) && array_key_exists('home_vue', $cfg) && $cfg['home_vue'] === false) {
        $cached = false;
        return false;
    }
    $cached = is_readable(DED_VUE_HOME_MANIFEST);
    return $cached;
}

function ded_vue_home_asset_urls(): ?array
{
    if (!is_readable(DED_VUE_HOME_MANIFEST)) {
        return null;
    }
    $manifest = json_decode((string) file_get_contents(DED_VUE_HOME_MANIFEST), true);
    if (!is_array($manifest)) {
        return null;
    }
    $entry = $manifest['src/main.js'] ?? $manifest['index.html'] ?? null;
    if (!is_array($entry)) {
        foreach ($manifest as $item) {
            if (is_array($item) && ($item['isEntry'] ?? false)) {
                $entry = $item;
                break;
            }
        }
    }
    if (!is_array($entry) || empty($entry['file'])) {
        return null;
    }
    $base = ded_vitrin_web_base() . '/vue-home/dist/';
    $css = [];
    foreach ($entry['css'] ?? [] as $c) {
        if (is_string($c) && $c !== '') {
            $css[] = $base . $c;
        }
    }

    return [
        'js' => $base . $entry['file'],
        'css' => $css,
    ];
}

function ded_vue_home_image_src(string $path): string
{
    return ded_storefront_image_src($path, 'index');
}

function ded_vue_home_product_dto(array $det): array
{
    $row = $det['row'];
    $imgs = array_values(array_filter(array_map('strval', $det['images'] ?? [])));
    $price = ded_product_effective_sale_price($row, $det['variants'] ?? []);
    $compare = ded_product_compare_at_from_row($row);
    $promo = $compare !== null && $compare > $price + 0.009;
    $slug = (string) ($row['slug'] ?? '');

    $primary = $imgs[0] ?? '';
    $secondary = $imgs[1] ?? '';

    $saving = '';
    if ($promo && $compare !== null) {
        $saving = ded_format_price_try_like_theme($compare - $price) . ' tasarruf edin';
    }

    return [
        'slug' => $slug,
        'handle' => $slug,
        'title' => (string) ($row['title'] ?? ''),
        'url' => ded_vitrin_url('product', ['slug' => $slug]),
        'price' => $price,
        'priceFormatted' => ded_format_price_try_like_theme($price),
        'compareAt' => $promo ? $compare : null,
        'compareAtFormatted' => $promo && $compare !== null ? ded_format_price_try_like_theme($compare) : null,
        'onSale' => $promo,
        'savingLabel' => $saving,
        'imagePrimary' => $primary !== '' ? ded_vue_home_image_src($primary) : '',
        'imageSecondary' => $secondary !== '' ? ded_vue_home_image_src($secondary) : '',
        'audience' => ded_product_audience_label(ded_product_audience_normalize($row['audience'] ?? null)),
    ];
}

function ded_vue_home_featured_products(PDO $pdo, string $colSlug, int $limit = 12): array
{
    if ($colSlug === '') {
        return [];
    }
    $colData = ded_collection_by_id($pdo, $colSlug);
    if ($colData === null) {
        return [];
    }
    $out = [];
    $seen = [];
    foreach ($colData['products'] as $pr) {
        if (count($out) >= $limit) {
            break;
        }
        $prodSlug = (string) ($pr['slug'] ?? '');
        if ($prodSlug === '' || isset($seen[$prodSlug])) {
            continue;
        }
        $seen[$prodSlug] = true;
        $det = ded_product_by_slug($pdo, $prodSlug);
        if ($det === null) {
            continue;
        }
        $out[] = ded_vue_home_product_dto($det);
    }

    return $out;
}

function ded_vue_home_api_payload(?PDO $pdo = null): array
{
    $pdo = $pdo ?? ded_pdo();
    require_once __DIR__ . '/vitrinlayout.php';

    $layout = $pdo ? ded_vitrin_layout_load($pdo) : ded_vitrin_layout_merge([]);
    $home = $layout['home'] ?? [];
    $site = $pdo ? ded_site_row($pdo) : [];

    $hero = $home['hero'] ?? [];
    $titlesCfg = isset($home['featured_titles']) && is_array($home['featured_titles'])
        ? $home['featured_titles']
        : [];
    $slugCfg = isset($home['featured_collection_slugs']) && is_array($home['featured_collection_slugs'])
        ? $home['featured_collection_slugs']
        : [];
    $fallbackTitles = ['Çocuk', 'Erkek', 'Kadın'];
    $fallbackSlugs = [
        'cocuk-tekerlekli-ayakkabi',
        'erkek-tekerlekli-ayakkabi',
        'kadin-tekerlekli-ayakkabi',
    ];

    $collections = [];
    if ($pdo) {
        ded_catalog_ensure_products_in_collections($pdo);
        foreach (ded_collections_all($pdo) as $c) {
            $id = (string) ($c['id'] ?? '');
            $collections[] = [
                'id' => $id,
                'title' => (string) ($c['title'] ?? ''),
                'image' => ded_vue_home_image_src((string) ($c['image_path'] ?? '')),
                'url' => ded_vitrin_url('collection', ['slug' => $id]),
            ];
        }
    }

    $featured = [];
    for ($i = 0; $i < 3; $i++) {
        $title = isset($titlesCfg[$i]) ? trim((string) $titlesCfg[$i]) : '';
        if ($title === '') {
            $title = $fallbackTitles[$i] ?? '';
        }
        $colSlug = isset($slugCfg[$i]) ? trim((string) $slugCfg[$i]) : '';
        if ($colSlug === '') {
            $colSlug = $fallbackSlugs[$i] ?? '';
        }
        $featured[] = [
            'title' => $title,
            'collectionUrl' => $colSlug !== '' ? ded_vitrin_url('collection', ['slug' => $colSlug]) : ded_vitrin_url('collections'),
            'products' => $pdo ? ded_vue_home_featured_products($pdo, $colSlug) : [],
        ];
    }

    $imageText = $home['image_text'] ?? [];
    $richText = $home['rich_text'] ?? [];
    $video = $home['video'] ?? [];
    $imageOverlay = $home['image_overlay'] ?? [];

    return [
        'hero' => [
            'imageDesktop' => ded_vue_home_image_src((string) ($hero['image_desktop'] ?? '')),
            'imageMobile' => ded_vue_home_image_src((string) ($hero['image_mobile'] ?? '')),
            'subheading' => (string) ($hero['subheading'] ?? ''),
            'heading' => (string) ($hero['heading'] ?? ''),
        ],
        'collectionsIntro' => [
            'heading' => trim((string) ($site['home_collection_heading'] ?? '')) ?: 'YETİŞKİNLER VE ÇOCUKLAR İÇİN',
            'subtext' => trim((string) ($site['home_collection_subtext'] ?? ''))
                ?: 'Ayakkabılarını değiştirmeyi unut! Sadece tek bir düğmeye basarak, yürüyüşün tadını çıkarmanı sağlayacak şık spor ayakkabılara ve patenlere sahip olacaksın.',
            'moreUrl' => trim((string) ($site['home_collection_more_url'] ?? '')) ?: ded_vitrin_url('collections'),
        ],
        'collections' => $collections,
        'scrollingText' => trim((string) ($home['scrolling_text'] ?? '')) ?: 'HER YERDE KAY',
        'featured' => $featured,
        'video' => [
            'youtubeId' => (string) ($video['youtube_id'] ?? ''),
            'heading' => (string) ($video['heading'] ?? ''),
        ],
        'imageText' => [
            'image' => ded_vue_home_image_src((string) ($imageText['image'] ?? '')),
            'heading' => (string) ($imageText['heading'] ?? ''),
            'bodyHtml' => (string) ($imageText['body_html'] ?? ''),
        ],
        'richText' => [
            'heading' => (string) ($richText['heading'] ?? ''),
            'bodyHtml' => (string) ($richText['body_html'] ?? ''),
        ],
        'imageOverlay' => [
            'image' => ded_vue_home_image_src((string) ($imageOverlay['image'] ?? '')),
            'heading' => (string) ($imageOverlay['heading'] ?? ''),
        ],
        'urls' => [
            'api' => ded_vitrin_web_base() . '/api.php?path=public-home',
            'home' => ded_vitrin_url('home'),
            'collections' => ded_vitrin_url('collections'),
            'cart' => ded_vitrin_url('cart'),
        ],
    ];
}

function ded_vue_home_boot_json(?PDO $pdo = null): string
{
    $payload = ['ok' => true, 'home' => ded_vue_home_api_payload($pdo)];
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    return $json !== false ? $json : '{"ok":false}';
}

function ded_vue_home_replace_main(string $html, ?PDO $pdo = null): string
{
    $boot = ded_vue_home_boot_json($pdo);
    $assets = ded_vue_home_asset_urls();
    $jsScript = $assets ? '<script type="module" src="' . ded_attr($assets['js']) . '"></script>' : '';
    $replacement = '<main role="main" id="main" class="anchor transition-fade">'
        . '<div class="shopify-section" style="display:none" aria-hidden="true"><div allow-transparent-header></div></div>'
        . '<div id="ded-vue-home"></div>'
        . '<script type="application/json" id="ded-vue-home-boot">' . $boot . '</script>'
        . $jsScript
        . '</main>';
    $out = preg_replace('#<main\b[^>]*>.*?</main>#is', $replacement, $html, 1);
    return $out ?? $html;
}

function ded_vue_home_inject_assets(string $html): string
{
    $assets = ded_vue_home_asset_urls();
    if ($assets === null) {
        return $html;
    }
    $tags = '';
    foreach ($assets['css'] as $href) {
        $tags .= '<link rel="stylesheet" href="' . ded_attr($href) . '">';
    }
    if (preg_match('#</head>#i', $html)) {
        return preg_replace('#</head>#i', $tags . '</head>', $html, 1) ?? $html;
    }
    return $html . $tags;
}

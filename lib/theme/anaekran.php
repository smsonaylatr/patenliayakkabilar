<?php

declare(strict_types=1);

require_once __DIR__ . '/../vitrin.php';
require_once __DIR__ . '/../katalogdepo.php';


const DED_PATCH_INDEX_HERO_TEXT_BLOCK = '#<h2 class="h2" reveal-on-scroll="true"><split-lines>.*?</split-lines></h2>\s*<p>.*?</p>#s';

const DED_PATCH_INDEX_MORE_LINK_FIRST = '~<a href="#" class="text-with-icon group">\s*<span class="reversed-link">Daha Fazla</span>~';

const DED_PATCH_INDEX_COLLECTION_LIST = '#<collection-list class="collection-list">.*?</collection-list>#s';

const DED_PATCH_INDEX_FEATURED_PRODUCT_STRIP = '/<reveal-items selector="\\.product-list > \\*">\\s*<product-list class="product-list"[^>]*>.*?<\\/product-list>\\s*<\\/reveal-items>/s';

const DED_PATCH_INDEX_FIRST_PRODUCT_CARD = '/<product-card\\b[^>]*>.*?<\\/product-card>/s';

function ded_patch_index_apply(PDO $pdo, string $html): string
{
    if (!function_exists('ded_product_card_from_prototype')) {
        require_once __DIR__ . '/../sabloncalistir.php';
    }

    ded_catalog_ensure_products_in_collections($pdo);

    $html = ded_patch_index_site_meta_and_blocks($pdo, $html);

    require_once __DIR__ . '/../vuehome.php';
    if (ded_vue_home_enabled()) {
        $html = ded_vue_home_replace_main($html, $pdo);
        $html = ded_html_rewrite_context($html, 'index');
        require_once __DIR__ . '/../vitrinlayout.php';
        $html = ded_vitrin_apply_global_layout($html, $pdo);
        $html = ded_vue_home_inject_assets($html);
    } else {
        $html = ded_patch_index_featured_product_carousels($pdo, $html);
        $html = ded_html_rewrite_context($html, 'index');
        require_once __DIR__ . '/../vitrinlayout.php';
        $html = ded_vitrin_apply_home_layout($html, $pdo);
    }

    require_once __DIR__ . '/../seo.php';
    $site = ded_site_row($pdo);
    ded_seo_set_context([
        'type' => 'home',
        'route' => 'home',
        'title' => trim((string) ($site['title'] ?? '')) ?: trim((string) ($site['name'] ?? '')),
        'description' => trim((string) ($site['description'] ?? '')),
        'url' => rtrim(ded_storefront_public_url(), '/') . '/',
        'breadcrumbs' => [
            ['name' => 'Ana sayfa', 'url' => ded_vitrin_url('home')],
        ],
    ]);

    require_once __DIR__ . '/../sepetsayfa.php';

    return ded_theme_inject_cart_core_global($html);
}

function ded_patch_index_site_meta_and_blocks(PDO $pdo, string $html): string
{
    $site = ded_site_row($pdo);
    $title = (string) ($site['title'] ?? '');
    $desc = (string) ($site['description'] ?? '');
    $heading = (string) ($site['home_collection_heading'] ?? '');
    $sub = (string) ($site['home_collection_subtext'] ?? '');
    $moreUrl = (string) ($site['home_collection_more_url'] ?? '#');

    if ($title !== '') {
        $html = preg_replace('/<title>.*?<\/title>/s', '<title>' . ded_h($title) . '</title>', $html, 1) ?? $html;
        $html = preg_replace(
            '/<meta property="og:title" content="[^"]*"/',
            '<meta property="og:title" content="' . ded_attr($title) . '"',
            $html,
            1
        ) ?? $html;
        $html = preg_replace(
            '/<meta name="twitter:title" content="[^"]*"/',
            '<meta name="twitter:title" content="' . ded_attr($title) . '"',
            $html,
            1
        ) ?? $html;
    }
    if ($desc !== '') {
        $html = preg_replace(
            '/<meta name="description" content="[^"]*"/',
            '<meta name="description" content="' . ded_attr($desc) . '"',
            $html,
            1
        ) ?? $html;
        $html = preg_replace(
            '/<meta property="og:description" content="[^"]*"/',
            '<meta property="og:description" content="' . ded_attr($desc) . '"',
            $html,
            1
        ) ?? $html;
        $html = preg_replace(
            '/<meta name="twitter:description" content="[^"]*"/',
            '<meta name="twitter:description" content="' . ded_attr($desc) . '"',
            $html,
            1
        ) ?? $html;
    }

    $html = preg_replace(
        DED_PATCH_INDEX_HERO_TEXT_BLOCK,
        '<h2 class="h2" reveal-on-scroll="true"><split-lines>' . ded_h($heading) . '</split-lines></h2><p>' . ded_h($sub) . '</p>',
        $html,
        1
    ) ?? $html;

    $html = preg_replace(
        DED_PATCH_INDEX_MORE_LINK_FIRST,
        '<a href="' . ded_attr($moreUrl) . '" class="text-with-icon group">
        <span class="reversed-link">Daha Fazla</span>',
        $html,
        1
    ) ?? $html;

    $cols = ded_collections_all($pdo);
    $inner = '';
    foreach ($cols as $c) {
        $inner .= ded_render_collection_card_html($c);
    }
    if ($inner !== '') {
        $html = preg_replace(
            DED_PATCH_INDEX_COLLECTION_LIST,
            '<collection-list class="collection-list">' . $inner . '</collection-list>',
            $html,
            1
        ) ?? $html;
    }

    return $html;
}

function ded_patch_index_featured_product_carousels(PDO $pdo, string $html): string
{
    if (!preg_match(DED_PATCH_INDEX_FIRST_PRODUCT_CARD, $html, $cm)) {
        return $html;
    }
    $proto = $cm[0];
    if (!function_exists('ded_vitrin_layout_load')) {
        require_once __DIR__ . '/../vitrinlayout.php';
    }
    $homeLayout = ded_vitrin_layout_load($pdo)['home'] ?? [];
    $slugCfg = isset($homeLayout['featured_collection_slugs']) && is_array($homeLayout['featured_collection_slugs'])
        ? $homeLayout['featured_collection_slugs']
        : [];
    $fallbackSlugs = [
        'cocuk-tekerlekli-ayakkabi',
        'erkek-tekerlekli-ayakkabi',
        'kadin-tekerlekli-ayakkabi',
    ];
    $ci = 0;

    $patched = preg_replace_callback(
        DED_PATCH_INDEX_FEATURED_PRODUCT_STRIP,
        function (array $m) use ($pdo, $proto, $slugCfg, $fallbackSlugs, &$ci): string {
            $colSlug = isset($slugCfg[$ci]) ? trim((string) $slugCfg[$ci]) : '';
            if ($colSlug === '') {
                $colSlug = trim((string) ($fallbackSlugs[$ci] ?? ''));
            }
            $rows = [];
            if ($colSlug !== '') {
                $colData = ded_collection_by_id($pdo, $colSlug);
                if ($colData !== null) {
                    $rows = $colData['products'];
                }
            }
            ++$ci;

            $cardsHtml = '';
            $seen = [];
            foreach ($rows as $pr) {
                $prodSlug = (string) ($pr['slug'] ?? '');
                if ($prodSlug === '' || isset($seen[$prodSlug])) {
                    continue;
                }
                $seen[$prodSlug] = true;
                $det = ded_product_by_slug($pdo, $prodSlug);
                if ($det === null) {
                    continue;
                }
                $imgs = $det['images'] ?? [];
                $cardRow = $det['row'];
                $cardRow['price'] = ded_product_effective_sale_price($det['row'], $det['variants'] ?? []);
                $cardsHtml .= ded_product_card_from_prototype($proto, $cardRow, $imgs);
            }
    
            if ($cardsHtml === '') {
                return '<reveal-items selector=".product-list > *"><product-list class="product-list product-list--empty" role="region" aria-live="polite"></product-list></reveal-items>';
            }

            return '<reveal-items selector=".product-list > *"><product-list class="product-list" role="region" aria-live="polite">'
                . $cardsHtml . '</product-list></reveal-items>';
        },
        $html
    );

    return $patched ?? $html;
}

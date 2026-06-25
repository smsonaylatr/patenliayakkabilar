<?php

declare(strict_types=1);

require_once __DIR__ . '/vitrin.php';

function ded_cart_js_ver(): string
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
    $t = max(
        @filemtime($dir . 'sepetcekirdek.js') ?: 0,
        @filemtime($dir . 'sepeturun.js') ?: 0,
        @filemtime($dir . 'sepetsayfa.js') ?: 0,
        @filemtime($dir . 'hizliekle.js') ?: 0,
    );
    $cache = $t > 0 ? '?v=' . $t : '';

    return $cache;
}

function ded_cart_product_image_map(PDO $pdo): array
{
    if (!ded_db_ready()) {
        return [];
    }
    $sql = 'SELECT p.slug, pi.path
            FROM ded_products p
            INNER JOIN ded_product_images pi ON pi.product_id = p.id
            ORDER BY p.slug ASC, pi.sort_order ASC, pi.id ASC';
    $map = [];
    foreach ($pdo->query($sql) as $row) {
        $slug = (string) ($row['slug'] ?? '');
        if ($slug === '' || isset($map[$slug])) {
            continue;
        }
        $path = ded_storefront_image_src((string) ($row['path'] ?? ''), 'index');
        if ($path !== '') {
            $map[$slug] = $path;
        }
    }

    return $map;
}

function ded_cart_swap_main_section(string $html): string
{
    $shell =
        '<section$1 class="shopify-section shopify-section--main-cart ded-cart-page">'
        . '<div class="container ded-cart-page__inner">'
        . '<header class="ded-cart-head">'
        . '<h1 class="ded-cart-head__title">Sepetim</h1>'
        . '<p class="ded-cart-head__sub" id="ded-cart-head-count">Yükleniyor…</p>'
        . '</header>'
        . '<div class="ded-cart-page__grid">'
        . '<div id="ded-cart-root" class="ded-cart-page__lines" aria-live="polite"></div>'
        . '<aside id="ded-cart-summary" class="ded-cart-page__summary" hidden></aside>'
        . '</div></div></section>';

    $out = preg_replace(
        '#<section([^>]*shopify-section--main-cart[^>]*)>[\s\S]*?</section>#',
        $shell,
        $html,
        1
    );

    $html = $out ?? $html;
    $html = preg_replace('/<title>.*?<\/title>/s', '<title>Sepetim — Laykidsofficial</title>', $html, 1) ?? $html;

    return $html;
}

function ded_cart_append_page_scripts(string $html): string
{
    $mapJson = '{}';
    $pdo = ded_pdo();
    if ($pdo !== null) {
        $encoded = json_encode(
            ded_cart_product_image_map($pdo),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if ($encoded !== false) {
            $mapJson = $encoded;
        }
    }

    if (stripos($html, 'sepet.css') === false) {
        $html = preg_replace(
            '#</head>#i',
            '<link rel="stylesheet" href="css/sepet.css">' . "\n" . '</head>',
            $html,
            1
        ) ?? $html;
    }

    $snippet = ded_vitrin_js_config_script()
        . '<script src="js/vitrin-urls.js"></script>'
        . '<script>window.DED_CART_IMAGE_MAP=' . $mapJson . ';</script>'
        . '<script src="js/sepetcekirdek.js' . ded_cart_js_ver() . '"></script><script src="js/sepetsayfa.js' . ded_cart_js_ver() . '" defer></script>';
    $out = preg_replace('#</body>#i', $snippet . '</body>', $html, 1);

    return $out ?? $html;
}

function ded_cart_inject_product_scripts(string $html, string $slug, array $row, array $images, array $variants = []): string
{
    if (stripos($html, 'urun.css') === false) {
        $html = preg_replace(
            '#</head>#i',
            '<link rel="stylesheet" href="css/urun.css">' . "\n" . '</head>',
            $html,
            1
        ) ?? $html;
    }

    $boot = [
        'slug' => $slug,
        'title' => (string) ($row['title'] ?? ''),
        'price' => (float) ($row['price'] ?? 0),
        'currency' => (string) ($row['currency'] ?? 'TRY'),
        'image' => $images !== []
            ? ded_storefront_image_src((string) $images[0], 'index')
            : '',
        'variants' => $variants,
    ];
    $capBoot = ded_product_compare_at_from_row($row);
    if ($capBoot !== null) {
        $boot['compareAtPrice'] = $capBoot;
    }
    $json = json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        $json = '{}';
    }
    $snippet = '<script>window.DED_PRODUCT_BOOT=' . $json . ';</script>'
        . ded_vitrin_js_config_script()
        . '<script src="js/vitrin-urls.js"></script>'
        . '<script src="js/sepetcekirdek.js' . ded_cart_js_ver() . '"></script>'
        . '<script src="js/sepeturun.js' . ded_cart_js_ver() . '"></script>'
        . '<script src="js/hizliekle.js' . ded_cart_js_ver() . '"></script>';
    $out = preg_replace('#</body>#i', $snippet . '</body>', $html, 1);

    return $out ?? $html;
}

function ded_theme_inject_cart_core_global(string $html): string
{
    $snippet = '';
    if (strpos($html, 'sepetcekirdek.js') === false) {
        $snippet .= ded_vitrin_js_config_script()
            . '<script src="js/vitrin-urls.js"></script>'
            . '<script src="js/sepetcekirdek.js' . ded_cart_js_ver() . '"></script>';
    }
    if (strpos($html, 'sepeturun.js') === false) {
        $snippet .= '<script src="js/sepeturun.js' . ded_cart_js_ver() . '"></script>';
    }
    if (strpos($html, 'hizliekle.js') === false) {
        $snippet .= '<script src="js/hizliekle.js' . ded_cart_js_ver() . '"></script>';
    }
    if ($snippet === '') {
        return $html;
    }

    if (strpos($html, 'window.DED_VITRIN') === false) {
        $snippet = ded_vitrin_js_config_script() . $snippet;
    }

    if (stripos($html, 'urun.css') === false && strpos($snippet, 'sepeturun.js') !== false) {
        $html = preg_replace(
            '#</head>#i',
            '<link rel="stylesheet" href="css/urun.css">' . "\n" . '</head>',
            $html,
            1
        ) ?? $html;
    }
    $out = preg_replace('#</body>#i', $snippet . '</body>', $html, 1);

    return $out ?? $html;
}

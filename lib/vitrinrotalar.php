<?php

declare(strict_types=1);



function ded_vitrin_web_base(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = dirname($script);
    if ($dir === '/' || $dir === '.' || $dir === '\\') {
        $base = '';
    } else {
        $base = rtrim($dir, '/');
    }

    return $base;
}


function ded_vitrin_url(string $route, array $params = []): string
{
    $path = match ($route) {
        'home' => '',
        'collections' => '/koleksiyonlar',
        'collection' => '/koleksiyon/' . rawurlencode((string) ($params['slug'] ?? '')),
        'product' => '/urun/' . rawurlencode((string) ($params['slug'] ?? '')),
        'page' => '/sayfa/' . rawurlencode((string) ($params['slug'] ?? '')),
        'cart' => '/sepet',
        'checkout' => '/odeme',
        'search' => '/arama',
        'order_success' => '/siparis-tamam',
        default => '',
    };

    if ($route === 'order_success' && (($params['num'] ?? '') !== '' || ($params['no'] ?? '') !== '')) {
        $num = (string) ($params['num'] ?? $params['no'] ?? '');
        $path .= '?num=' . rawurlencode($num);
    }
    if ($route === 'search' && ($params['q'] ?? '') !== '') {
        $path .= (str_contains($path, '?') ? '&' : '?') . 'q=' . rawurlencode((string) $params['q']);
    }

    $base = ded_vitrin_web_base();
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }

    return $base . $path;
}

function ded_vitrin_href(string $route, array $params = []): string
{
    return ded_attr(ded_vitrin_url($route, $params));
}

function ded_vitrin_slug_from_request(string $queryKey = 'slug'): string
{
    if (isset($_GET[$queryKey])) {
        return ded_storefront_slug_from_request((string) $_GET[$queryKey]);
    }

    return '';
}


function ded_vitrin_rewrite_html_links(string $html): string
{
    $html = preg_replace_callback(
        '#href=(["\'])(?:\.\./)?urun\.php\?slug=([^"\'&]+)\1#i',
        static fn (array $m): string => 'href=' . $m[1] . ded_vitrin_url('product', ['slug' => rawurldecode($m[2])]) . $m[1],
        $html
    ) ?? $html;

    $html = preg_replace_callback(
        '#href=(["\'])(?:\.\./)?koleksiyon\.php\?slug=([^"\'&]+)\1#i',
        static fn (array $m): string => 'href=' . $m[1] . ded_vitrin_url('collection', ['slug' => rawurldecode($m[2])]) . $m[1],
        $html
    ) ?? $html;

    $html = preg_replace_callback(
        '#href=(["\'])(?:\.\./)?sayfa\.php\?slug=([^"\'&]+)\1#i',
        static fn (array $m): string => 'href=' . $m[1] . ded_vitrin_url('page', ['slug' => rawurldecode($m[2])]) . $m[1],
        $html
    ) ?? $html;

    $replacements = [
        'koleksiyonlar.php' => ded_vitrin_url('collections'),
        'sepet.php' => ded_vitrin_url('cart'),
        'odeme.php' => ded_vitrin_url('checkout'),
        'arama.php' => ded_vitrin_url('search'),
        'siparisonay.php' => ded_vitrin_url('order_success'),
        'collections.php' => ded_vitrin_url('collections'),
        'cart.php' => ded_vitrin_url('cart'),
        'checkout.php' => ded_vitrin_url('checkout'),
        'search.php' => ded_vitrin_url('search'),
        'index.php' => ded_vitrin_url('home'),
        'index.html' => ded_vitrin_url('home'),
    ];
    foreach ($replacements as $old => $new) {
        $html = str_replace('href="' . $old . '"', 'href="' . $new . '"', $html);
        $html = str_replace("href='" . $old . "'", "href='" . $new . "'", $html);
    }

    
    $searchActionRewrite = static function (array $m): string {
        $q = $m[1];

        return 'action=' . $q . ded_attr(ded_vitrin_url('search')) . $q;
    };
    $html = preg_replace_callback(
        '#\baction\s*=\s*(["\'])https?:\/\/[^"\']+\/search\/?[^"\']*\1#iu',
        $searchActionRewrite,
        $html
    ) ?? $html;
    $html = preg_replace_callback(
        '#\baction\s*=\s*(["\'])\/\/[^"\']+\/search\/?[^"\']*\1#iu',
        $searchActionRewrite,
        $html
    ) ?? $html;
    $html = preg_replace_callback(
        '~\baction\s*=\s*(["\'])/\s*search/?(?:[?#][^"\']*)?\1~iu',
        $searchActionRewrite,
        $html
    ) ?? $html;

    return $html;
}


function ded_vitrin_base_href(): string
{
    return rtrim(ded_storefront_public_url(), '/') . '/';
}


function ded_vitrin_normalize_asset_paths(string $html): string
{
    $html = preg_replace('#(\.\./)+cdn/#', 'cdn/', $html) ?? $html;

    $root = ded_vitrin_web_base();
    if ($root === '') {
        return $html;
    }

    $prefix = $root;
    $html = preg_replace(
        '#(?<=["\'])(?!(?:https?:)?//)(?!(?:' . preg_quote($prefix, '#') . '/))cdn/#',
        $prefix . '/cdn/',
        $html
    ) ?? $html;
    $html = preg_replace(
        '#(?<=["\'])(?!(?:https?:)?//)(?!(?:' . preg_quote($prefix, '#') . '/))js/#',
        $prefix . '/js/',
        $html
    ) ?? $html;

    return $html;
}

function ded_vitrin_inject_base_tag(string $html): string
{
    $base = ded_vitrin_base_href();
    if (preg_match('#<base\s#i', $html)) {
        return preg_replace(
            '#<base\s[^>]*href=["\'][^"\']*["\'][^>]*>#i',
            '<base href="' . ded_attr($base) . '">',
            $html,
            1
        ) ?? $html;
    }

    return preg_replace(
        '/<head(\s[^>]*)?>/i',
        '<head$1>' . "\n" . '<base href="' . ded_attr($base) . '">',
        $html,
        1
    ) ?? $html;
}



function ded_vitrin_patch_shopify_routes_root(string $html): string
{
    $base = ded_vitrin_web_base();
    $root = $base === '' ? '/' : ($base . '/');

    return (string) (preg_replace(
        '#Shopify\.routes\.root\s*=\s*(["\'])[^"\']*\1\s*;#u',
        'Shopify.routes.root = ' . json_encode($root, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE) . ';',
        $html
    ) ?? $html);
}

function ded_vitrin_finalize_document(string $html): string
{
    $html = ded_vitrin_normalize_asset_paths($html);
    $html = ded_vitrin_patch_shopify_routes_root($html);
    $html = ded_vitrin_inject_base_tag($html);
    require_once __DIR__ . '/vitrinlayout.php';
    $html = ded_vitrin_apply_global_layout($html);
    require_once __DIR__ . '/seo.php';
    $html = ded_seo_inject($html);
    require_once __DIR__ . '/vitrinson.php';

    return ded_vitrin_apply_extras($html);
}

function ded_vitrin_js_config_script(): string
{
    $cfg = [
        'base' => ded_vitrin_web_base(),
        'home' => ded_vitrin_url('home'),
        'collections' => ded_vitrin_url('collections'),
        'cart' => ded_vitrin_url('cart'),
        'checkout' => ded_vitrin_url('checkout'),
        'search' => ded_vitrin_url('search'),
        'orderSuccess' => ded_vitrin_url('order_success'),
        
        'productHrefPrefix' => ded_vitrin_url('product', ['slug' => '']),
    ];

    return '<script>window.DED_VITRIN=' . json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . ';</script>';
}

function ded_vitrin_public_url(string $route, array $params = []): string
{
    return ded_storefront_public_url() . ded_vitrin_url($route, $params);
}

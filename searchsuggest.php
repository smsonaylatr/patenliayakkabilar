<?php

declare(strict_types=1);



require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/katalogdepo.php';
require_once __DIR__ . '/lib/vitrinrotalar.php';
require_once __DIR__ . '/lib/vitrin.php';

header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow', true);

$q = trim((string) ($_GET['q'] ?? ''));

$limit = 10;
if (isset($_GET['resources']) && is_array($_GET['resources']) && isset($_GET['resources']['limit'])) {
    $limit = (int) $_GET['resources']['limit'];
}
$limit = max(1, min($limit, 24));

$pdo = ded_pdo();
$rows = ($pdo instanceof PDO && ded_db_ready() && $q !== '')
    ? ded_products_search_quick($pdo, $q, $limit)
    : [];

ob_start();

echo '<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8"></head><body>';

echo '<div slot="results">';

if ($rows === []) {
    echo '<div class="empty-state">';
    echo '<p class="h6 text-subdued">Sonuç bulunamadı.</p>';
    echo '</div>';
} else {
    echo '<div class="v-stack gap-4 sm:gap-6 ded-predictive-results">';
    foreach ($rows as $pr) {
        $slug = (string) ($pr['slug'] ?? '');
        $title = (string) ($pr['title'] ?? '');
        $thumb = isset($pr['_ded_thumb']) && is_string($pr['_ded_thumb']) ? $pr['_ded_thumb'] : '';
        unset($pr['_ded_thumb']);

        $price = (float) ($pr['price'] ?? 0);
        $cur = (string) ($pr['currency'] ?? 'TRY');

        $href = ded_vitrin_url('product', ['slug' => $slug]);
        $src = $thumb !== '' ? ded_storefront_image_src($thumb, 'index') : '';

        echo '<a class="predictive-search__item h-stack align-center gap-5 sm:gap-6 decoration-none tap-area text-custom" ';
        echo 'href="' . ded_attr($href) . '">';
        if ($src !== '') {
            echo '<img src="' . ded_attr($src) . '" width="78" height="78" loading="lazy" decoding="async" alt="" ';
            echo 'class="rounded-xs shrink-0 object-cover" style="width:78px;height:78px">';
        } else {
            echo '<span class="skeleton skeleton--thumbnail shrink-0" style="width:78px;height:78px"></span>';
        }
        echo '<div class="v-stack gap-1 w-full justify-center text-start overflow-hidden">';
        echo '<span class="bold truncate">' . ded_h($title) . '</span>';
        echo '<span class="text-subdued text-sm">' . ded_h(ded_format_money($price, $cur)) . '</span>';
        echo '</div></a>';
    }
    echo '</div>';
}

echo '</div></body></html>';

echo ob_get_clean();

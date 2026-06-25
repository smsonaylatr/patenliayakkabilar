<?php

declare(strict_types=1);

if (!is_readable(__DIR__ . '/templates/arama.php')) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Şablon eksik: ded/templates/arama.php');
}

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/katalogdepo.php';
require_once __DIR__ . '/lib/vitrin.php';
require_once __DIR__ . '/lib/sepetsayfa.php';
require_once __DIR__ . '/lib/sablonyukle.php';
require_once __DIR__ . '/lib/sabloncalistir.php';

require_once __DIR__ . '/lib/seo.php';
$q = trim((string) ($_GET['q'] ?? ''));
ded_seo_set_context([
    'type' => 'page',
    'route' => 'search',
    'title' => $q !== '' ? ('Arama: ' . $q) : 'Arama',
    'description' => $q !== '' ? ('“' . $q . '” için arama sonuçları.') : 'Mağazada ürün arayın.',
    'url' => ded_seo_absolute_url(ded_vitrin_url('search')),
    'noindex' => true,
    'breadcrumbs' => [
        ['name' => 'Ana sayfa', 'url' => ded_vitrin_url('home')],
        ['name' => 'Arama', 'url' => ded_vitrin_url('search')],
    ],
]);

$html = ded_template_render('arama.php');
$html = ded_search_page_apply_db_results(ded_pdo(), $html, $q);
$html = ded_html_rewrite_context($html, 'search_root');
$html = ded_theme_inject_cart_core_global($html);

header('Content-Type: text/html; charset=utf-8');
echo ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links($html));

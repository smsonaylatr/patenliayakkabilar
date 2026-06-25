<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/katalogdepo.php';
require_once __DIR__ . '/lib/vitrin.php';
require_once __DIR__ . '/lib/sablonyukle.php';

if (!is_readable(__DIR__ . '/templates/index.php')) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Şablon eksik: ded/templates/index.php (HTML tasarımınızı bu dosyaya koyun veya kopyalayın).';
    exit;
}

if (!ded_pdo() || !ded_db_ready()) {
    header('Content-Type: text/html; charset=utf-8');
    $html = ded_template_render('index.php');
    $html = ded_html_rewrite_context($html, 'index');
    require_once __DIR__ . '/lib/sepetsayfa.php';
    require_once __DIR__ . '/lib/seo.php';
    ded_seo_set_context([
        'type' => 'home',
        'route' => 'home',
        'title' => '',
        'url' => rtrim(ded_storefront_public_url(), '/') . '/',
        'breadcrumbs' => [['name' => 'Ana sayfa', 'url' => ded_vitrin_url('home')]],
    ]);
    echo ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links(ded_theme_inject_cart_core_global($html)));
    exit;
}

$pdo = ded_pdo();
$html = ded_template_render('index.php');
$html = ded_storefront_patch_index($pdo, $html);

header('Content-Type: text/html; charset=utf-8');
echo ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links($html));

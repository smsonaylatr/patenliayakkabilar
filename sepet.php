<?php

declare(strict_types=1);

if (!is_readable(__DIR__ . '/templates/sepet.php')) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Şablon eksik: ded/templates/sepet.php');
}

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/vitrin.php';
require_once __DIR__ . '/lib/sepetsayfa.php';
require_once __DIR__ . '/lib/sablonyukle.php';

require_once __DIR__ . '/lib/seo.php';
ded_seo_set_context([
    'type' => 'page',
    'route' => 'cart',
    'title' => 'Sepet',
    'description' => 'Sepetinizdeki ürünler.',
    'url' => ded_seo_absolute_url(ded_vitrin_url('cart')),
    'noindex' => true,
    'breadcrumbs' => [
        ['name' => 'Ana sayfa', 'url' => ded_vitrin_url('home')],
        ['name' => 'Sepet', 'url' => ded_vitrin_url('cart')],
    ],
]);

$html = ded_template_render('sepet.php');
$html = ded_html_rewrite_context($html, 'cart_root');
$html = ded_cart_swap_main_section($html);
$html = ded_cart_append_page_scripts($html);

header('Content-Type: text/html; charset=utf-8');
echo ded_vitrin_finalize_document(ded_vitrin_rewrite_html_links($html));

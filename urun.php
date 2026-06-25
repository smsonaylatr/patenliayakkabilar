<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/sabloncalistir.php';

$slug = ded_vitrin_slug_from_request('slug');

if ($slug === '' || !ded_pdo() || !ded_db_ready()) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Ürün bulunamadı.');
}

$html = ded_product_page_render(ded_pdo(), $slug);
if ($html === null) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Ürün bulunamadı.');
}

header('Content-Type: text/html; charset=utf-8');
echo $html;

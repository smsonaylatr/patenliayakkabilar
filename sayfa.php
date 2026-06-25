<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/katalogdepo.php';
require_once __DIR__ . '/lib/sablonyukle.php';
require_once __DIR__ . '/lib/sabloncalistir.php';

$slug = ded_vitrin_slug_from_request('slug');
if ($slug === '') {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Sayfa bulunamadı.');
}

if (!ded_pdo() || !ded_db_ready()) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Veritabanı hazır değil.');
}

$html = ded_cms_page_render(ded_pdo(), $slug);
if ($html === null) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Sayfa bulunamadı.');
}

header('Content-Type: text/html; charset=utf-8');
echo $html;

<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/sabloncalistir.php';

if (!ded_pdo() || !ded_db_ready()) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Veritabanı hazır değil.');
}

$html = ded_collections_list_render(ded_pdo());
if ($html === null) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Şablon eksik: ded/templates/koleksiyonliste.php');
}

header('Content-Type: text/html; charset=utf-8');
echo $html;

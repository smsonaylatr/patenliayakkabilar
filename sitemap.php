<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/ekstra.php';

header('Content-Type: application/xml; charset=utf-8');

$pdo = ded_pdo();
if (!$pdo) {
    http_response_code(503);
    echo '<?xml version="1.0"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
    exit;
}

echo ded_sitemap_xml($pdo);

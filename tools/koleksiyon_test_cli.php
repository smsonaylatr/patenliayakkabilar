<?php

declare(strict_types=1);

chdir(dirname(__DIR__));

error_reporting(E_ALL);
ini_set('display_errors', '1');

$_GET['slug'] = $argv[1] ?? 'erkek-tekerlekli-ayakkabi';

require_once __DIR__ . '/../lib/bootstrap.php';
require_once __DIR__ . '/../lib/sabloncalistir.php';

$pdo = ded_pdo();
if (!$pdo instanceof PDO || !ded_db_ready()) {
    fwrite(STDERR, "DB hazır değil\n");
    exit(1);
}

$out = ded_collection_page_render($pdo, (string) $_GET['slug']);
if ($out === null) {
    fwrite(STDERR, "NULL html\n");
    exit(2);
}

echo strlen($out) > 800 ? substr($out, 0, 800) . '...' : $out;

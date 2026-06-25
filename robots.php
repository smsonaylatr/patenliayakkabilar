<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/ekstra.php';

header('Content-Type: text/plain; charset=utf-8');

$pdo = ded_pdo();
if (!$pdo) {
    echo "User-agent: *\nDisallow: /\n";
    exit;
}

echo ded_robots_txt($pdo);

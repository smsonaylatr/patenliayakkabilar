<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';
require_once dirname(__DIR__) . '/lib/sema.php';

$pdo = ded_pdo();
if (!$pdo) {
    echo "config.local.php yok veya DB bağlantısı kurulamadı.\n";
    exit(1);
}

try {
    ded_schema_ensure_shop($pdo);
    echo "Mağaza tabloları hazır.\n";
} catch (Throwable $e) {
    echo 'Hata: ' . $e->getMessage() . "\n";
    exit(1);
}

<?php

declare(strict_types=1);


require_once dirname(__DIR__) . '/lib/bootstrap.php';

$pdo = ded_pdo();
if (!$pdo) {
    fwrite(STDERR, "config.local.php / MySQL yok.\n");
    exit(1);
}

try {
    $pdo->exec(
        "ALTER TABLE ded_products ADD COLUMN audience VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'kiz|erkek|unisex|yetiskin' AFTER brand"
    );
    echo "audience sütunu eklendi.\n";
} catch (Throwable $e) {
    if (str_contains($e->getMessage(), '1060') || str_contains($e->getMessage(), 'Duplicate column')) {
        echo "audience zaten var.\n";
        exit(0);
    }
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

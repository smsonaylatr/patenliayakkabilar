<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';

$pdo = ded_pdo();
if (!$pdo) {
    fwrite(STDERR, "MySQL bağlantısı yok.\n");
    exit(1);
}

try {
    $pdo->exec(
        'ALTER TABLE ded_product_variants ADD COLUMN stock_qty INT NOT NULL DEFAULT 0 AFTER in_stock'
    );
    echo "stock_qty sütunu eklendi.\n";
} catch (Throwable $e) {
    if (str_contains($e->getMessage(), '1060') || str_contains($e->getMessage(), 'Duplicate')) {
        echo "stock_qty zaten var.\n";
        exit(0);
    }
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

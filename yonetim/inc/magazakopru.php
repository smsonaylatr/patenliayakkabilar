<?php declare(strict_types=1);

require_once __DIR__ . '/cekirdek.php';
require_once dirname(__DIR__, 2) . '/lib/magazadepo.php';

function yonetim_shop_pdo(): ?PDO
{
    $pdo = ded_pdo();
    return ($pdo && ded_shop_ready($pdo)) ? $pdo : null;
}

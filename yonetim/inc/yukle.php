<?php declare(strict_types=1);

require_once __DIR__ . '/cekirdek.php';
require_once __DIR__ . '/oturum.php';
require_once __DIR__ . '/rotalar.php';
require_once __DIR__ . '/yollar.php';
require_once __DIR__ . '/tema.php';
require_once __DIR__ . '/arayuz.php';
require_once __DIR__ . '/magazakopru.php';
require_once __DIR__ . '/katalog.php';

function yonetim_magaza_pdo(): ?PDO
{
    return yonetim_shop_pdo();
}

function yonetim_katalog_al(): ?array
{
    return yonetim_catalog_get();
}

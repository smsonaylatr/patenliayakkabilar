<?php declare(strict_types=1);

require_once __DIR__ . '/inc/yukle.php';
require_once dirname(__DIR__) . '/lib/magaza.php';

yonetim_require_login();
$pdo = yonetim_magaza_pdo();
if (!$pdo) {
    yonetim_redirect('orders');
}
$durum = isset($_GET['status']) ? (string) $_GET['status'] : '';
ded_siparis_csv($pdo, $durum !== '' ? $durum : null);

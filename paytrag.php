<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/magazadepo.php';
require_once __DIR__ . '/lib/paytr.php';

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '';

    exit;
}

$pdo = ded_pdo();
if (!$pdo || !ded_shop_ready($pdo)) {
    echo 'OK';

    exit;
}

$settings = ded_shop_settings_get($pdo);
if (!ded_paytr_configured($settings)) {
    echo 'OK';

    exit;
}

$post = [];
foreach ($_POST as $k => $v) {
    $post[(string) $k] = is_scalar($v) ? (string) $v : '';
}

$key = (string) ($settings['paytr_merchant_key'] ?? '');
$salt = (string) ($settings['paytr_merchant_salt'] ?? '');
if (!ded_paytr_verify_callback_hash($post, $key, $salt)) {
    echo '';

    exit;
}

if (($post['status'] ?? '') !== 'success') {
    echo 'OK';

    exit;
}

$orderId = ded_paytr_parse_order_id_from_oid((string) ($post['merchant_oid'] ?? ''));
if ($orderId === null) {
    echo 'OK';

    exit;
}

$amt = (int) ($post['total_amount'] ?? 0);
ded_order_mark_paid_paytr($pdo, $orderId, $amt);

echo 'OK';

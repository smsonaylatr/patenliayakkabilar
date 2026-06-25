<?php declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

$panelConfig = __DIR__ . '/panelconfig.php';
if (is_readable($panelConfig)) {
    require_once $panelConfig;
}

$dedRoot = dirname(__DIR__, 2);
require_once $dedRoot . '/lib/bootstrap.php';
require_once $dedRoot . '/lib/katalogdepo.php';

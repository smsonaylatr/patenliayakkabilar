<?php declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';
require_once dirname(__DIR__) . '/lib/kimlik.php';

ded_panel_auth_ensure();
ded_panel_auth_write([
    'password_hash' => password_hash('admin', PASSWORD_DEFAULT),
    'api_token' => null,
]);

echo "Tamam. Giriş şifresi: admin (API token sıfırlandı, yeniden giriş gerekir).\n";

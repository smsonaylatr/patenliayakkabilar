<?php

declare(strict_types=1);

define('DED_ROOT', dirname(__DIR__));
define('DED_CONFIG_FILE', DED_ROOT . '/config.local.php');
define('DED_SCHEMA_FILE', DED_ROOT . '/schema.sql');
define('DED_SEED_SQL_FILE', DED_ROOT . '/schema.sql');

function ded_install_sql_path(): ?string
{
    if (is_readable(DED_SEED_SQL_FILE)) {
        return DED_SEED_SQL_FILE;
    }
    if (is_readable(DED_SCHEMA_FILE)) {
        return DED_SCHEMA_FILE;
    }
    return null;
}

function ded_install_run_sql(PDO $pdo, string $sql): void
{
    $sql = (string) preg_replace('#^\s*--.*$#m', '', $sql);

    $sql = (string) preg_replace('#current_timestamp\s*\(\s*\)#i', 'CURRENT_TIMESTAMP', $sql);

    $sql = (string) preg_replace(
        '#\b(?:datetime|timestamp)(\s+(?:NOT\s+NULL|NULL))?(\s+DEFAULT\s+CURRENT_TIMESTAMP)(\s+ON\s+UPDATE\s+CURRENT_TIMESTAMP)?#i',
        'timestamp NULL DEFAULT CURRENT_TIMESTAMP$3',
        $sql
    );
    $sql = (string) preg_replace(
        '#\b(?:datetime|timestamp)(\s+(?:NOT\s+NULL|NULL))?\s+ON\s+UPDATE\s+CURRENT_TIMESTAMP#i',
        'timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP',
        $sql
    );

    $sql = (string) preg_replace_callback(
        '#(CREATE\s+TABLE\s+`ded_orders`\s*\((?:[^;]|\n)*?\)\s*ENGINE=)#i',
        static function (array $m): string {
            $block = $m[1];
            $block = (string) preg_replace('#`created_at`\s+[^,\n)]+#i', '`created_at` datetime NULL DEFAULT NULL', $block);
            $block = (string) preg_replace('#`updated_at`\s+[^,\n)]+#i', '`updated_at` datetime NULL DEFAULT NULL', $block);
            return $block;
        },
        $sql
    );

    $sql = (string) preg_replace('#`slug` varchar\(\d+\)#', '`slug` varchar(181)', $sql);
    $sql = (string) preg_replace('#`product_slug` varchar\(\d+\)#', '`product_slug` varchar(181)', $sql);
    $sql = (string) preg_replace('#`email` varchar\(\d+\)#', '`email` varchar(189)', $sql);
    $sql = (string) preg_replace('#(CREATE\s+TABLE\s+`ded_collections`\s*\(\s*`id`\s+varchar)\(\d+\)#', '${1}(170)', $sql);
    $sql = (string) preg_replace('#(`collection_id` varchar)\(\d+\)#', '${1}(170)', $sql);
    $sql = (string) preg_replace('#(CREATE\s+TABLE\s+`ded_media_meta`\s*\(\s*`path`\s+varchar)\(\d+\)#', '${1}(188)', $sql);
    $sql = (string) preg_replace('#(`target_type` varchar)\(\d+\)#', '${1}(10)', $sql);
    $sql = (string) preg_replace('#(`target_key` varchar)\(\d+\)#', '${1}(181)', $sql);

    $tables = [];
    if (preg_match_all('#CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`([^`]+)`#i', $sql, $m) > 0) {
        $tables = array_values(array_unique($m[1]));
    }

    $prev = $pdo->getAttribute(PDO::ATTR_ERRMODE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);
        try {
            $pdo->exec("SET SESSION explicit_defaults_for_timestamp = 1");
        } catch (Throwable) {
        }
        try {
            $pdo->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
        } catch (Throwable) {
        }
        try {
            $pdo->exec('SET SESSION innodb_large_prefix = 1');
            $pdo->exec('SET SESSION innodb_file_format = "Barracuda"');
            $pdo->exec('SET SESSION innodb_default_row_format = "DYNAMIC"');
        } catch (Throwable) {
        }

        if ($tables !== []) {
            try {
                $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
                foreach ($tables as $t) {
                    $safe = '`' . str_replace('`', '``', $t) . '`';
                    $pdo->exec('DROP TABLE IF EXISTS ' . $safe);
                }
            } finally {
                try {
                    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
                } catch (Throwable) {
                }
            }
        }

        $pdo->exec($sql);
    } finally {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, $prev);
    }
}

define('DATA_DIR', DED_ROOT . '/data');
define('UPLOAD_SUBDIR', 'cdn/shop/files/admin-uploads');

function ded_config(): ?array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    if (!is_readable(DED_CONFIG_FILE)) {
        $cached = null;
        return null;
    }
    $c = require DED_CONFIG_FILE;
    $cached = is_array($c) ? $c : null;
    return $cached;
}

function ded_pdo(): ?PDO
{
    static $pdo = false;
    if ($pdo !== false) {
        return $pdo;
    }
    $cfg = ded_config();
    if (!$cfg || empty($cfg['db'])) {
        $pdo = null;
        return null;
    }
    $d = $cfg['db'];
    $host = (string) ($d['host'] ?? '127.0.0.1');
    $port = (int) ($d['port'] ?? 3306);
    $name = (string) ($d['name'] ?? '');
    $user = (string) ($d['user'] ?? '');
    $pass = (string) ($d['pass'] ?? '');
    $charset = (string) ($d['charset'] ?? 'utf8mb4');
    if ($name === '') {
        $pdo = null;
        return null;
    }
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        try {
            require_once __DIR__ . '/sema.php';
            ded_schema_ensure($pdo);
        } catch (Throwable) {
        }
    } catch (Throwable) {
        $pdo = null;
    }
    return $pdo;
}

function ded_db_ready(): bool
{
    $pdo = ded_pdo();
    if (!$pdo) {
        return false;
    }
    try {
        $pdo->query('SELECT 1 FROM ded_site LIMIT 1');
        return true;
    } catch (Throwable) {
        return false;
    }
}

function ded_h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function ded_attr(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

require_once __DIR__ . '/para.php';
require_once __DIR__ . '/vitrinrotalar.php';

if (PHP_SAPI !== 'cli') {
    ded_maintenance_maybe_exit();
}

function ded_maintenance_maybe_exit(): void
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $script = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    if (str_contains($uri, '/yonetim/')
        || str_contains($uri, '/admin-api/')
        || in_array($script, ['paytrag.php', 'paytrdonus.php', 'sitemap.php', 'robots.php', 'bulten.php'], true)
    ) {
        return;
    }
    $pdo = ded_pdo();
    if (!$pdo) {
        return;
    }
    try {
        $pdo->query('SELECT 1 FROM ded_shop_settings LIMIT 1');
    } catch (Throwable) {
        return;
    }
    $row = $pdo->query('SELECT settings_json FROM ded_shop_settings WHERE id = 1 LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return;
    }
    $s = json_decode((string) ($row['settings_json'] ?? '{}'), true);
    if (!is_array($s) || empty($s['maintenance_mode'])) {
        return;
    }
    $msg = trim((string) ($s['maintenance_message'] ?? ''));
    if ($msg === '') {
        $msg = 'Mağazamız bakımda.';
    }
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    header('Retry-After: 3600');
    echo '<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Bakım</title><style>body{font-family:system-ui,sans-serif;display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;background:#f8fafc;color:#0f172a}';
    echo '.box{max-width:420px;padding:2rem;text-align:center}h1{font-size:1.5rem}</style></head><body>';
    echo '<div class="box"><h1>Bakım modu</h1><p>' . ded_h($msg) . '</p></div></body></html>';
    exit;
}

function ded_storefront_slug_from_request(string $raw): string
{
    $s = trim(rawurldecode($raw));
    if ($s === '' || str_contains($s, '..') || str_contains($s, '/') || str_contains($s, '\\')) {
        return '';
    }

    return $s;
}

function ded_asset_url(string $relative): string
{
    $relative = str_replace('\\', '/', $relative);
    return ltrim($relative, '/');
}

function ded_client_ip(): string
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return trim((string) $_SERVER['HTTP_CLIENT_IP']);
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']);

        return trim((string) ($parts[0] ?? ''));
    }

    return trim((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'));
}

function ded_storefront_public_url(): string
{
    $s = $_SERVER;
    $https = (!empty($s['HTTPS']) && (string) $s['HTTPS'] !== 'off')
        || (strtolower((string) ($s['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = (string) ($s['HTTP_HOST'] ?? 'localhost');
    $script = str_replace('\\', '/', (string) ($s['SCRIPT_NAME'] ?? ''));
    $dir = dirname($script);
    foreach (['/yonetim', '/storefront', '/tools'] as $suffix) {
        if (str_ends_with($dir, $suffix)) {
            $dir = substr($dir, 0, -strlen($suffix));
            break;
        }
    }

    return rtrim($scheme . '://' . $host . ($dir === '/' ? '' : $dir), '/');
}

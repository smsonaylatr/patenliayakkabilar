<?php declare(strict_types=1);

function ded_mysql_column_exists(PDO $pdo, string $table, string $column): bool
{
    try {
        $st = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $st->execute([$table, $column]);

        return (int) $st->fetchColumn() > 0;
    } catch (Throwable) {
        return false;
    }
}

function ded_panel_auth_ensure_table(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS ded_panel_auth (
          id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
          password_hash VARCHAR(255) NOT NULL,
          api_token VARCHAR(128) NULL,
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          CONSTRAINT chk_panel_auth_singleton CHECK (id = 1)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function ded_panel_auth_ensure_profile_columns(PDO $pdo): void
{
    if (!ded_mysql_column_exists($pdo, 'ded_panel_auth', 'username')) {
        $pdo->exec(
            "ALTER TABLE ded_panel_auth ADD COLUMN username VARCHAR(190) NOT NULL DEFAULT 'admin' AFTER api_token"
        );
    }
    if (!ded_mysql_column_exists($pdo, 'ded_panel_auth', 'email')) {
        $pdo->exec(
            "ALTER TABLE ded_panel_auth ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' AFTER username"
        );
    }
    if (!ded_mysql_column_exists($pdo, 'ded_panel_auth', 'avatar_path')) {
        $pdo->exec(
            'ALTER TABLE ded_panel_auth ADD COLUMN avatar_path VARCHAR(512) NULL AFTER email'
        );
    }
}

function ded_panel_auth_ensure(): void
{
    $pdo = ded_pdo();
    if (!$pdo) {
        throw new RuntimeException('Veritabanı bağlantısı yok. config.local.php dosyasını ve MySQL içe aktarımını (schema.sql) kontrol edin.');
    }
    ded_panel_auth_ensure_table($pdo);
    ded_panel_auth_ensure_profile_columns($pdo);
    $row = $pdo->query('SELECT id FROM ded_panel_auth WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $stmt = $pdo->prepare(
            'INSERT INTO ded_panel_auth (id, password_hash, api_token, username, email, avatar_path)
             VALUES (1, :h, NULL, :u, :e, NULL)'
        );
        $stmt->execute([
            'h' => password_hash('admin', PASSWORD_DEFAULT),
            'u' => 'admin',
            'e' => '',
        ]);
    }
}

function ded_panel_auth_read(): array
{
    ded_panel_auth_ensure();
    $pdo = ded_pdo();
    if (!$pdo) {
        return [
            'password_hash' => '',
            'api_token' => null,
            'username' => 'admin',
            'email' => '',
            'avatar_path' => null,
        ];
    }
    $row = $pdo->query(
        'SELECT password_hash, api_token, username, email, avatar_path FROM ded_panel_auth WHERE id = 1'
    )->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        return [
            'password_hash' => '',
            'api_token' => null,
            'username' => 'admin',
            'email' => '',
            'avatar_path' => null,
        ];
    }

    $av = $row['avatar_path'] ?? null;

    return [
        'password_hash' => (string) ($row['password_hash'] ?? ''),
        'api_token' => $row['api_token'] !== null ? (string) $row['api_token'] : null,
        'username' => (string) ($row['username'] ?? 'admin'),
        'email' => (string) ($row['email'] ?? ''),
        'avatar_path' => $av !== null && $av !== '' ? (string) $av : null,
    ];
}

function ded_panel_auth_write(array $a): void
{
    ded_panel_auth_ensure();
    $pdo = ded_pdo();
    if (!$pdo) {
        throw new RuntimeException('Veritabanı bağlantısı yok.');
    }
    $cur = ded_panel_auth_read();
    $hash = (string) ($a['password_hash'] ?? $cur['password_hash'] ?? '');
    $token = array_key_exists('api_token', $a) ? $a['api_token'] : ($cur['api_token'] ?? null);
    $username = trim((string) ($a['username'] ?? $cur['username'] ?? 'admin'));
    $email = trim((string) ($a['email'] ?? $cur['email'] ?? ''));
    $avatarPath = array_key_exists('avatar_path', $a) ? $a['avatar_path'] : ($cur['avatar_path'] ?? null);
    $avatarSql = $avatarPath !== null && $avatarPath !== '' ? (string) $avatarPath : null;

    $stmt = $pdo->prepare(
        'INSERT INTO ded_panel_auth (id, password_hash, api_token, username, email, avatar_path)
         VALUES (1, :h, :t, :u, :e, :av)
         ON DUPLICATE KEY UPDATE
           password_hash = VALUES(password_hash),
           api_token = VALUES(api_token),
           username = VALUES(username),
           email = VALUES(email),
           avatar_path = VALUES(avatar_path)'
    );
    $stmt->execute([
        'h' => $hash,
        't' => $token !== null && $token !== '' ? (string) $token : null,
        'u' => $username !== '' ? $username : 'admin',
        'e' => $email,
        'av' => $avatarSql,
    ]);
}

function ded_ensure_auth_file(): void
{
    ded_panel_auth_ensure();
}

function ded_read_auth(): array
{
    return ded_panel_auth_read();
}

function ded_write_auth(array $a): void
{
    ded_panel_auth_write($a);
}

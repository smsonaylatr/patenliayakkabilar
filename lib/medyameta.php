<?php declare(strict_types=1);

function ded_media_meta_ensure_table(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS ded_media_meta (
          path VARCHAR(188) NOT NULL PRIMARY KEY,
          alt VARCHAR(255) NOT NULL DEFAULT '',
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

function ded_media_meta_all(): array
{
    $pdo = ded_pdo();
    if (!$pdo) {
        return [];
    }
    $rows = [];
    try {
        ded_media_meta_ensure_table($pdo);
        $rows = $pdo->query('SELECT path, alt FROM ded_media_meta')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        $p = (string) ($row['path'] ?? '');
        if ($p !== '') {
            $out[$p] = ['alt' => (string) ($row['alt'] ?? '')];
        }
    }

    return $out;
}

function ded_media_meta_save(string $rel, string $alt): void
{
    $pdo = ded_pdo();
    if (!$pdo) {
        throw new RuntimeException('Veritabanı bağlantısı yok.');
    }
    ded_media_meta_ensure_table($pdo);
    $rel = trim($rel);
    if ($rel === '') {
        throw new InvalidArgumentException('Geçersiz dosya.');
    }
    $alt = trim($alt);
    if ($alt === '') {
        $pdo->prepare('DELETE FROM ded_media_meta WHERE path = :p')->execute(['p' => $rel]);
        return;
    }
    $pdo->prepare(
        'INSERT INTO ded_media_meta (path, alt) VALUES (:p, :a)
         ON DUPLICATE KEY UPDATE alt = VALUES(alt)'
    )->execute(['p' => $rel, 'a' => $alt]);
}

function ded_media_meta_remove(string $rel): void
{
    $pdo = ded_pdo();
    if (!$pdo) {
        return;
    }
    ded_media_meta_ensure_table($pdo);
    $rel = trim($rel);
    if ($rel === '') {
        return;
    }
    $pdo->prepare('DELETE FROM ded_media_meta WHERE path = :p OR path LIKE :like')->execute([
        'p' => $rel,
        'like' => '%' . basename($rel),
    ]);
}

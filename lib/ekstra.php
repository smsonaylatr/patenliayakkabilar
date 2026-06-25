<?php

declare(strict_types=1);

require_once __DIR__ . '/magazadepo.php';

function ded_extras_schema_ensure(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS ded_newsletter_subscribers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL DEFAULT '',
  active TINYINT(1) NOT NULL DEFAULT 1,
  source VARCHAR(64) NOT NULL DEFAULT 'footer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_nl_email (email),
  KEY idx_nl_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_faq (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  question VARCHAR(512) NOT NULL,
  answer MEDIUMTEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_faq_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_product_reviews (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  product_slug VARCHAR(181) NOT NULL,
  author_name VARCHAR(255) NOT NULL,
  author_email VARCHAR(255) NOT NULL DEFAULT '',
  rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
  body TEXT NOT NULL,
  approved TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_rev_slug (product_slug),
  KEY idx_rev_approved (approved),
  KEY idx_rev_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    $prev = $pdo->getAttribute(PDO::ATTR_ERRMODE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);
        $pdo->exec($sql);
    } catch (Throwable) {
    } finally {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, $prev);
    }
}

function ded_newsletter_subscribe(PDO $pdo, string $email, string $name = '', string $source = 'footer'): array
{
    ded_extras_schema_ensure($pdo);
    $email = strtolower(trim($email));
    $name = trim($name);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Geçerli bir e-posta girin.'];
    }
    $stmt = $pdo->prepare(
        'INSERT INTO ded_newsletter_subscribers (email, name, active, source)
         VALUES (?, ?, 1, ?)
         ON DUPLICATE KEY UPDATE name = IF(VALUES(name) <> "", VALUES(name), name), active = 1'
    );
    $stmt->execute([$email, $name, $source]);

    return ['ok' => true, 'message' => 'Kayıt alındı.'];
}

function ded_newsletter_list(PDO $pdo, int $limit = 200, int $offset = 0, string $q = ''): array
{
    ded_extras_schema_ensure($pdo);
    $limit = max(1, min(500, $limit));
    $offset = max(0, $offset);
    $where = 'WHERE 1=1';
    $params = [];
    $q = trim($q);
    if ($q !== '') {
        $where .= ' AND (email LIKE ? OR name LIKE ?)';
        $like = '%' . $q . '%';
        $params = [$like, $like];
    }
    $stmtCnt = $pdo->prepare("SELECT COUNT(*) FROM ded_newsletter_subscribers {$where}");
    $stmtCnt->execute($params);
    $total = (int) $stmtCnt->fetchColumn();
    $sql = "SELECT * FROM ded_newsletter_subscribers {$where} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return ['total' => $total, 'items' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
}

function ded_newsletter_set_active(PDO $pdo, int $id, bool $active): void
{
    ded_extras_schema_ensure($pdo);
    $pdo->prepare('UPDATE ded_newsletter_subscribers SET active = ? WHERE id = ?')->execute([$active ? 1 : 0, $id]);
}

function ded_newsletter_delete(PDO $pdo, int $id): void
{
    ded_extras_schema_ensure($pdo);
    $pdo->prepare('DELETE FROM ded_newsletter_subscribers WHERE id = ?')->execute([$id]);
}

function ded_faq_list(PDO $pdo, bool $activeOnly = false): array
{
    ded_extras_schema_ensure($pdo);
    $sql = 'SELECT * FROM ded_faq';
    if ($activeOnly) {
        $sql .= ' WHERE active = 1';
    }
    $sql .= ' ORDER BY sort_order ASC, id ASC';
    $stmt = $pdo->query($sql);

    return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

function ded_faq_get(PDO $pdo, int $id): ?array
{
    ded_extras_schema_ensure($pdo);
    $stmt = $pdo->prepare('SELECT * FROM ded_faq WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);

    return $r ?: null;
}

function ded_faq_save(PDO $pdo, ?int $id, string $question, string $answer, int $sortOrder, bool $active): int
{
    ded_extras_schema_ensure($pdo);
    if ($id !== null && $id > 0) {
        $pdo->prepare(
            'UPDATE ded_faq SET question = ?, answer = ?, sort_order = ?, active = ? WHERE id = ?'
        )->execute([$question, $answer, $sortOrder, $active ? 1 : 0, $id]);

        return $id;
    }
    $pdo->prepare(
        'INSERT INTO ded_faq (question, answer, sort_order, active) VALUES (?, ?, ?, ?)'
    )->execute([$question, $answer, $sortOrder, $active ? 1 : 0]);

    return (int) $pdo->lastInsertId();
}

function ded_faq_delete(PDO $pdo, int $id): void
{
    ded_extras_schema_ensure($pdo);
    $pdo->prepare('DELETE FROM ded_faq WHERE id = ?')->execute([$id]);
}

function ded_review_submit(PDO $pdo, string $slug, string $name, string $email, int $rating, string $body): array
{
    ded_extras_schema_ensure($pdo);
    $slug = trim($slug);
    $name = trim($name);
    $body = trim($body);
    $rating = max(1, min(5, $rating));
    if ($slug === '' || $name === '' || $body === '') {
        return ['ok' => false, 'message' => 'Ürün, ad ve yorum zorunludur.'];
    }
    if (mb_strlen($body) < 10) {
        return ['ok' => false, 'message' => 'Yorum en az 10 karakter olmalı.'];
    }
    $email = strtolower(trim($email));
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Geçersiz e-posta.'];
    }
    $pdo->prepare(
        'INSERT INTO ded_product_reviews (product_slug, author_name, author_email, rating, body, approved)
         VALUES (?, ?, ?, ?, ?, 0)'
    )->execute([$slug, $name, $email, $rating, $body]);

    return ['ok' => true, 'message' => 'Gönderildi.'];
}

function ded_reviews_list(PDO $pdo, int $limit = 100, ?bool $approved = null, string $slug = ''): array
{
    ded_extras_schema_ensure($pdo);
    $limit = max(1, min(500, $limit));
    $where = 'WHERE 1=1';
    $params = [];
    if ($approved !== null) {
        $where .= ' AND approved = ?';
        $params[] = $approved ? 1 : 0;
    }
    $slug = trim($slug);
    if ($slug !== '') {
        $where .= ' AND product_slug = ?';
        $params[] = $slug;
    }
    $stmtCnt = $pdo->prepare("SELECT COUNT(*) FROM ded_product_reviews {$where}");
    $stmtCnt->execute($params);
    $total = (int) $stmtCnt->fetchColumn();
    $sql = "SELECT * FROM ded_product_reviews {$where} ORDER BY id DESC LIMIT {$limit}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return ['total' => $total, 'items' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
}

function ded_reviews_for_product(PDO $pdo, string $slug, int $limit = 20): array
{
    $data = ded_reviews_list($pdo, $limit, true, $slug);

    return $data['items'];
}

function ded_review_set_approved(PDO $pdo, int $id, bool $approved): void
{
    ded_extras_schema_ensure($pdo);
    $pdo->prepare('UPDATE ded_product_reviews SET approved = ? WHERE id = ?')->execute([$approved ? 1 : 0, $id]);
}

function ded_review_delete(PDO $pdo, int $id): void
{
    ded_extras_schema_ensure($pdo);
    $pdo->prepare('DELETE FROM ded_product_reviews WHERE id = ?')->execute([$id]);
}

function ded_sitemap_urls(PDO $pdo): array
{
    require_once __DIR__ . '/katalogdepo.php';
    require_once __DIR__ . '/seo.php';
    $base = rtrim(ded_storefront_public_url(), '/');
    $today = gmdate('Y-m-d');
    $urls = [
        ['loc' => $base . '/', 'lastmod' => $today],
        ['loc' => $base . '/koleksiyonlar', 'lastmod' => $today],
    ];
    if (!ded_db_ready()) {
        return $urls;
    }

    ded_seo_ensure_overrides_schema($pdo);
    $noindex = [];
    try {
        foreach ($pdo->query('SELECT target_type, target_key FROM ded_seo_overrides WHERE noindex = 1')->fetchAll() as $r) {
            $noindex[((string) $r['target_type']) . ':' . ((string) $r['target_key'])] = true;
        }
    } catch (Throwable) {
    }

    $cat = ded_catalog_fetch($pdo);
    foreach ($cat['products'] ?? [] as $p) {
        if (!is_array($p)) {
            continue;
        }
        $slug = (string) ($p['slug'] ?? '');
        if ($slug === '' || isset($noindex['product:' . $slug])) {
            continue;
        }
        $imgs = [];
        foreach ((array) ($p['images'] ?? []) as $im) {
            $im = trim((string) $im);
            if ($im === '') {
                continue;
            }
            $imgs[] = ded_seo_absolute_url($im);
            if (count($imgs) >= 5) {
                break;
            }
        }
        $urls[] = [
            'loc' => $base . '/urun/' . rawurlencode($slug),
            'lastmod' => $today,
            'images' => $imgs,
        ];
    }
    foreach ($cat['collections'] ?? [] as $c) {
        if (!is_array($c)) {
            continue;
        }
        $id = (string) ($c['id'] ?? '');
        if ($id === '' || isset($noindex['collection:' . $id])) {
            continue;
        }
        $imgs = [];
        $im = trim((string) ($c['image'] ?? ''));
        if ($im !== '') {
            $imgs[] = ded_seo_absolute_url($im);
        }
        $urls[] = [
            'loc' => $base . '/koleksiyon/' . rawurlencode($id),
            'lastmod' => $today,
            'images' => $imgs,
        ];
    }
    foreach ($cat['pages'] ?? [] as $pg) {
        if (!is_array($pg)) {
            continue;
        }
        $slug = (string) ($pg['slug'] ?? '');
        if ($slug === '' || isset($noindex['page:' . $slug])) {
            continue;
        }
        $urls[] = ['loc' => $base . '/sayfa/' . rawurlencode($slug), 'lastmod' => $today];
    }

    return $urls;
}

function ded_sitemap_xml(PDO $pdo): string
{
    $urls = ded_sitemap_urls($pdo);
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
    foreach ($urls as $u) {
        $xml .= '  <url><loc>' . htmlspecialchars((string) $u['loc'], ENT_XML1) . '</loc>';
        $xml .= '<lastmod>' . htmlspecialchars((string) $u['lastmod'], ENT_XML1) . '</lastmod>';
        foreach ((array) ($u['images'] ?? []) as $img) {
            $img = trim((string) $img);
            if ($img === '') {
                continue;
            }
            $xml .= '<image:image><image:loc>' . htmlspecialchars($img, ENT_XML1) . '</image:loc></image:image>';
        }
        $xml .= '</url>' . "\n";
    }
    $xml .= '</urlset>';

    return $xml;
}

function ded_robots_txt(PDO $pdo): string
{
    $s = ded_shop_ready($pdo) ? ded_shop_settings_get($pdo) : [];
    $base = rtrim(ded_storefront_public_url(), '/');
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /yonetim/',
        'Disallow: /admin-api/',
        'Disallow: /data/',
        'Disallow: /tools/',
        'Disallow: /sepet',
        'Disallow: /sepet/',
        'Disallow: /odeme',
        'Disallow: /odeme/',
        'Disallow: /arama',
        'Disallow: /arama?*',
        'Disallow: /siparis-basarili',
        'Disallow: /*?q=',
        'Disallow: /*?utm_*',
        'Sitemap: ' . $base . '/sitemap.xml',
    ];
    $extra = trim((string) ($s['robots_txt_extra'] ?? ''));
    if ($extra !== '') {
        $lines[] = '';
        $lines[] = $extra;
    }

    return implode("\n", $lines) . "\n";
}

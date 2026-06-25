<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function ded_product_audience_normalize(?string $raw): string
{
    $s = trim((string) $raw);
    if ($s === '' || $s === 'genel') {
        return '';
    }
    $ok = ['kiz', 'erkek', 'unisex', 'yetiskin'];

    return in_array($s, $ok, true) ? $s : '';
}

function ded_product_audience_label(string $code): string
{
    return match ($code) {
        'kiz' => 'Kız çocuk',
        'erkek' => 'Erkek çocuk',
        'unisex' => 'Unisex',
        'yetiskin' => 'Yetişkin',
        default => '',
    };
}

function ded_product_audience_form_options(): array
{
    return [
        ['value' => '', 'label' => 'Genel (seçilmedi)'],
        ['value' => 'kiz', 'label' => 'Kız çocuk'],
        ['value' => 'erkek', 'label' => 'Erkek çocuk'],
        ['value' => 'unisex', 'label' => 'Unisex'],
        ['value' => 'yetiskin', 'label' => 'Yetişkin'],
    ];
}

function ded_catalog_fetch(PDO $pdo): array
{
    ded_pages_ensure_source_html_column($pdo);
    ded_catalog_ensure_product_compare_at_price_column($pdo);
    $siteRow = $pdo->query('SELECT * FROM ded_site WHERE id = 1')->fetch();
    if (!$siteRow) {
        $siteRow = [];
    }
    $site = [
        'name' => $siteRow['name'] ?? '',
        'title' => $siteRow['title'] ?? '',
        'description' => $siteRow['description'] ?? '',
        'homeCollectionHeading' => $siteRow['home_collection_heading'] ?? '',
        'homeCollectionSubtext' => $siteRow['home_collection_subtext'] ?? '',
        'homeCollectionMoreUrl' => $siteRow['home_collection_more_url'] ?? '#',
    ];

    $products = [];
    $prodStmt = $pdo->query(
        'SELECT * FROM ded_products ORDER BY sort_order ASC, id ASC'
    );
    foreach ($prodStmt->fetchAll() as $p) {
        $id = (int) $p['id'];
        $imgs = $pdo->prepare(
            'SELECT path FROM ded_product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC'
        );
        $imgs->execute([$id]);
        $images = array_column($imgs->fetchAll(), 'path');

        $vars = $pdo->prepare(
            'SELECT name, price, currency, sku, in_stock, stock_qty FROM ded_product_variants WHERE product_id = ? ORDER BY sort_order ASC, id ASC'
        );
        $vars->execute([$id]);
        $variants = [];
        foreach ($vars->fetchAll() as $v) {
            $qty = (int) ($v['stock_qty'] ?? 0);
            $variants[] = [
                'name' => $v['name'],
                'price' => (float) $v['price'],
                'currency' => $v['currency'],
                'sku' => $v['sku'],
                'inStock' => (bool) $v['in_stock'],
                'stockQty' => $qty,
            ];
        }

        $capPull = ded_product_compare_at_from_row($p);

        $entry = [
            'id' => (string) ($p['external_id'] ?: $p['id']),
            'slug' => $p['slug'],
            'title' => $p['title'],
            'description' => (string) $p['description'],
            'brand' => $p['brand'],
            'audience' => ded_product_audience_normalize($p['audience'] ?? null),
            'price' => (float) $p['price'],
            'currency' => $p['currency'],
            'sortOrder' => (int) ($p['sort_order'] ?? 0),
            'images' => $images,
            'variants' => $variants,
            'sourceHtml' => '',
        ];
        if ($capPull !== null) {
            $entry['compareAtPrice'] = $capPull;
        }
        $products[] = $entry;
    }

    $collections = [];
    $colStmt = $pdo->query('SELECT * FROM ded_collections ORDER BY sort_order ASC, id ASC');
    foreach ($colStmt->fetchAll() as $c) {
        $cid = $c['id'];
        $ps = $pdo->prepare(
            'SELECT pr.slug FROM ded_collection_products cp
             JOIN ded_products pr ON pr.id = cp.product_id
             WHERE cp.collection_id = ?
             ORDER BY cp.sort_order ASC'
        );
        $ps->execute([$cid]);
        $slugs = array_column($ps->fetchAll(), 'slug');

        $collections[] = [
            'id' => $cid,
            'title' => $c['title'],
            'description' => (string) $c['description'],
            'image' => (string) $c['image_path'],
            'productSlugs' => $slugs,
            'sourceHtml' => '',
        ];
    }

    $pages = [];
    foreach ($pdo->query('SELECT * FROM ded_pages ORDER BY slug ASC')->fetchAll() as $pg) {
        $pages[] = [
            'slug' => $pg['slug'],
            'title' => $pg['title'],
            'description' => (string) $pg['description'],
            'sourceHtml' => (string) ($pg['source_html'] ?? ''),
        ];
    }

    return [
        'version' => 1,
        'site' => $site,
        'products' => $products,
        'collections' => $collections,
        'pages' => $pages,
    ];
}

function ded_catalog_ensure_products_in_collections(PDO $pdo): void
{
    $firstCol = $pdo->query(
        'SELECT id FROM ded_collections ORDER BY sort_order ASC, id ASC LIMIT 1'
    )->fetchColumn();
    if ($firstCol === false || $firstCol === '') {
        return;
    }
    $cid = (string) $firstCol;

    $orphans = $pdo->query(
        'SELECT p.id FROM ded_products p
         LEFT JOIN ded_collection_products cp ON cp.product_id = p.id
         WHERE cp.product_id IS NULL'
    )->fetchAll(PDO::FETCH_COLUMN);

    if ($orphans === []) {
        return;
    }

    $stMax = $pdo->prepare(
        'SELECT COALESCE(MAX(sort_order), -1) FROM ded_collection_products WHERE collection_id = ?'
    );
    $stMax->execute([$cid]);
    $maxSort = (int) $stMax->fetchColumn();

    $ins = $pdo->prepare(
        'INSERT INTO ded_collection_products (collection_id, product_id, sort_order) VALUES (?, ?, ?)'
    );
    foreach ($orphans as $pid) {
        $maxSort++;
        $ins->execute([$cid, (int) $pid, $maxSort]);
    }
}

function ded_catalog_ensure_variant_stock_qty_column(PDO $pdo): void
{
    try {
        $pdo->query('SELECT stock_qty FROM ded_product_variants LIMIT 1');
    } catch (Throwable) {
        try {
            $pdo->exec(
                'ALTER TABLE ded_product_variants ADD COLUMN stock_qty INT NOT NULL DEFAULT 0 AFTER in_stock'
            );
        } catch (Throwable) {
        }
    }
}

function ded_pages_ensure_source_html_column(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    try {
        $col = $pdo->query("SHOW COLUMNS FROM ded_pages LIKE 'source_html'")->fetch(PDO::FETCH_ASSOC);
        if ($col !== false && stripos((string) ($col['Type'] ?? ''), 'varchar') !== false) {
            $pdo->exec('ALTER TABLE ded_pages MODIFY source_html LONGTEXT NULL');
        }
    } catch (Throwable) {
    }
}

function ded_catalog_ensure_product_audience_column(PDO $pdo): void
{
    try {
        $pdo->query('SELECT audience FROM ded_products LIMIT 1');
    } catch (Throwable) {
        try {
            $pdo->exec(
                "ALTER TABLE ded_products ADD COLUMN audience VARCHAR(32) NOT NULL DEFAULT '' "
                . "COMMENT 'kiz|erkek|unisex|yetiskin' AFTER brand"
            );
        } catch (Throwable) {
        }
    }
}

function ded_catalog_ensure_product_compare_at_price_column(PDO $pdo): void
{
    try {
        $pdo->query('SELECT compare_at_price FROM ded_products LIMIT 1');
    } catch (Throwable) {
        try {
            $pdo->exec(
                'ALTER TABLE ded_products ADD COLUMN compare_at_price DECIMAL(12,2) NULL DEFAULT NULL '
                . 'COMMENT \'isteğe bağlı liste fiyatı\' AFTER price'
            );
        } catch (Throwable) {
        }
    }
}

function ded_catalog_save(PDO $pdo, array $catalog): void
{
    if (
        !isset($catalog['site'], $catalog['products'], $catalog['collections'], $catalog['pages'])
        || !is_array($catalog['products'])
        || !is_array($catalog['collections'])
        || !is_array($catalog['pages'])
    ) {
        throw new InvalidArgumentException('invalid_catalog');
    }

    ded_catalog_ensure_product_audience_column($pdo);
    ded_catalog_ensure_variant_stock_qty_column($pdo);
    ded_catalog_ensure_product_compare_at_price_column($pdo);

    $s = $catalog['site'];
    $pdo->beginTransaction();
    try {
        $u = $pdo->prepare(
            'INSERT INTO ded_site (id, name, title, description, home_collection_heading, home_collection_subtext, home_collection_more_url)
             VALUES (1, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE name = VALUES(name), title = VALUES(title), description = VALUES(description),
             home_collection_heading = VALUES(home_collection_heading), home_collection_subtext = VALUES(home_collection_subtext),
             home_collection_more_url = VALUES(home_collection_more_url)'
        );
        $u->execute([
            (string) ($s['name'] ?? ''),
            (string) ($s['title'] ?? ''),
            (string) ($s['description'] ?? ''),
            (string) ($s['homeCollectionHeading'] ?? ''),
            (string) ($s['homeCollectionSubtext'] ?? ''),
            (string) ($s['homeCollectionMoreUrl'] ?? '#'),
        ]);

        $pdo->exec('DELETE FROM ded_collection_products');
        $pdo->exec('DELETE FROM ded_product_variants');
        $pdo->exec('DELETE FROM ded_product_images');
        $pdo->exec('DELETE FROM ded_products');
        $pdo->exec('DELETE FROM ded_collections');
        $pdo->exec('DELETE FROM ded_pages');

        $insP = $pdo->prepare(
            'INSERT INTO ded_products (external_id, slug, title, description, brand, audience, price, compare_at_price, currency, source_html, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $insImg = $pdo->prepare(
            'INSERT INTO ded_product_images (product_id, path, sort_order) VALUES (?, ?, ?)'
        );
        $insV = $pdo->prepare(
            'INSERT INTO ded_product_variants (product_id, name, price, currency, sku, in_stock, stock_qty, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $slugToId = [];
        $order = 0;
        foreach ($catalog['products'] as $p) {
            $slug = (string) ($p['slug'] ?? '');
            if ($slug === '') {
                continue;
            }
            $ext = (string) ($p['id'] ?? '');
            $sortOrd = array_key_exists('sortOrder', $p)
                ? (int) $p['sortOrder']
                : $order;
            $pvPrice = (float) ($p['price'] ?? 0);
            $capDb = ded_product_compare_at_from_catalog_product($p);
            $variantsList = $p['variants'] ?? [];
            if ($pvPrice <= 0 && is_array($variantsList)) {
                foreach ($variantsList as $v) {
                    $vp = (float) ($v['price'] ?? 0);
                    if ($vp > 0) {
                        $pvPrice = $vp;
                        break;
                    }
                }
            }
            $insP->execute([
                $ext !== '' ? $ext : null,
                $slug,
                (string) ($p['title'] ?? ''),
                (string) ($p['description'] ?? ''),
                (string) ($p['brand'] ?? ''),
                ded_product_audience_normalize($p['audience'] ?? null),
                $pvPrice,
                $capDb !== null ? $capDb : null,
                (string) ($p['currency'] ?? 'TRY'),
                '',
                $sortOrd,
            ]);
            $order++;
            $pid = (int) $pdo->lastInsertId();
            $slugToId[$slug] = $pid;

            $imOr = 0;
            foreach ($p['images'] ?? [] as $path) {
                $path = (string) $path;
                if ($path === '') {
                    continue;
                }
                $insImg->execute([$pid, $path, $imOr++]);
            }

            $vOr = 0;
            foreach ($p['variants'] ?? [] as $v) {
                $stockQty = max(0, (int) ($v['stockQty'] ?? 0));
                $inStock = array_key_exists('inStock', $v)
                    ? !empty($v['inStock'])
                    : ($stockQty > 0);
                if ($stockQty > 0) {
                    $inStock = true;
                }
                $insV->execute([
                    $pid,
                    (string) ($v['name'] ?? ''),
                    (float) ($v['price'] ?? 0),
                    (string) ($v['currency'] ?? 'TRY'),
                    isset($v['sku']) && $v['sku'] !== '' ? (string) $v['sku'] : null,
                    $inStock ? 1 : 0,
                    $stockQty,
                    $vOr++,
                ]);
            }
        }

        $insC = $pdo->prepare(
            'INSERT INTO ded_collections (id, title, description, image_path, source_html, sort_order) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $insCp = $pdo->prepare(
            'INSERT INTO ded_collection_products (collection_id, product_id, sort_order) VALUES (?, ?, ?)'
        );

        $cOr = 0;
        foreach ($catalog['collections'] as $c) {
            $cid = (string) ($c['id'] ?? '');
            if ($cid === '') {
                continue;
            }
            $insC->execute([
                $cid,
                (string) ($c['title'] ?? ''),
                (string) ($c['description'] ?? ''),
                (string) ($c['image'] ?? ''),
                '',
                $cOr++,
            ]);
            $pOr = 0;
            foreach ($c['productSlugs'] ?? [] as $pslug) {
                $pslug = (string) $pslug;
                if (!isset($slugToId[$pslug])) {
                    continue;
                }
                $insCp->execute([$cid, $slugToId[$pslug], $pOr++]);
            }
        }

        $insPg = $pdo->prepare(
            'INSERT INTO ded_pages (slug, title, description, source_html) VALUES (?, ?, ?, ?)'
        );
        foreach ($catalog['pages'] as $pg) {
            $slug = (string) ($pg['slug'] ?? '');
            if ($slug === '') {
                continue;
            }
            $insPg->execute([
                $slug,
                (string) ($pg['title'] ?? ''),
                (string) ($pg['description'] ?? ''),
                (string) ($pg['sourceHtml'] ?? ''),
            ]);
        }

        ded_catalog_ensure_products_in_collections($pdo);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function ded_product_effective_sale_price(array $productRow, array $variantRows): float
{
    $base = (float) ($productRow['price'] ?? 0);

    foreach ($variantRows as $v) {
        $name = trim((string) ($v['name'] ?? ''));
        if ($name === '') {
            continue;
        }
        $pv = (float) ($v['price'] ?? 0);
        if ($pv > 0) {
            return $pv;
        }
    }

    foreach ($variantRows as $v) {
        $pv = (float) ($v['price'] ?? 0);
        if ($pv > 0) {
            return $pv;
        }
    }

    return max(0.0, $base);
}

function ded_products_search_quick(PDO $pdo, string $q, int $limit = 10): array
{
    $term = trim($q);
    if ($term === '') {
        return [];
    }
    $limit = max(1, min($limit, 120));

    $sql = <<<SQL
SELECT p.*, (
        SELECT pi.path FROM ded_product_images pi
        WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC, pi.id ASC LIMIT 1
    ) AS _ded_thumb
FROM ded_products p
WHERE
    LOCATE(?, COALESCE(p.title, '')) > 0
    OR LOCATE(?, COALESCE(p.slug, '')) > 0
    OR LOCATE(?, COALESCE(p.brand, '')) > 0
    OR LOCATE(?, COALESCE(p.description, '')) > 0
    OR EXISTS (
        SELECT 1 FROM ded_product_variants v
        WHERE v.product_id = p.id
        AND (LOCATE(?, COALESCE(v.sku, '')) > 0 OR LOCATE(?, COALESCE(v.name, '')) > 0)
    )
ORDER BY p.sort_order ASC, p.id DESC
LIMIT {$limit}
SQL;

    $vals = [$term, $term, $term, $term, $term, $term];
    $st = $pdo->prepare($sql);
    $st->execute($vals);

    return $st->fetchAll() ?: [];
}

function ded_product_by_slug(PDO $pdo, string $slug): ?array
{
    $st = $pdo->prepare('SELECT * FROM ded_products WHERE slug = ? LIMIT 1');
    $st->execute([$slug]);
    $p = $st->fetch();
    if (!$p) {
        return null;
    }
    $id = (int) $p['id'];
    $imgs = $pdo->prepare(
        'SELECT path FROM ded_product_images WHERE product_id = ? ORDER BY sort_order ASC'
    );
    $imgs->execute([$id]);
    $images = array_column($imgs->fetchAll(), 'path');
    $vars = $pdo->prepare(
        'SELECT * FROM ded_product_variants WHERE product_id = ? ORDER BY sort_order ASC'
    );
    $vars->execute([$id]);
    return [
        'row' => $p,
        'images' => $images,
        'variants' => $vars->fetchAll(),
    ];
}

function ded_collection_by_id(PDO $pdo, string $id): ?array
{
    $st = $pdo->prepare('SELECT * FROM ded_collections WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $c = $st->fetch();
    if (!$c) {
        return null;
    }
    $st = $pdo->prepare(
        'SELECT p.* FROM ded_products p
         INNER JOIN ded_collection_products cp ON cp.product_id = p.id
         WHERE cp.collection_id = ?
         ORDER BY cp.sort_order ASC, p.id ASC'
    );
    $st->execute([$id]);
    return ['row' => $c, 'products' => $st->fetchAll()];
}

function ded_site_row(PDO $pdo): array
{
    $r = $pdo->query('SELECT * FROM ded_site WHERE id = 1')->fetch();
    return $r ?: [];
}

function ded_collections_all(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM ded_collections ORDER BY sort_order ASC, id ASC')->fetchAll();
}

function ded_cms_page_by_slug(PDO $pdo, string $slug): ?array
{
    $st = $pdo->prepare('SELECT * FROM ded_pages WHERE slug = ? LIMIT 1');
    $st->execute([$slug]);
    $row = $st->fetch();
    return $row ?: null;
}

const DED_LISTING_ALLOWED_SORT_VALUES = [
    'manual',
    'most-relevant',
    'best-selling',
    'title-ascending',
    'title-descending',
    'price-ascending',
    'price-descending',
    'created-ascending',
    'created-descending',
];

function ded_listing_q_val(array $q, string ...$keys): mixed
{
    foreach ($keys as $k) {
        if (array_key_exists($k, $q)) {
            return $q[$k];
        }
    }

    return null;
}

function ded_listing_parse_controls(?array $q = null): array
{
    $q ??= $_GET;

    $sort = strtolower(trim((string) ded_listing_q_val($q, 'sort_by')));
    if (!in_array($sort, DED_LISTING_ALLOWED_SORT_VALUES, true)) {
        $sort = 'manual';
    }

    $inStockRaw = ded_listing_q_val($q, 'filter_v_availability', 'filter.v.availability');
    $inStockOnly = $inStockRaw !== null && (string) $inStockRaw === '1';

    $gteRaw = ded_listing_q_val($q, 'filter_v_price_gte', 'filter.v.price.gte');
    $lteRaw = ded_listing_q_val($q, 'filter_v_price_lte', 'filter.v.price.lte');

    $priceGte = $gteRaw === null || $gteRaw === '' ? null : (is_numeric((string) $gteRaw) ? (float) $gteRaw : null);

    $priceLte = $lteRaw === null || $lteRaw === '' ? null : (is_numeric((string) $lteRaw) ? (float) $lteRaw : null);
    if ($priceGte !== null && $priceGte < 0) {
        $priceGte = 0.0;
    }
    if ($priceLte !== null && $priceLte < 0) {
        $priceLte = 0.0;
    }

    return [
        'sort' => $sort,
        'in_stock_only' => $inStockOnly,
        'price_gte' => $priceGte,
        'price_lte' => $priceLte,
    ];
}

function ded_listing_sales_totals_by_slug(PDO $pdo): array
{
    static $cache = [];
    static $loaded = false;
    if ($loaded) {
        return $cache;
    }
    try {
        $st = $pdo->query(
            'SELECT product_slug AS s, COALESCE(SUM(qty), 0) AS t FROM ded_order_items GROUP BY product_slug'
        );
        if ($st !== false) {
            while (($row = $st->fetch(PDO::FETCH_ASSOC)) !== false) {

                $cache[(string) ($row['s'] ?? '')] = (int) ($row['t'] ?? 0);
            }
        }
    } catch (Throwable) {
        $cache = [];
    }
    $loaded = true;

    return $cache;
}

function ded_listing_variant_saleable(array $v): bool
{
    return !empty($v['in_stock']) || ((int) ($v['stock_qty'] ?? 0) > 0);
}

function ded_listing_product_has_stock(?array $det): bool
{
    if ($det === null) {
        return false;
    }
    if (!isset($det['variants']) || !is_array($det['variants'])) {
        return false;
    }

    foreach ($det['variants'] as $v) {
        if (is_array($v) && ded_listing_variant_saleable($v)) {
            return true;
        }
    }

    return false;
}

function ded_listing_effective_price_bounds_for_slugs(PDO $pdo, array $slugs): array
{
    $minP = INF;
    $maxP = 0.0;
    foreach ($slugs as $slug1) {
        $slug = (string) $slug1;
        if ($slug === '') {
            continue;
        }
        $det = ded_product_by_slug($pdo, $slug);
        if ($det === null) {
            continue;
        }

        $p = ded_product_effective_sale_price($det['row'], is_array($det['variants']) ? $det['variants'] : []);
        if ($p <= 0.0) {
            continue;
        }

        $minP = min($minP, $p);
        $maxP = max($maxP, $p);
    }

    if ($minP === INF) {
        return ['min' => 0.0, 'max' => 8000.0];
    }

    return ['min' => floor($minP), 'max' => max(ceil($maxP), 1.0)];
}

function ded_products_apply_listing_controls(PDO $pdo, array $productRows, array $ctrl): array
{
    if ($productRows === []) {
        return [];
    }

    $sales = ded_listing_sales_totals_by_slug($pdo);

    $detMemo = [];
    $resolver = static function (string $slug) use ($pdo, &$detMemo): ?array {
        if (!array_key_exists($slug, $detMemo)) {
            $detMemo[$slug] = ded_product_by_slug($pdo, $slug);
        }

        return $detMemo[$slug];
    };

    $originalIndexBySlug = [];
    foreach ($productRows as $i => $_row) {
        $slugI = (string) ($_row['slug'] ?? '');
        if ($slugI !== '') {
            $originalIndexBySlug[$slugI] ??= $i;
        }
    }

    $effectivePrice = static function (string $slug) use ($resolver): float {
        $det = $resolver($slug);
        if ($det === null) {
            return 0.0;
        }

        return ded_product_effective_sale_price($det['row'], is_array($det['variants'] ?? null) ? $det['variants'] : []);
    };

    $out = [];

    foreach ($productRows as $row) {
        $slug = (string) ($row['slug'] ?? '');
        if ($slug === '') {
            continue;
        }

        $det = $resolver($slug);

        if (!empty($ctrl['in_stock_only']) && !ded_listing_product_has_stock($det)) {
            continue;
        }

        $price = $effectivePrice($slug);
        if (($ctrl['price_gte'] ?? null) !== null && $price < (float) $ctrl['price_gte']) {
            continue;
        }
        if (($ctrl['price_lte'] ?? null) !== null && $price > (float) $ctrl['price_lte']) {
            continue;
        }

        $out[] = $row;
    }

    $sort = (string) ($ctrl['sort'] ?? 'manual');

    $cmpOriginal = static function (string $a, string $b) use ($originalIndexBySlug): int {
        $ia = $originalIndexBySlug[$a] ?? 0;
        $ib = $originalIndexBySlug[$b] ?? 0;

        return $ia <=> $ib;
    };

    if ($sort === 'manual') {
        usort(
            $out,

            static fn (array $a, array $b): int => $cmpOriginal((string) ($a['slug'] ?? ''), (string) ($b['slug'] ?? '')),
        );

        return $out;
    }

    if ($sort === 'most-relevant') {

        usort(
            $out,
            static fn (array $a, array $b): int => $cmpOriginal((string) ($a['slug'] ?? ''), (string) ($b['slug'] ?? '')),
        );

        return $out;
    }

    if ($sort === 'best-selling') {
        usort(
            $out,

            static function (array $a, array $b) use ($sales, $cmpOriginal): int {
                $sa = (string) ($a['slug'] ?? '');
                $sb = (string) ($b['slug'] ?? '');
                $ca = $sales[$sa] ?? 0;
                $cb = $sales[$sb] ?? 0;
                if ($ca !== $cb) {
                    return $cb <=> $ca;
                }

                return $cmpOriginal($sa, $sb);
            },
        );

        return $out;
    }

    if ($sort === 'title-ascending') {
        usort($out, static function (array $a, array $b) use ($cmpOriginal): int {
            $cmp = strcmp(
                strtolower((string) ($a['title'] ?? '')),
                strtolower((string) ($b['title'] ?? '')),
            );

            return $cmp !== 0 ? $cmp : $cmpOriginal((string) ($a['slug'] ?? ''), (string) ($b['slug'] ?? ''));
        });

        return $out;
    }

    if ($sort === 'title-descending') {
        usort($out, static function (array $a, array $b) use ($cmpOriginal): int {
            $cmp = strcmp(
                strtolower((string) ($b['title'] ?? '')),
                strtolower((string) ($a['title'] ?? '')),
            );

            return $cmp !== 0 ? $cmp : $cmpOriginal((string) ($a['slug'] ?? ''), (string) ($b['slug'] ?? ''));
        });

        return $out;
    }

    if ($sort === 'price-ascending') {
        usort($out, static function (array $a, array $b) use ($effectivePrice, $cmpOriginal): int {
            $sa = (string) ($a['slug'] ?? '');
            $sb = (string) ($b['slug'] ?? '');
            $pa = $effectivePrice($sa);
            $pb = $effectivePrice($sb);
            if (($pa <=> $pb) !== 0) {
                return $pa <=> $pb;
            }

            return $cmpOriginal($sa, $sb);
        });

        return $out;
    }

    if ($sort === 'price-descending') {
        usort($out, static function (array $a, array $b) use ($effectivePrice, $cmpOriginal): int {
            $sa = (string) ($a['slug'] ?? '');
            $sb = (string) ($b['slug'] ?? '');
            $pa = $effectivePrice($sa);
            $pb = $effectivePrice($sb);
            if (($pb <=> $pa) !== 0) {
                return $pb <=> $pa;
            }

            return $cmpOriginal($sa, $sb);
        });

        return $out;
    }

    $tsAsc = static function (array $a, array $b) use ($cmpOriginal): int {
        $sa = (string) ($a['slug'] ?? '');
        $sb = (string) ($b['slug'] ?? '');
        $taNum = strtotime((string) ($a['created_at'] ?? ''));
        $tbNum = strtotime((string) ($b['created_at'] ?? ''));
        $taTs = ($taNum !== false) ? $taNum : 0;
        $tbTs = ($tbNum !== false) ? $tbNum : 0;
        if (($taTs <=> $tbTs) !== 0) {
            return $taTs <=> $tbTs;
        }

        return $cmpOriginal($sa, $sb);
    };

    $tsDesc = static function (array $a, array $b) use ($tsAsc): int {
        return $tsAsc($b, $a);
    };

    if ($sort === 'created-ascending') {
        usort($out, $tsAsc);

        return $out;
    }

    if ($sort === 'created-descending') {
        usort($out, $tsDesc);

        return $out;
    }

    usort($out, static fn (array $a, array $b): int => $cmpOriginal((string) ($a['slug'] ?? ''), (string) ($b['slug'] ?? '')));

    return $out;
}

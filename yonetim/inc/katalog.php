<?php declare(strict_types=1);

function yonetim_catalog_get(): ?array
{
    $pdo = ded_pdo();
    if (!$pdo || !ded_db_ready()) {
        return null;
    }
    ded_catalog_ensure_variant_stock_qty_column($pdo);
    ded_catalog_ensure_product_audience_column($pdo);
    ded_catalog_ensure_product_compare_at_price_column($pdo);
    ded_pages_ensure_source_html_column($pdo);
    $cat = ded_catalog_fetch($pdo);
    $cat['version'] = (int) ($cat['version'] ?? 1);
    $cat['updatedAt'] = gmdate('c');

    return $cat;
}

function yonetim_catalog_save(array $catalog): void
{
    if (
        !isset($catalog['site'], $catalog['products'], $catalog['collections'], $catalog['pages'])
        || !is_array($catalog['products'])
    ) {
        throw new InvalidArgumentException('invalid_catalog');
    }
    $catalog['version'] = (int) ($catalog['version'] ?? 1);
    $catalog['updatedAt'] = gmdate('c');

    $pdo = ded_pdo();
    if (!$pdo || !ded_db_ready()) {
        throw new RuntimeException('Katalog kaydı için veritabanı gerekli.');
    }
    ded_catalog_save($pdo, $catalog);
}

function yonetim_catalog_find_product_index(array $catalog, string $slug): ?int
{
    foreach ($catalog['products'] as $i => $p) {
        if (($p['slug'] ?? '') === $slug) {
            return (int) $i;
        }
    }
    return null;
}

function yonetim_catalog_find_collection_index(array $catalog, string $id): ?int
{
    foreach ($catalog['collections'] as $i => $c) {
        if ((string) ($c['id'] ?? '') === $id) {
            return (int) $i;
        }
    }
    return null;
}

function yonetim_catalog_delete_collection_by_id(array &$catalog, string $id): bool
{
    $idx = yonetim_catalog_find_collection_index($catalog, $id);
    if ($idx === null) {
        return false;
    }
    array_splice($catalog['collections'], $idx, 1);

    return true;
}

function yonetim_catalog_find_page_index(array $catalog, string $slug): ?int
{
    foreach ($catalog['pages'] as $i => $p) {
        if (($p['slug'] ?? '') === $slug) {
            return (int) $i;
        }
    }
    return null;
}

function yonetim_catalog_normalize_page_slug(string $slug): string
{
    $slug = trim($slug);
    $slug = preg_replace('/\s+/u', '-', $slug) ?? $slug;
    $slug = preg_replace('/-+/u', '-', $slug) ?? $slug;

    return trim($slug, '-');
}

function yonetim_catalog_page_slug_valid(string $slug): bool
{
    if ($slug === '' || strlen($slug) > 181) {
        return false;
    }
    if (str_contains($slug, '/') || str_contains($slug, '\\') || str_contains($slug, '..')) {
        return false;
    }

    return (bool) preg_match('/^[a-z0-9][a-z0-9\-]*$/ui', $slug);
}

function yonetim_catalog_normalize_collection_id(string $raw): string
{
    $s = trim($raw);
    $s = mb_strtolower($s, 'UTF-8');
    static $tr = [
        'ş' => 's', 'ğ' => 'g', 'ü' => 'u', 'ö' => 'o', 'ç' => 'c',
        'ı' => 'i', 'İ' => 'i', 'â' => 'a', 'î' => 'i', 'û' => 'u',
    ];
    $s = strtr($s, $tr);
    $s = preg_replace('/\s+/u', '-', $s) ?? $s;
    $s = preg_replace('/[^a-z0-9\-]+/', '-', $s) ?? $s;
    $s = preg_replace('/-{2,}/', '-', $s) ?? $s;

    return trim($s, '-');
}

function yonetim_catalog_collection_id_valid(string $id): bool
{
    if ($id === '' || strlen($id) > 190) {
        return false;
    }
    if (str_contains($id, '/') || str_contains($id, '\\') || str_contains($id, '..')) {
        return false;
    }

    return (bool) preg_match('/^[a-z0-9][a-z0-9\-]*$/', $id);
}

function yonetim_catalog_delete_page_by_slug(array &$catalog, string $slug): bool
{
    $idx = yonetim_catalog_find_page_index($catalog, $slug);
    if ($idx === null) {
        return false;
    }
    array_splice($catalog['pages'], $idx, 1);

    return true;
}

function yonetim_catalog_replace_product_slug(array &$catalog, string $oldSlug, string $newSlug): void
{
    foreach ($catalog['collections'] as &$col) {
        $slugs = $col['productSlugs'] ?? [];
        if (!is_array($slugs)) {
            continue;
        }
        foreach ($slugs as $j => $ps) {
            if ((string) $ps === $oldSlug) {
                $slugs[$j] = $newSlug;
            }
        }
        $col['productSlugs'] = $slugs;
    }
    unset($col);
}

function yonetim_catalog_default_product(): array
{
    return [
        'id' => '',
        'slug' => '',
        'title' => '',
        'description' => '',
        'brand' => '',
        'audience' => '',
        'price' => 0.0,
        'currency' => 'TRY',
        'sortOrder' => 0,
        'images' => [],
        'variants' => [
            [
                'name' => 'Varsayılan',
                'price' => 0.0,
                'currency' => 'TRY',
                'sku' => null,
                'inStock' => true,
                'stockQty' => 10,
            ],
        ],
        'sourceHtml' => '',
    ];
}

function yonetim_catalog_remove_product_slug_from_collections(array &$catalog, string $slug): void
{
    foreach ($catalog['collections'] as &$col) {
        $slugs = $col['productSlugs'] ?? [];
        if (!is_array($slugs)) {
            continue;
        }
        $col['productSlugs'] = array_values(array_filter($slugs, static function ($s) use ($slug) {
            return (string) $s !== $slug;
        }));
    }
    unset($col);
}

function yonetim_catalog_delete_product_by_slug(array &$catalog, string $slug): bool
{
    $idx = yonetim_catalog_find_product_index($catalog, $slug);
    if ($idx === null) {
        return false;
    }
    array_splice($catalog['products'], $idx, 1);
    yonetim_catalog_remove_product_slug_from_collections($catalog, $slug);

    return true;
}

function yonetim_catalog_sync_product_collections(array &$catalog, string $slug, array $collectionIds): void
{
    $slug = trim($slug);
    if ($slug === '' || !isset($catalog['collections']) || !is_array($catalog['collections'])) {
        return;
    }
    $want = [];
    foreach ($collectionIds as $id) {
        $id = trim((string) $id);
        if ($id !== '') {
            $want[$id] = true;
        }
    }
    foreach ($catalog['collections'] as &$col) {
        $cid = (string) ($col['id'] ?? '');
        $slugs = $col['productSlugs'] ?? [];
        if (!is_array($slugs)) {
            $slugs = [];
        }
        $has = in_array($slug, $slugs, true);
        $should = isset($want[$cid]);
        if ($should && !$has) {
            $slugs[] = $slug;
        } elseif (!$should && $has) {
            $slugs = array_values(array_filter($slugs, static fn ($s) => (string) $s !== $slug));
        }
        $col['productSlugs'] = array_values($slugs);
    }
    unset($col);
}

function yonetim_catalog_collections_for_product_slug(array $catalog, string $slug): array
{
    $out = [];
    foreach ($catalog['collections'] ?? [] as $col) {
        $slugs = $col['productSlugs'] ?? [];
        if (!is_array($slugs)) {
            continue;
        }
        if (in_array($slug, $slugs, true)) {
            $out[] = (string) ($col['id'] ?? '');
        }
    }

    return $out;
}

function yonetim_catalog_append_product_to_all_collections(array &$catalog, string $slug): void
{
    $slug = trim($slug);
    if ($slug === '' || !isset($catalog['collections']) || !is_array($catalog['collections'])) {
        return;
    }
    foreach ($catalog['collections'] as &$col) {
        $slugs = $col['productSlugs'] ?? [];
        if (!is_array($slugs)) {
            $slugs = [];
        }
        if (!in_array($slug, $slugs, true)) {
            $slugs[] = $slug;
        }
        $col['productSlugs'] = $slugs;
    }
    unset($col);
}

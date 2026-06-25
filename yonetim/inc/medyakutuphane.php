<?php declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/lib/medyameta.php';
require_once __DIR__ . '/urunyukle.php';

const YONETIM_MEDIA_ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

function yonetim_media_library_list(int $limit = 400): array
{
    $base = DED_ROOT . '/cdn';
    if (!is_dir($base)) {
        return [];
    }
    $meta = yonetim_media_meta_all();
    $list = [];
    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, YONETIM_MEDIA_ALLOWED_EXT, true)) {
                continue;
            }
            $full = $file->getPathname();
            $rel = str_replace('\\', '/', substr($full, strlen(DED_ROOT) + 1));
            $list[] = [
                'path' => $rel,
                'size' => $file->getSize(),
                'mtime' => $file->getMTime(),
                'name' => basename($rel),
                'alt' => (string) ($meta[$rel]['alt'] ?? ''),
            ];
            if (count($list) >= $limit) {
                break;
            }
        }
    } catch (Throwable) {
        return [];
    }
    usort($list, static fn ($a, $b) => ($b['mtime'] ?? 0) <=> ($a['mtime'] ?? 0));

    return $list;
}

function yonetim_media_format_size(int $bytes): string
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }

    return round($bytes / (1024 * 1024), 1) . ' MB';
}

function yonetim_media_path_key(string $path): string
{
    $path = trim(str_replace('\\', '/', $path));
    $q = strpos($path, '?');
    if ($q !== false) {
        $path = substr($path, 0, $q);
    }

    return $path;
}

function yonetim_media_matches_ref(string $ref, string $rel): bool
{
    $a = yonetim_media_path_key($ref);
    $b = yonetim_media_path_key($rel);
    if ($a === $b) {
        return true;
    }
    if ($a !== '' && $b !== '' && basename($a) === basename($b)) {
        return true;
    }

    return false;
}

function yonetim_media_normalize_rel(string $path): ?string
{
    $path = yonetim_media_path_key($path);
    if ($path === '' || !yonetim_safe_storage_rel_path($path)) {
        return null;
    }
    if (!str_starts_with($path, 'cdn/')) {
        return null;
    }
    $full = DED_ROOT . '/' . $path;
    if (!is_file($full)) {
        return null;
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (!in_array($ext, YONETIM_MEDIA_ALLOWED_EXT, true)) {
        return null;
    }

    return $path;
}

function yonetim_media_full_path(string $rel): ?string
{
    $rel = yonetim_media_normalize_rel($rel);
    if ($rel === null) {
        return null;
    }

    return DED_ROOT . '/' . $rel;
}

function yonetim_media_meta_all(): array
{
    return ded_media_meta_all();
}

function yonetim_media_meta_save(string $rel, string $alt): void
{
    $rel = yonetim_media_normalize_rel($rel);
    if ($rel === null) {
        throw new InvalidArgumentException('Geçersiz dosya.');
    }
    ded_media_meta_save($rel, $alt);
}

function yonetim_media_meta_remove(string $rel): void
{
    ded_media_meta_remove($rel);
}

function yonetim_media_paths_from_post(): array
{
    $raw = $_POST['paths'] ?? [];
    if (!is_array($raw)) {
        $one = trim((string) ($_POST['path'] ?? ''));
        return $one !== '' ? [$one] : [];
    }
    $out = [];
    foreach ($raw as $p) {
        $p = trim((string) $p);
        if ($p !== '') {
            $out[] = $p;
        }
    }

    return array_values(array_unique($out));
}

function yonetim_media_save_uploads(): array
{
    if (empty($_FILES['media_files'])) {
        return [];
    }
    require_once __DIR__ . '/urunyukle.php';
    $prev = $_FILES['product_images'] ?? null;
    $_FILES['product_images'] = $_FILES['media_files'];
    try {
        return yonetim_product_save_uploaded_images();
    } finally {
        if ($prev === null) {
            unset($_FILES['product_images']);
        } else {
            $_FILES['product_images'] = $prev;
        }
    }
}

function yonetim_media_delete_file(string $rel): void
{
    $full = yonetim_media_full_path($rel);
    if ($full === null) {
        throw new RuntimeException('Dosya bulunamadı.');
    }
    if (!@unlink($full)) {
        throw new RuntimeException('Dosya silinemedi.');
    }
    yonetim_media_meta_remove($rel);
}

function yonetim_media_rename_file(string $rel, string $newName): string
{
    $rel = yonetim_media_normalize_rel($rel);
    if ($rel === null) {
        throw new InvalidArgumentException('Geçersiz dosya.');
    }
    $newName = trim($newName);
    if ($newName === '' || str_contains($newName, '/') || str_contains($newName, '\\') || str_contains($newName, '..')) {
        throw new InvalidArgumentException('Geçersiz dosya adı.');
    }
    $dir = dirname($rel);
    $oldExt = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
    $newExt = strtolower(pathinfo($newName, PATHINFO_EXTENSION));
    if ($newExt === '') {
        $newName .= '.' . $oldExt;
        $newExt = $oldExt;
    }
    if ($newExt !== $oldExt || !in_array($newExt, YONETIM_MEDIA_ALLOWED_EXT, true)) {
        throw new InvalidArgumentException('Uzantı değiştirilemez.');
    }
    $newName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $newName) ?? $newName;
    $newRel = ($dir === '.' ? '' : $dir . '/') . $newName;
    if ($newRel === $rel) {
        return $rel;
    }
    $oldFull = DED_ROOT . '/' . $rel;
    $newFull = DED_ROOT . '/' . $newRel;
    if (is_file($newFull)) {
        throw new RuntimeException('Bu isimde dosya zaten var.');
    }
    if (!@rename($oldFull, $newFull)) {
        throw new RuntimeException('Yeniden adlandırılamadı.');
    }
    $meta = yonetim_media_meta_all();
    if (isset($meta[$rel]['alt'])) {
        ded_media_meta_save($newRel, (string) ($meta[$rel]['alt'] ?? ''));
        ded_media_meta_remove($rel);
    }
    yonetim_media_replace_references($rel, $newRel);

    return $newRel;
}

function yonetim_media_replace_references(string $oldRel, ?string $newRel): int
{
    require_once __DIR__ . '/katalog.php';
    $cat = yonetim_catalog_get();
    if ($cat === null) {
        return 0;
    }
    $n = 0;
    foreach ($cat['products'] as &$p) {
        if (!isset($p['images']) || !is_array($p['images'])) {
            continue;
        }
        $changed = false;
        foreach ($p['images'] as $i => $img) {
            if (!yonetim_media_matches_ref((string) $img, $oldRel)) {
                continue;
            }
            if ($newRel === null) {
                unset($p['images'][$i]);
            } else {
                $p['images'][$i] = $newRel;
            }
            $changed = true;
            $n++;
        }
        if ($changed) {
            $p['images'] = array_values($p['images']);
        }
    }
    unset($p);
    foreach ($cat['collections'] as &$c) {
        $img = (string) ($c['image'] ?? '');
        if ($img !== '' && yonetim_media_matches_ref($img, $oldRel)) {
            $c['image'] = $newRel ?? '';
            $n++;
        }
    }
    unset($c);
    if ($n > 0) {
        yonetim_catalog_save($cat);
    }

    return $n;
}

function yonetim_media_detach_references(string $rel): int
{
    return yonetim_media_replace_references($rel, null);
}

function yonetim_media_usage_count(string $rel): int
{
    require_once __DIR__ . '/katalog.php';
    $cat = yonetim_catalog_get();
    if ($cat === null) {
        return 0;
    }
    $n = 0;
    foreach ($cat['products'] as $p) {
        foreach ($p['images'] ?? [] as $img) {
            if (yonetim_media_matches_ref((string) $img, $rel)) {
                $n++;
            }
        }
    }
    foreach ($cat['collections'] as $c) {
        if (yonetim_media_matches_ref((string) ($c['image'] ?? ''), $rel)) {
            $n++;
        }
    }

    return $n;
}

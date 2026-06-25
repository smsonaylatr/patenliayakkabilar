<?php declare(strict_types=1);

function yonetim_product_image_admin_src(string $path): string
{
    $path = trim(str_replace('\\', '/', $path));
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $q = strpos($path, '?');
    if ($q !== false) {
        $path = substr($path, 0, $q);
    }

    return '../' . ltrim($path, '/');
}

const YONETIM_PRODUCT_IMAGE_MAX_BYTES = 8 * 1024 * 1024;
const YONETIM_PRODUCT_IMAGE_MAX_COUNT = 24;

function yonetim_safe_storage_rel_path(string $path): bool
{
    $path = trim(str_replace('\\', '/', $path));
    if ($path === '' || str_contains($path, "\0")) {
        return false;
    }
    foreach (explode('/', $path) as $seg) {
        if ($seg === '..') {
            return false;
        }
    }

    return true;
}

function yonetim_product_existing_images_from_post(): array
{
    $raw = $_POST['existing_images'] ?? [];
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $path) {
        $path = trim((string) $path);
        if ($path === '' || !yonetim_safe_storage_rel_path($path)) {
            continue;
        }
        $out[] = $path;
    }

    return $out;
}

function yonetim_product_save_uploaded_images(): array
{
    if (empty($_FILES['product_images'])) {
        return [];
    }
    $f = $_FILES['product_images'];
    $items = [];
    if (is_array($f['name'])) {
        $n = count($f['name']);
        for ($i = 0; $i < $n; $i++) {
            if (($f['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $items[] = [
                'name' => $f['name'][$i],
                'tmp_name' => $f['tmp_name'][$i],
                'error' => $f['error'][$i],
                'size' => $f['size'][$i],
            ];
        }
    } else {
        if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [];
        }
        $items[] = [
            'name' => $f['name'],
            'tmp_name' => $f['tmp_name'],
            'error' => $f['error'],
            'size' => $f['size'],
        ];
    }

    if ($items === []) {
        return [];
    }
    if (count($items) > YONETIM_PRODUCT_IMAGE_MAX_COUNT) {
        throw new RuntimeException('En fazla ' . YONETIM_PRODUCT_IMAGE_MAX_COUNT . ' dosya yükleyebilirsiniz.');
    }

    $targetDir = DED_ROOT . '/' . UPLOAD_SUBDIR;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        throw new RuntimeException('Yükleme klasörü oluşturulamadı.');
    }

    $mimeToExt = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $out = [];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    foreach ($items as $item) {
        if ($item['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Dosya yükleme hatası (kod ' . (int) $item['error'] . ').');
        }
        if (!is_uploaded_file($item['tmp_name'])) {
            throw new RuntimeException('Geçersiz yükleme.');
        }
        if (($item['size'] ?? 0) > YONETIM_PRODUCT_IMAGE_MAX_BYTES) {
            throw new RuntimeException('Dosya çok büyük (en fazla 8 MB).');
        }
        $mime = $finfo->file($item['tmp_name']) ?: '';
        $ext = $mimeToExt[$mime] ?? null;
        if ($ext === null) {
            throw new RuntimeException('Yalnızca JPEG, PNG, WebP veya GIF yüklenebilir.');
        }
        $orig = basename((string) $item['name']);
        $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($orig, PATHINFO_FILENAME)) ?? 'img';
        $name = $safe . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $targetDir . '/' . $name;
        if (!move_uploaded_file($item['tmp_name'], $dest)) {
            throw new RuntimeException('Dosya kaydedilemedi.');
        }
        $out[] = str_replace('\\', '/', UPLOAD_SUBDIR . '/' . $name);
    }

    return $out;
}

function yonetim_single_image_upload(string $fieldName): ?string
{
    if (empty($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return null;
    }
    $f = $_FILES[$fieldName];
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Dosya yükleme hatası (kod ' . (int) ($f['error'] ?? 0) . ').');
    }
    if (!is_uploaded_file((string) ($f['tmp_name'] ?? ''))) {
        throw new RuntimeException('Geçersiz yükleme.');
    }
    if (($f['size'] ?? 0) > YONETIM_PRODUCT_IMAGE_MAX_BYTES) {
        throw new RuntimeException('Dosya çok büyük (en fazla 8 MB).');
    }

    $targetDir = DED_ROOT . '/' . UPLOAD_SUBDIR;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        throw new RuntimeException('Yükleme klasörü oluşturulamadı.');
    }

    $mimeToExt = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string) $f['tmp_name']) ?: '';
    $ext = $mimeToExt[$mime] ?? null;
    if ($ext === null) {
        throw new RuntimeException('Yalnızca JPEG, PNG, WebP veya GIF yüklenebilir.');
    }
    $orig = basename((string) $f['name']);
    $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($orig, PATHINFO_FILENAME)) ?? 'cover';
    $name = $safe . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = $targetDir . '/' . $name;
    if (!move_uploaded_file((string) $f['tmp_name'], $dest)) {
        throw new RuntimeException('Dosya kaydedilemedi.');
    }

    return str_replace('\\', '/', UPLOAD_SUBDIR . '/' . $name);
}

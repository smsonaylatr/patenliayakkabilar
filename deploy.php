<?php
/**
 * Post-deploy script — Plesk Git webhook sonrası çalışır.
 * 
 * Plesk Git "Additional actions" alanına şunu girin:
 *   php deploy.php
 */

$basePath = __DIR__ . '/';

echo "🚀 Deploy başlıyor...\n\n";

// 1. Storage symlink oluştur (yoksa veya kırık ise)
$publicStorage = $basePath . 'public/storage';
$storageTarget = $basePath . 'storage/app/public';

if (!is_link($publicStorage)) {
    // Eğer gerçek dizin ise sil
    if (is_dir($publicStorage)) {
        // Önce içindeki dosyaları storage/app/public'e taşı (veri kaybını önle)
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($publicStorage, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $relativePath = substr($item->getPathname(), strlen($publicStorage));
            $targetPath = $storageTarget . $relativePath;
            
            if ($item->isDir() && !is_dir($targetPath)) {
                @mkdir($targetPath, 0755, true);
            } elseif ($item->isFile() && !file_exists($targetPath)) {
                @copy($item->getPathname(), $targetPath);
            }
        }
        echo "📁 public/storage içindeki dosyalar storage/app/public'e kopyalandı\n";
        
        // Dizini sil
        $deleteIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($publicStorage, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($deleteIterator as $item) {
            $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }
        @rmdir($publicStorage);
        echo "🗑️  Eski public/storage dizini silindi\n";
    }
    
    // Symlink oluştur
    if (symlink($storageTarget, $publicStorage)) {
        echo "✅ Storage symlink oluşturuldu\n";
    } else {
        echo "⚠️  Symlink oluşturulamadı — sunucu symlink'i desteklemiyor olabilir\n";
    }
} else {
    echo "✅ Storage symlink zaten mevcut\n";
}

// 1b. Migration çalıştır
echo "📦 Migration çalıştırılıyor...\n";
exec('cd ' . escapeshellarg($basePath) . ' && php artisan migrate --force 2>&1', $migrationOutput, $migrationCode);
echo implode("\n", $migrationOutput) . "\n";
echo $migrationCode === 0 ? "✅ Migration tamamlandı\n" : "⚠️ Migration hatası (kod: {$migrationCode})\n";

// 2. Clear compiled views
$viewsDir = $basePath . 'storage/framework/views/';
if (is_dir($viewsDir)) {
    $files = glob($viewsDir . '*.php');
    foreach ($files as $file) {
        @unlink($file);
    }
    echo "✅ View cache temizlendi (" . count($files) . " dosya)\n";
}

// 3. Clear config cache
$configCache = $basePath . 'bootstrap/cache/config.php';
if (file_exists($configCache)) {
    @unlink($configCache);
    echo "✅ Config cache temizlendi\n";
}

// 4. Clear route cache
$routeFiles = glob($basePath . 'bootstrap/cache/routes-*.php');
foreach ($routeFiles as $routeCache) {
    @unlink($routeCache);
}
echo "✅ Route cache temizlendi\n";

// 5. Clear application cache
$cacheDir = $basePath . 'storage/framework/cache/data/';
if (is_dir($cacheDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isFile()) {
            @unlink($item->getPathname());
        }
    }
    echo "✅ Application cache temizlendi\n";
}

// 6. Fix permissions — storage dizinleri
$storageDirs = [
    'storage',
    'storage/logs',
    'storage/framework',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/app',
    'storage/app/public',
    'storage/app/public/products',
    'storage/app/public/livewire-tmp',
    'storage/app/private',
    'storage/app/private/livewire-tmp',
    'bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    $fullPath = $basePath . $dir;
    if (is_dir($fullPath)) {
        @chmod($fullPath, 0775);
    } elseif (!file_exists($fullPath)) {
        @mkdir($fullPath, 0775, true);
    }
}
echo "✅ İzinler düzeltildi\n";

echo "\n✅ Deploy tamamlandı! " . date('Y-m-d H:i:s') . "\n";


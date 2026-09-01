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

// 1a. Composer paketleri (varsa güncelle)
echo "📦 Composer paketleri yükleniyor...\n";
exec('cd ' . escapeshellarg($basePath) . ' && composer install --no-dev --optimize-autoloader 2>&1', $composerOutput);
echo implode("\n", (array)$composerOutput) . "\n";

// 1b. Migration çalıştır
echo "📦 Migration çalıştırılıyor...\n";
exec('cd ' . escapeshellarg($basePath) . ' && php artisan migrate --force 2>&1', $migrationOutput, $migrationCode);
echo implode("\n", $migrationOutput) . "\n";
echo $migrationCode === 0 ? "✅ Migration tamamlandı\n" : "⚠️ Migration hatası (kod: {$migrationCode})\n";

// 1c. Seeders & SEO Linkleme
exec('cd ' . escapeshellarg($basePath) . ' && php artisan db:seed --class="Database\\Seeders\\BacklinkSeeder" --force 2>&1');
exec('cd ' . escapeshellarg($basePath) . ' && php artisan db:seed --class="Database\\Seeders\\LinkableAssetBlogPostSeeder" --force 2>&1');
exec('cd ' . escapeshellarg($basePath) . ' && php artisan db:seed --class="Database\\Seeders\\KarneHediyesiBlogSeeder" --force 2>&1');

// 1d. Doğrudan SQL ile blog ekleme (seeder başarısız olursa yedek)
$sqlFile = $basePath . 'karne_blog_insert.sql';
if (file_exists($sqlFile)) {
    $runSqlScript = $basePath . 'run_sql_insert.php';
    // Geçici script oluştur
    file_put_contents($runSqlScript, '<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    $sql = file_get_contents(__DIR__ . "/karne_blog_insert.sql");
    \Illuminate\Support\Facades\DB::unprepared($sql);
    echo "SQL OK";
} catch (\Exception $e) {
    echo "SQL HATA: " . $e->getMessage();
}
');
    exec('cd ' . escapeshellarg($basePath) . ' && php -d memory_limit=128M run_sql_insert.php 2>&1', $sqlOutput);
    echo "📝 Karne blog SQL: " . implode("\n", $sqlOutput) . "\n";
    @unlink($runSqlScript); // Geçici script sil
}
// 1e. Beden rehberi SQL güncelleme
$bedenSql = $basePath . 'beden_rehberi_update.sql';
if (file_exists($bedenSql)) {
    $runBedenScript = $basePath . 'run_beden_update.php';
    file_put_contents($runBedenScript, '<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    $sql = file_get_contents(__DIR__ . "/beden_rehberi_update.sql");
    \Illuminate\Support\Facades\DB::unprepared($sql);
    echo "Beden SQL OK";
} catch (\Exception $e) {
    echo "Beden SQL HATA: " . $e->getMessage();
}
');
    exec('cd ' . escapeshellarg($basePath) . ' && php -d memory_limit=128M run_beden_update.php 2>&1', $bedenOutput);
    echo "📏 Beden rehberi SQL: " . implode("\n", $bedenOutput) . "\n";
    @unlink($runBedenScript);
}

exec('cd ' . escapeshellarg($basePath) . ' && php artisan seo:link-content 2>&1');

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

// 6. Reload Octane (RoadRunner) if it's running
echo "🚀 Octane (RoadRunner) yeniden başlatılıyor...\n";
exec('cd ' . escapeshellarg($basePath) . ' && php artisan octane:reload 2>&1', $octaneOutput, $octaneCode);
echo implode("\n", $octaneOutput) . "\n";
echo "✅ Octane yenilendi\n";

// 7. Fix permissions — storage dizinleri
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


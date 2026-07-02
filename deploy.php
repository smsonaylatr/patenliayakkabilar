<?php
/**
 * Post-deploy webhook script
 * This should be called after git pull to clear all caches
 * 
 * Add this to your Plesk Git webhook settings or call manually:
 * /opt/plesk/php/8.3/bin/php /var/www/vhosts/patenliayakkabilar.com/httpdocs/deploy.php
 */

$basePath = __DIR__ . '/';

// 1. Clear compiled views
$viewsDir = $basePath . 'storage/framework/views/';
if (is_dir($viewsDir)) {
    $files = glob($viewsDir . '*.php');
    foreach ($files as $file) {
        @unlink($file);
    }
    echo "✅ View cache cleared (" . count($files) . " files)\n";
}

// 2. Clear config cache
$configCache = $basePath . 'bootstrap/cache/config.php';
if (file_exists($configCache)) {
    @unlink($configCache);
    echo "✅ Config cache cleared\n";
}

// 3. Clear route cache
foreach (glob($basePath . 'bootstrap/cache/routes-*.php') as $routeCache) {
    @unlink($routeCache);
}
echo "✅ Route cache cleared\n";

// 4. Clear application cache
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
    echo "✅ Application cache cleared\n";
}

// 5. Fix permissions
chmod($basePath . 'storage', 0777);
@chmod($basePath . 'storage/logs', 0777);
@chmod($basePath . 'storage/framework', 0777);
@chmod($basePath . 'storage/framework/views', 0777);
@chmod($basePath . 'storage/framework/cache', 0777);
@chmod($basePath . 'storage/framework/sessions', 0777);
@chmod($basePath . 'bootstrap/cache', 0777);
echo "✅ Permissions fixed\n";

echo "\n✅ Deploy complete! " . date('Y-m-d H:i:s') . "\n";

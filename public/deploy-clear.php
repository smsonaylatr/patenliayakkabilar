<?php
/**
 * Clear all Laravel caches - run after each deploy
 * URL: https://patenliayakkabilar.com/deploy-clear.php
 */

$base = dirname(__FILE__) . '/../';

// Clear compiled views (THIS IS THE FIX FOR show.blade.php error)
$viewsDir = $base . 'storage/framework/views/';
$count = 0;
if (is_dir($viewsDir)) {
    foreach (glob($viewsDir . '*.php') as $file) {
        @unlink($file);
        $count++;
    }
}
echo "✅ Compiled views cleared: {$count} files\n";

// Clear config cache
if (file_exists($base . 'bootstrap/cache/config.php')) {
    @unlink($base . 'bootstrap/cache/config.php');
    echo "✅ Config cache cleared\n";
}

// Clear route cache
foreach (glob($base . 'bootstrap/cache/routes-*.php') as $f) {
    @unlink($f);
}
echo "✅ Route cache cleared\n";

// Clear application cache  
$cacheDir = $base . 'storage/framework/cache/data/';
if (is_dir($cacheDir)) {
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $item) {
        if ($item->isFile()) @unlink($item->getPathname());
    }
    echo "✅ App cache cleared\n";
}

echo "\n🎉 Done! Refresh the product page now.\n";
echo date('Y-m-d H:i:s');

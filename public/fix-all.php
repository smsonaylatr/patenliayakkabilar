<?php
/**
 * Fix storage symlink and check images
 * DELETE THIS FILE AFTER USE
 */
$base = dirname(__FILE__) . '/../';
$publicPath = dirname(__FILE__) . '/';

echo "<pre>";

// 1. Check current storage link
$storageLink = $publicPath . 'storage';
echo "=== STORAGE LINK STATUS ===\n";
if (is_link($storageLink)) {
    echo "Type: Symlink\n";
    echo "Target: " . readlink($storageLink) . "\n";
    echo "Target exists: " . (file_exists(readlink($storageLink)) ? 'YES' : 'NO') . "\n";
} elseif (is_dir($storageLink)) {
    echo "Type: DIRECTORY (not a symlink!)\n";
} else {
    echo "Type: DOES NOT EXIST\n";
}

// 2. Re-create symlink properly
echo "\n=== FIXING STORAGE LINK ===\n";
if (is_link($storageLink)) {
    unlink($storageLink);
    echo "Removed old symlink\n";
} elseif (is_dir($storageLink)) {
    // It's a directory, not a symlink - this is the problem!
    echo "WARNING: storage is a directory, not a symlink!\n";
    // Don't delete directory, just rename it
    rename($storageLink, $storageLink . '_backup_' . time());
    echo "Renamed to storage_backup\n";
}

// Create proper symlink
$target = $base . 'storage/app/public';
if (symlink($target, $storageLink)) {
    echo "✅ Created symlink: public/storage -> $target\n";
} else {
    // Try with artisan
    echo "Direct symlink failed, trying artisan...\n";
    $phpBin = '/opt/plesk/php/8.3/bin/php';
    if (!file_exists($phpBin)) $phpBin = 'php';
    exec("cd " . escapeshellarg($base) . " && $phpBin artisan storage:link 2>&1", $out, $code);
    echo implode("\n", $out) . "\n";
}

// 3. Fix permissions
echo "\n=== FIXING PERMISSIONS ===\n";
$storagePublic = $base . 'storage/app/public';
chmod($storagePublic, 0755);
$productsDir = $storagePublic . '/products';
if (is_dir($productsDir)) {
    chmod($productsDir, 0755);
    foreach (glob($productsDir . '/*') as $file) {
        chmod($file, 0644);
    }
    echo "✅ Fixed permissions on products/ directory\n";
}

// 4. Verify access
echo "\n=== VERIFICATION ===\n";
$testFiles = glob($productsDir . '/*.*');
foreach ($testFiles as $file) {
    $basename = basename($file);
    $publicFile = $publicPath . 'storage/products/' . $basename;
    $exists = file_exists($publicFile);
    $size = $exists ? filesize($publicFile) : 0;
    $status = $exists && $size > 1000 ? '✅' : '❌';
    echo "$status $basename | Size: " . number_format($size) . " bytes | Via public: " . ($exists ? 'YES' : 'NO') . "\n";
}

// 5. Clear view cache
$viewsDir = $base . 'storage/framework/views/';
$count = 0;
foreach (glob($viewsDir . '*.php') as $f) { @unlink($f); $count++; }
echo "\n✅ Cleared $count compiled views\n";

echo "\n🎉 Done! " . date('Y-m-d H:i:s') . "\n";
echo "</pre>";
echo "<br>⚠️ DELETE THIS FILE: rm public/fix-all.php";

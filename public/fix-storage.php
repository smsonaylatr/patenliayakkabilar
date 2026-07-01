<?php
/**
 * Fix storage symlink and permissions for images
 * Access: https://patenliayakkabilar.com/fix-storage.php
 */

$basePath = dirname(__FILE__) . '/../';
$publicPath = dirname(__FILE__) . '/';

echo "<h2>Storage Fix</h2><pre>";

// 1. Remove existing storage link/dir
$storageLink = $publicPath . 'storage';
if (is_link($storageLink)) {
    $target = readlink($storageLink);
    echo "Current symlink: $storageLink -> $target\n";
    echo "Target exists: " . (file_exists($target) ? 'YES' : 'NO') . "\n";
    echo "Target readable: " . (is_readable($target) ? 'YES' : 'NO') . "\n\n";
} elseif (is_dir($storageLink)) {
    echo "Storage is a DIRECTORY, not a symlink\n\n";
} else {
    echo "No storage link found\n\n";
}

// 2. Fix permissions on storage/app/public
$storagePath = $basePath . 'storage/app/public';
echo "Fixing permissions on storage/app/public...\n";

// Make all dirs 755 and files 644
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($storagePath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$dirCount = 0;
$fileCount = 0;
foreach ($iterator as $item) {
    if ($item->isDir()) {
        chmod($item->getPathname(), 0755);
        $dirCount++;
    } else {
        chmod($item->getPathname(), 0644);
        $fileCount++;
    }
}
chmod($storagePath, 0755);
echo "Fixed: $dirCount directories, $fileCount files\n\n";

// 3. Re-create symlink using relative path
if (is_link($storageLink)) {
    unlink($storageLink);
    echo "Removed old symlink\n";
}

// Use artisan to create proper symlink
exec('cd ' . escapeshellarg($basePath) . ' && /opt/plesk/php/8.3/bin/php artisan storage:link 2>&1', $output, $returnCode);
echo "artisan storage:link output:\n";
echo implode("\n", $output) . "\n";
echo "Return code: $returnCode\n\n";

// 4. Verify
echo "\n--- Verification ---\n";
$storageLink = $publicPath . 'storage';
if (is_link($storageLink) || is_dir($storageLink)) {
    echo "✅ Storage link exists\n";
    
    // Test reading a file
    $testFiles = glob($storagePath . '/products/*.png');
    if (empty($testFiles)) {
        $testFiles = glob($storagePath . '/products/*.webp');
    }
    if (!empty($testFiles)) {
        $testFile = basename($testFiles[0]);
        $publicUrl = '/storage/products/' . $testFile;
        $fullPath = $publicPath . 'storage/products/' . $testFile;
        echo "Test file: products/$testFile\n";
        echo "Public URL: $publicUrl\n";
        echo "File exists via public path: " . (file_exists($fullPath) ? 'YES ✅' : 'NO ❌') . "\n";
        echo "File readable: " . (is_readable($fullPath) ? 'YES ✅' : 'NO ❌') . "\n";
        echo "File size: " . (file_exists($fullPath) ? filesize($fullPath) . ' bytes' : 'N/A') . "\n";
    }
} else {
    echo "❌ Storage link STILL missing!\n";
}

echo "</pre>";

// 5. Also check what image_path values are in the database
echo "<h3>Database image paths</h3><pre>";
require $basePath . 'vendor/autoload.php';
$app = require_once $basePath . 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

$images = \App\Models\ProductImage::all();
foreach ($images as $img) {
    $fullPath = $storagePath . '/' . $img->image_path;
    $exists = file_exists($fullPath);
    $url = '/storage/' . $img->image_path;
    echo "Product #{$img->product_id} | DB path: {$img->image_path} | File exists: " . ($exists ? '✅' : '❌') . " | URL: {$url}\n";
}
echo "</pre>";

echo "<br>⚠️ DELETE: rm public/fix-storage.php";

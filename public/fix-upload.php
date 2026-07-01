<?php
/**
 * Fix upload configuration on server
 * Access via: https://patenliayakkabilar.com/fix-upload.php
 * DELETE THIS FILE AFTER USE
 */

echo "<h2>PHP Upload Configuration</h2>";
echo "<pre>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "max_input_time: " . ini_get('max_input_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "</pre>";

$basePath = dirname(__FILE__) . '/../';

// Ensure livewire-tmp directory exists and is writable
$tmpDir = $basePath . 'storage/app/public/livewire-tmp';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0775, true);
    echo "✅ Created livewire-tmp directory<br>";
} else {
    echo "✅ livewire-tmp directory exists<br>";
}
echo "   Writable: " . (is_writable($tmpDir) ? '✅ Yes' : '❌ No - run: chmod -R 777 storage') . "<br>";

// Ensure products directory exists
$productsDir = $basePath . 'storage/app/public/products';
if (!is_dir($productsDir)) {
    mkdir($productsDir, 0775, true);
    echo "✅ Created products directory<br>";
} else {
    echo "✅ products directory exists<br>";
}
echo "   Writable: " . (is_writable($productsDir) ? '✅ Yes' : '❌ No') . "<br>";

// Check storage link
$storageLink = $basePath . 'public/storage';
echo "<br>Storage symlink: " . (file_exists($storageLink) ? '✅ Exists' : '❌ Missing') . "<br>";

// Create .htaccess to increase PHP limits
$htaccessPath = $basePath . 'public/.user.ini';
$phpIni = "upload_max_filesize = 20M\npost_max_size = 25M\nmax_execution_time = 300\nmax_input_time = 300\nmemory_limit = 256M\n";
file_put_contents($htaccessPath, $phpIni);
echo "<br>✅ Created .user.ini with increased upload limits<br>";

// Also create in root
file_put_contents($basePath . '.user.ini', $phpIni);
echo "✅ Created root .user.ini<br>";

echo "<br><strong>✅ Upload configuration fixed! Try uploading again.</strong><br>";
echo "<br>⚠️ DELETE THIS FILE: rm public/fix-upload.php<br>";

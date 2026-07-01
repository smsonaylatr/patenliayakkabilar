<?php
/**
 * Diagnose and fix image upload issues
 * Access via: https://patenliayakkabilar.com/diagnose.php
 */

echo "<h2>🔍 Upload Diagnostics</h2>";

$basePath = dirname(__FILE__) . '/../';

// 1. PHP Upload Settings
echo "<h3>1. PHP Upload Settings</h3><pre>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'ON' : 'OFF') . "\n";
echo "upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: '(system default)') . "\n";
echo "sys_temp_dir: " . sys_get_temp_dir() . "\n";
echo "temp writable: " . (is_writable(sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";
echo "</pre>";

// 2. Storage directories
echo "<h3>2. Storage Directories</h3><pre>";
$dirs = [
    'storage/app/public/' => $basePath . 'storage/app/public/',
    'storage/app/public/products/' => $basePath . 'storage/app/public/products/',
    'storage/app/public/livewire-tmp/' => $basePath . 'storage/app/public/livewire-tmp/',
    'storage/framework/cache/' => $basePath . 'storage/framework/cache/',
    'storage/logs/' => $basePath . 'storage/logs/',
];
foreach ($dirs as $label => $dir) {
    $exists = is_dir($dir);
    $writable = $exists ? is_writable($dir) : false;
    echo "$label => " . ($exists ? 'EXISTS' : 'MISSING') . " | " . ($writable ? 'WRITABLE' : 'NOT WRITABLE') . "\n";
    if (!$exists) {
        mkdir($dir, 0777, true);
        echo "  → Created!\n";
    } elseif (!$writable) {
        chmod($dir, 0777);
        echo "  → Fixed permissions!\n";
    }
}
echo "</pre>";

// 3. Storage symlink
echo "<h3>3. Storage Symlink</h3><pre>";
$link = $basePath . 'public/storage';
if (is_link($link)) {
    echo "Symlink: EXISTS → " . readlink($link) . "\n";
    echo "Target accessible: " . (is_dir(readlink($link)) ? 'YES' : 'NO') . "\n";
} elseif (is_dir($link)) {
    echo "Storage is a DIRECTORY (not symlink)\n";
} else {
    echo "Symlink: MISSING\n";
    echo "Creating symlink...\n";
    symlink($basePath . 'storage/app/public', $link);
    echo "Created!\n";
}
echo "</pre>";

// 4. Existing product images
echo "<h3>4. Product Images on Disk</h3><pre>";
$productsDir = $basePath . 'storage/app/public/products/';
if (is_dir($productsDir)) {
    $files = scandir($productsDir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $size = filesize($productsDir . $f);
        $status = $size < 1000 ? '❌ CORRUPT (too small)' : '✅ OK';
        echo "$f → {$size} bytes $status\n";
    }
} else {
    echo "Products directory not found!\n";
}
echo "</pre>";

// 5. Livewire temp files
echo "<h3>5. Livewire Temp Files</h3><pre>";
$tmpDir = $basePath . 'storage/app/public/livewire-tmp/';
if (is_dir($tmpDir)) {
    $files = scandir($tmpDir);
    $count = 0;
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $size = filesize($tmpDir . $f);
        echo "$f → {$size} bytes\n";
        $count++;
    }
    echo "Total: $count files\n";
} else {
    echo "No temp directory\n";
}
echo "</pre>";

// 6. Test file write
echo "<h3>6. Write Test</h3><pre>";
$testFile = $productsDir . '_test_write.tmp';
$testData = str_repeat('X', 10000);
$written = @file_put_contents($testFile, $testData);
if ($written === 10000) {
    echo "Write test: ✅ SUCCESS (wrote 10000 bytes)\n";
    @unlink($testFile);
} else {
    echo "Write test: ❌ FAILED\n";
}
echo "</pre>";

// 7. Check .user.ini
echo "<h3>7. PHP Override (.user.ini)</h3><pre>";
$userIni = $basePath . 'public/.user.ini';
if (file_exists($userIni)) {
    echo file_get_contents($userIni);
} else {
    echo "No .user.ini found - creating one...\n";
    $ini = "upload_max_filesize = 20M\npost_max_size = 25M\nmax_execution_time = 300\nmax_input_time = 300\nmemory_limit = 256M\n";
    file_put_contents($userIni, $ini);
    file_put_contents($basePath . '.user.ini', $ini);
    echo "Created .user.ini\n";
}
echo "</pre>";

// 8. Check Nginx proxy (Plesk often uses Nginx as reverse proxy)
echo "<h3>8. Server Info</h3><pre>";
echo "Server software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "\n";
echo "</pre>";

echo "<br><strong>⚠️ DELETE THIS FILE: rm public/diagnose.php</strong>";

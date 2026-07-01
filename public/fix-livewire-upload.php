<?php
/**
 * Fix Livewire file uploads - diagnose and fix all issues
 * Access: https://patenliayakkabilar.com/fix-livewire-upload.php
 */

$basePath = dirname(__FILE__) . '/../';

echo "<h2>Livewire Upload Fix</h2>";

// 1. PHP upload settings
echo "<h3>1. PHP Settings</h3><pre>";
$uploadMax = ini_get('upload_max_filesize');
$postMax = ini_get('post_max_size');
echo "upload_max_filesize: $uploadMax\n";
echo "post_max_size: $postMax\n";

// Convert to bytes for comparison
function toBytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

$uploadBytes = toBytes($uploadMax);
$postBytes = toBytes($postMax);

if ($uploadBytes < 10 * 1024 * 1024) {
    echo "⚠️ upload_max_filesize is too low! Needs 10M+\n";
} else {
    echo "✅ upload_max_filesize OK\n";
}
if ($postBytes < 12 * 1024 * 1024) {
    echo "⚠️ post_max_size is too low! Needs 12M+\n";
} else {
    echo "✅ post_max_size OK\n";
}
echo "</pre>";

// 2. Create/fix .user.ini with proper settings
$userIniContent = "upload_max_filesize = 50M\npost_max_size = 55M\nmax_execution_time = 300\nmax_input_time = 300\nmemory_limit = 256M\nmax_file_uploads = 20\n";

file_put_contents($basePath . '.user.ini', $userIniContent);
file_put_contents($basePath . 'public/.user.ini', $userIniContent);
echo "✅ Created .user.ini files with proper upload limits<br>";

// 3. Fix directories
$dirsToFix = [
    'storage/app/public/products',
    'storage/app/public/livewire-tmp',
    'storage/app/livewire-tmp',
    'storage/framework/cache',
    'storage/framework/views',
    'storage/framework/sessions',
    'storage/logs',
    'bootstrap/cache',
];

echo "<h3>3. Directory Permissions</h3><pre>";
foreach ($dirsToFix as $dir) {
    $fullPath = $basePath . $dir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0777, true);
        echo "📁 Created: $dir\n";
    }
    chmod($fullPath, 0777);
    echo "✅ $dir → " . (is_writable($fullPath) ? 'WRITABLE' : 'NOT WRITABLE') . "\n";
}
echo "</pre>";

// 4. Fix storage symlink
echo "<h3>4. Storage Symlink</h3><pre>";
$storageLink = $basePath . 'public/storage';
if (is_link($storageLink)) {
    echo "✅ Symlink exists → " . readlink($storageLink) . "\n";
} elseif (is_dir($storageLink)) {
    echo "ℹ️ Storage is a real directory (not symlink) - OK\n";
} else {
    symlink($basePath . 'storage/app/public', $storageLink);
    echo "✅ Created symlink\n";
}
echo "</pre>";

// 5. Test actual file write
echo "<h3>5. Write Test</h3><pre>";
$testData = str_repeat('A', 100000); // 100KB test
$testFile = $basePath . 'storage/app/public/products/_write_test.tmp';
$result = @file_put_contents($testFile, $testData);
if ($result === 100000) {
    echo "✅ Write test passed (100KB written to products/)\n";
    @unlink($testFile);
} else {
    echo "❌ Write test FAILED for products/\n";
}

$testFile2 = $basePath . 'storage/app/public/livewire-tmp/_write_test.tmp';
$result2 = @file_put_contents($testFile2, $testData);
if ($result2 === 100000) {
    echo "✅ Write test passed (100KB written to livewire-tmp/)\n";
    @unlink($testFile2);
} else {
    echo "❌ Write test FAILED for livewire-tmp/\n";
}
echo "</pre>";

// 6. Fix Livewire config to use 'local' disk instead of 'public'
echo "<h3>6. Livewire Config</h3><pre>";
$livewireConfig = $basePath . 'config/livewire.php';
if (file_exists($livewireConfig)) {
    $content = file_get_contents($livewireConfig);
    // Change disk from 'public' to 'local' for temp uploads (more reliable)
    $content = preg_replace(
        "/'disk'\s*=>\s*'public'/",
        "'disk' => 'local'",
        $content,
        1
    );
    file_put_contents($livewireConfig, $content);
    echo "✅ Changed Livewire temp disk to 'local'\n";
}
echo "</pre>";

// 7. Clear all caches
echo "<h3>7. Cache Clear</h3><pre>";
// Clear config cache
$configCacheFile = $basePath . 'bootstrap/cache/config.php';
if (file_exists($configCacheFile)) {
    @unlink($configCacheFile);
    echo "✅ Config cache cleared\n";
}
// Clear route cache
$routeCacheFile = $basePath . 'bootstrap/cache/routes-v7.php';
if (file_exists($routeCacheFile)) {
    @unlink($routeCacheFile);
    echo "✅ Route cache cleared\n";
}
// Clear compiled views
$viewsDir = $basePath . 'storage/framework/views/';
if (is_dir($viewsDir)) {
    $files = glob($viewsDir . '*.php');
    foreach ($files as $file) {
        @unlink($file);
    }
    echo "✅ View cache cleared (" . count($files) . " files)\n";
}
echo "</pre>";

echo "<br><h2 style='color:green'>✅ All fixes applied!</h2>";
echo "<p><strong>ÖNEMLİ:</strong> .user.ini değişikliklerinin etkili olması için 5 dakika bekleyin (PHP-FPM cache'i).</p>";
echo "<p>Sonra admin panelinden görsel yüklemeyi tekrar deneyin.</p>";
echo "<br>⚠️ Bu dosyayı silin: <code>rm public/fix-livewire-upload.php</code>";

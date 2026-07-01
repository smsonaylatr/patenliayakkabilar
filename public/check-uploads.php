<?php
/**
 * Diagnose what's in the corrupt 795-byte uploaded files
 * Access: https://patenliayakkabilar.com/check-uploads.php
 */

$basePath = dirname(__FILE__) . '/../';

echo "<h2>Upload File Analysis</h2>";

// 1. Check products directory for small/corrupt files
echo "<h3>1. Files in storage/app/public/products/</h3><pre>";
$dir = $basePath . 'storage/app/public/products/';
if (is_dir($dir)) {
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..' || $f === '.gitignore') continue;
        $path = $dir . $f;
        $size = filesize($path);
        echo "\n--- $f ($size bytes) ---\n";
        if ($size < 2000) {
            // Show content of small/corrupt files
            $content = file_get_contents($path);
            echo "CONTENT: " . htmlspecialchars(substr($content, 0, 500)) . "\n";
            echo "HEX (first 50 bytes): " . bin2hex(substr($content, 0, 50)) . "\n";
        } else {
            echo "OK - file appears valid\n";
        }
    }
} else {
    echo "Directory not found!\n";
}
echo "</pre>";

// 2. Check livewire-tmp directories
echo "<h3>2. Livewire Temp Files</h3><pre>";
$tmpDirs = [
    'storage/app/livewire-tmp/',
    'storage/app/public/livewire-tmp/',
];
foreach ($tmpDirs as $tmpDir) {
    $fullPath = $basePath . $tmpDir;
    echo "\n--- $tmpDir ---\n";
    if (is_dir($fullPath)) {
        $files = scandir($fullPath);
        $count = 0;
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            $size = filesize($fullPath . $f);
            echo "$f → $size bytes\n";
            if ($size < 2000) {
                $content = file_get_contents($fullPath . $f);
                echo "  CONTENT: " . htmlspecialchars(substr($content, 0, 300)) . "\n";
            }
            $count++;
        }
        echo "Total: $count files\n";
    } else {
        echo "Directory does not exist\n";
    }
}
echo "</pre>";

// 3. Check if Livewire upload route exists
echo "<h3>3. Livewire Upload Route Test</h3><pre>";
require $basePath . 'vendor/autoload.php';
$app = require_once $basePath . 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle(Illuminate\Http\Request::capture());

$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    $uri = $route->uri();
    if (stripos($uri, 'livewire') !== false && stripos($uri, 'upload') !== false) {
        echo "Route: " . $route->methods()[0] . " /" . $uri . "\n";
        echo "Action: " . ($route->getActionName()) . "\n";
        echo "Middleware: " . implode(', ', $route->gatherMiddleware()) . "\n";
    }
}
echo "</pre>";

// 4. Check PHP config that's ACTUALLY active
echo "<h3>4. Active PHP Config</h3><pre>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'ON' : 'OFF') . "\n";
echo "upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) . "\n";
echo "temp writable: " . (is_writable(ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";
echo "</pre>";

// 5. Check Livewire config
echo "<h3>5. Livewire Config</h3><pre>";
$config = config('livewire.temporary_file_upload');
echo "disk: " . ($config['disk'] ?? 'default') . "\n";
echo "directory: " . ($config['directory'] ?? 'livewire-tmp') . "\n";
echo "rules: " . json_encode($config['rules'] ?? 'default') . "\n";
echo "max_upload_time: " . ($config['max_upload_time'] ?? '5') . " min\n";
echo "</pre>";

echo "<br>⚠️ DELETE: rm public/check-uploads.php";

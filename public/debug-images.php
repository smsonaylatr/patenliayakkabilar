<?php
require dirname(__FILE__) . '/../vendor/autoload.php';
$app = require_once dirname(__FILE__) . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(Illuminate\Http\Request::capture());

echo "<pre>";

// 1. Clear ALL caches first
echo "=== CLEARING CACHES ===\n";
Illuminate\Support\Facades\Artisan::call('config:clear');
echo Illuminate\Support\Facades\Artisan::output();
Illuminate\Support\Facades\Artisan::call('cache:clear');
echo Illuminate\Support\Facades\Artisan::output();
Illuminate\Support\Facades\Artisan::call('view:clear');
echo Illuminate\Support\Facades\Artisan::output();

// 2. Show current Livewire config
echo "\n=== LIVEWIRE TEMP CONFIG (LIVE) ===\n";
$config = config('livewire.temporary_file_upload');
echo "disk: " . ($config['disk'] ?? 'NOT SET') . "\n";
echo "directory: " . ($config['directory'] ?? 'NOT SET') . "\n";
echo "rules: " . json_encode($config['rules'] ?? []) . "\n";

// 3. Check Product 7 images
echo "\n=== PRODUCT 7 IMAGES ===\n";
$images = \App\Models\ProductImage::where('product_id', 7)->orderBy('sort_order')->get();
foreach ($images as $img) {
    $diskPath = storage_path('app/public/' . $img->image_path);
    $exists = file_exists($diskPath);
    $size = $exists ? filesize($diskPath) : 0;
    
    echo "ID:{$img->id} | Sort:{$img->sort_order} | Path: {$img->image_path}\n";
    echo "  Disk: $diskPath\n";
    echo "  Exists: " . ($exists ? "YES ({$size} bytes)" : "NO ❌") . "\n";
    
    if ($exists && $size < 2000) {
        $content = file_get_contents($diskPath);
        echo "  ⚠️ TINY FILE! Content:\n  " . substr($content, 0, 500) . "\n";
    }
    echo "\n";
}

// 4. Check livewire-tmp directories
echo "=== LIVEWIRE TEMP FILES ===\n";
$dirs = [
    'storage/app/livewire-tmp' => storage_path('app/livewire-tmp'),
    'storage/app/public/livewire-tmp' => storage_path('app/public/livewire-tmp'),
];
foreach ($dirs as $label => $dir) {
    echo "$label: ";
    if (!is_dir($dir)) { echo "DOES NOT EXIST\n"; continue; }
    $files = glob($dir . '/*');
    echo count($files) . " files\n";
    foreach (array_slice($files, 0, 5) as $f) {
        echo "  " . basename($f) . " (" . filesize($f) . " bytes)\n";
    }
}

echo "</pre>";

<?php
require dirname(__FILE__) . '/../vendor/autoload.php';
$app = require_once dirname(__FILE__) . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(Illuminate\Http\Request::capture());

echo "<pre>";
$images = \App\Models\ProductImage::orderBy('id')->get();
foreach ($images as $img) {
    $diskPath = storage_path('app/public/' . $img->image_path);
    $exists = file_exists($diskPath);
    $size = $exists ? filesize($diskPath) : 0;
    $url = '/storage/' . $img->image_path;
    
    echo "ID: {$img->id} | Product: {$img->product_id} | Sort: {$img->sort_order}\n";
    echo "  DB path: {$img->image_path}\n";
    echo "  Disk path: $diskPath\n";
    echo "  File exists: " . ($exists ? "YES ({$size} bytes)" : "NO ❌") . "\n";
    echo "  URL: $url\n";
    
    // Try to read first bytes to check if it's valid
    if ($exists && $size < 1000) {
        echo "  ⚠️ SMALL FILE! Content: " . bin2hex(substr(file_get_contents($diskPath), 0, 50)) . "\n";
        echo "  As text: " . substr(file_get_contents($diskPath), 0, 200) . "\n";
    }
    echo "\n";
}
echo "</pre>";

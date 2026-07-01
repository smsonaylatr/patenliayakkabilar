<?php
/**
 * Emergency image fix - copies product images to storage and creates DB records
 * Access via: https://patenliayakkabilar.com/fix-images.php
 * DELETE THIS FILE AFTER USE
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

use App\Models\Product;
use App\Models\ProductImage;

$basePath = dirname(__FILE__) . '/../';
$source = $basePath . 'resources/images/products/';
$dest = $basePath . 'storage/app/public/products/';

// Create destination directory
if (!is_dir($dest)) {
    mkdir($dest, 0775, true);
    echo "📁 Created directory: storage/app/public/products/<br>";
}

// Ensure storage link exists
$storageLink = $basePath . 'public/storage';
if (!is_link($storageLink) && !is_dir($storageLink)) {
    symlink($basePath . 'storage/app/public', $storageLink);
    echo "🔗 Created storage symlink<br>";
} else {
    echo "🔗 Storage symlink already exists<br>";
}

// Copy images and create DB records
$products = Product::orderBy('id')->get();
$count = 0;

foreach ($products as $index => $product) {
    $imageNumber = $index + 1;
    $filename = "{$imageNumber}.webp";
    $sourceFile = $source . $filename;
    $destFile = $dest . $filename;
    $imagePath = "products/{$filename}";

    // Copy image file
    if (file_exists($sourceFile)) {
        copy($sourceFile, $destFile);
        chmod($destFile, 0664);
        echo "✅ Image copied: {$filename} → storage/app/public/products/{$filename}<br>";
    } else {
        echo "⚠️ Source not found: resources/images/products/{$filename}<br>";
    }

    // Create DB record
    ProductImage::firstOrCreate(
        ['product_id' => $product->id, 'image_path' => $imagePath],
        ['sort_order' => 0]
    );
    echo "✅ DB record: Product #{$product->id} ({$product->name}) → {$imagePath}<br>";
    $count++;
}

echo "<br>🎉 Done! {$count} product images processed.<br>";
echo "<br>⚠️ DELETE THIS FILE: rm public/fix-images.php<br>";

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// TWİNGERS veya TWINGERS ürünlerini bul
echo "=== ÜRÜNLER ===" . PHP_EOL;
$products = \App\Models\Product::where('name', 'LIKE', '%NGER%')
    ->orWhere('name', 'LIKE', '%Twinger%')
    ->orWhere('name', 'LIKE', '%twinger%')
    ->orWhere('name', 'LIKE', '%TWİNGER%')
    ->get(['id', 'name', 'sku']);

foreach ($products as $p) {
    echo "  ID: {$p->id} | Name: {$p->name} | SKU: {$p->sku}" . PHP_EOL;
    
    $variants = \App\Models\ProductVariant::where('product_id', $p->id)->get(['id', 'sku', 'size']);
    echo "  Varyantlar (" . $variants->count() . "):" . PHP_EOL;
    foreach ($variants as $v) {
        echo "    ID: {$v->id} | Size: {$v->size} | SKU: {$v->sku}" . PHP_EOL;
    }
    echo PHP_EOL;
}

// TW-NGERS veya TW_NGERS içeren SKU'ları bul
echo "=== HATALI SKU'LAR ===" . PHP_EOL;
$badVariants = \App\Models\ProductVariant::where('sku', 'LIKE', '%TW-NGERS%')
    ->orWhere('sku', 'LIKE', '%TW_NGERS%')
    ->get(['id', 'sku', 'product_id']);
echo "Hatalı varyant SKU: " . $badVariants->count() . PHP_EOL;
foreach ($badVariants as $v) {
    echo "  ID: {$v->id} | Product: {$v->product_id} | SKU: {$v->sku}" . PHP_EOL;
}

$badProducts = \App\Models\Product::where('sku', 'LIKE', '%TW-NGERS%')
    ->orWhere('sku', 'LIKE', '%TW_NGERS%')
    ->get(['id', 'name', 'sku']);
echo "Hatalı ürün SKU: " . $badProducts->count() . PHP_EOL;
foreach ($badProducts as $p) {
    echo "  ID: {$p->id} | Name: {$p->name} | SKU: {$p->sku}" . PHP_EOL;
}

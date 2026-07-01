<?php
/**
 * Clear all product caches
 * Access via: https://patenliayakkabilar.com/clear-cache.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Cache;

Cache::forget('home_product_grid');
Cache::forget('best_seller_carousel_products');
Cache::flush();

echo "✅ Tüm cache temizlendi!<br>";
echo "Anasayfayı yenileyin.<br>";
echo "<br>⚠️ Bu dosyayı silin: rm public/clear-cache.php";

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$product = App\Models\Product::whereNotNull('description')->where('description', '!=', '')->first();
if ($product) {
    file_put_contents('desc.html', $product->description);
    echo "Done with: " . $product->slug;
} else {
    echo "Not found";
}

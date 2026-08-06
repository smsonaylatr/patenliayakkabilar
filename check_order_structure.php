<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

$order = Order::with(['items.product.images', 'items.variant'])->first();

if ($order) {
    echo "ORDER ID: {$order->id} | NO: {$order->order_number}\n";
    echo "NAME: {$order->customer_name} | PHONE: {$order->customer_phone} | EMAIL: {$order->customer_email}\n";
    echo "ADDRESS: {$order->shipping_address} | CITY: {$order->shipping_city} | DISTRICT: {$order->shipping_district}\n\n";
    
    echo "ITEMS:\n";
    foreach ($order->items as $item) {
        echo "   - ITEM ID: {$item->id} | NAME: {$item->product_name} | SIZE/VARIANT: " . json_encode($item->options) . " | QTY: {$item->quantity} | PRICE: {$item->unit_price}\n";
        if ($item->variant) {
            echo "     VARIANT COLOR: " . json_encode($item->variant->color) . " | SIZE: {$item->variant->size}\n";
        }
        if ($item->product) {
            echo "     PRODUCT MAIN IMG: " . ($item->product->images->first()?->image_url ?? 'Yok') . "\n";
        }
    }
} else {
    echo "Sipariş bulunamadı.\n";
}

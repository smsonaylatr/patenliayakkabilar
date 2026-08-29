<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = \App\Models\Order::where('order_number', 'TR718228')->first();

if (!$order) {
    echo "Sipariş bulunamadı\n";
    exit(1);
}

echo "Sipariş bulundu: #{$order->order_number}\n";
echo "Mevcut kargo kodu: " . ($order->cargo_tracking_code ?: 'YOK') . "\n";
echo "Mevcut kargo firması: " . ($order->cargo_company ?: 'YOK') . "\n\n";

echo "Kargo kodu oluşturuluyor...\n";
$result = app(\App\Services\PoregoApiService::class)->createBarcode($order);
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

$order->refresh();
echo "Güncel kargo kodu: " . ($order->cargo_tracking_code ?: 'YOK') . "\n";
echo "Güncel kargo firması: " . ($order->cargo_company ?: 'YOK') . "\n";

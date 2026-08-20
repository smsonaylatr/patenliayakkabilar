<?php 
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = App\Models\Order::latest()->first();
if (!$order) {
    echo "No orders found\n";
    exit;
}
echo "Order found: " . $order->order_number . "\n";

$service = app(App\Services\PoregoApiService::class);

$fakeData = [
    'data' => [
        [
            'platformCargoTrackingNumber' => '123456789',
            'platformCargoCompany' => 'Aras Kargo',
            'status' => 'SHIPPED',
            'deliveryDate' => '2026-08-18',
            'deliveryLocation' => 'ISTANBUL',
            'cargoMessage' => 'Transfer Aşamasında'
        ]
    ]
];

use Illuminate\Support\Facades\Http;

$ref = new ReflectionClass($service);
$apiKeyProp = $ref->getProperty('apiKey');
$apiKeyProp->setAccessible(true);
$apiKeyProp->setValue($service, 'fake-api-key');
$apiSecretProp = $ref->getProperty('apiSecret');
$apiSecretProp->setAccessible(true);
$apiSecretProp->setValue($service, 'fake-api-secret');

Http::fake([
    '*' => Http::response($fakeData, 200)
]);

$order = App\Models\Order::first();
if ($order) {
    $res = $service->fetchAndSaveOrderTracking($order);
    echo "Parsed Result:\n";
    print_r($res);
} else {
    echo "No orders to test with.\n";
}


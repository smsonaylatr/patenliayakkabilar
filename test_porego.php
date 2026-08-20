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
// Reflect to access protected methods/properties to check URL and keys
$ref = new ReflectionClass($service);
$apiUrlProp = $ref->getProperty('apiUrl');
$apiUrlProp->setAccessible(true);
$apiUrl = $apiUrlProp->getValue($service);
echo "API URL: " . $apiUrl . "\n";

$apiKeyProp = $ref->getProperty('apiKey');
$apiKeyProp->setAccessible(true);
$apiKey = $apiKeyProp->getValue($service);
echo "API KEY exists: " . (!empty($apiKey) ? 'Yes' : 'No') . "\n";

if (empty($apiKey)) {
    $apiKey = \App\Models\Setting::where('key', 'porego_api_key')->value('value');
    echo "DB API KEY exists: " . (!empty($apiKey) ? 'Yes' : 'No') . "\n";
}

$res = $service->fetchAndSaveOrderTracking($order);
echo "Result:\n";
print_r($res);

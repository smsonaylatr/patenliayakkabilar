<?php
// FINAL APPROACH: Test if we can use Porego's e-commerce integration webhooks
// Porego has Shopify/WooCommerce integrations - maybe there's a webhook endpoint that accepts product data
$env = file_get_contents(__DIR__ . '/.env');
preg_match('/POREGO_API_KEY=(.*)/', $env, $k);
preg_match('/POREGO_API_SECRET=(.*)/', $env, $s);
$apiKey = trim($k[1] ?? '');
$apiSecret = trim($s[1] ?? '');
$headers = [
    "X-Api-Key: {$apiKey}",
    "X-Api-Secret: {$apiSecret}",
    "Accept: application/json",
    "Content-Type: application/json",
];
$merchantBase = 'https://back.porego.com/depokargo/api/v1/merchant-api/v1';

// Test the /orders endpoint with different variations of product data
$items = [['sku' => 'TEST-001', 'name' => 'Alessio Patenli Spor Ayakkabı (Beden: 35)', 'quantity' => 1, 'price' => 2200.00]];

$variations = [
    // 1. WooCommerce-style line_items
    'woo_line_items' => ['line_items' => $items],
    
    // 2. products as array of objects (not JSON string)
    'products_array' => ['products' => $items],
    
    // 3. Both items AND products as array
    'items_and_products_array' => ['items' => $items, 'products' => $items],

    // 4. products as JSON string + items as array
    'products_json_items_array' => ['items' => $items, 'products' => json_encode($items, JSON_UNESCAPED_UNICODE)],
    
    // 5. Shopify-style with storeId
    'with_storeId' => ['items' => $items, 'storeId' => null, 'source' => 'API', 'platform' => 'API'],
    
    // 6. With source = WOOCOMMERCE
    'woo_source' => ['items' => $items, 'source' => 'WOOCOMMERCE', 'platform' => 'WOOCOMMERCE'],
    
    // 7. lineItems (camelCase)
    'lineItems_camel' => ['lineItems' => $items, 'items' => $items],
    
    // 8. orderProducts
    'orderProducts' => ['orderProducts' => $items, 'items' => $items],
];

foreach ($variations as $name => $extraFields) {
    $t = time() . rand(10, 99);
    $payload = array_merge([
        'customerName' => 'Test',
        'customerSurname' => $name,
        'customerPhone' => '5551234567',
        'address' => 'Test Sok No:1',
        'city' => 'İstanbul',
        'district' => 'Kadıköy',
        'neighbourhood' => 'Caferağa',
        'paymentType' => 'PREPAID',
        'totalWeight' => 1.0,
        'totalDeci' => 1.0,
        'platformOrderId' => 'V-' . $t,
        'platformOrderNumber' => 'V-' . $t,
        'notes' => '1x Alessio Patenli Spor Ayakkabı (Beden: 35)',
    ], $extraFields);
    
    $ch = curl_init("{$merchantBase}/orders");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($res, true);
    $orderNo = $data['orderNumber'] ?? 'FAILED';
    echo "=== [{$name}] Status: {$code} | Order: {$orderNo} ===\n";
    
    if ($code !== 200) {
        echo "Error: " . substr($res, 0, 200) . "\n";
    }
    
    // Cancel the order
    if (!empty($data['orderNumber'])) {
        $ch2 = curl_init("{$merchantBase}/orders/{$data['orderNumber']}/cancel");
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_exec($ch2);
        curl_close($ch2);
    }
    echo "\n";
}

echo "\n\n=== TESTING: Fetch label HTML for existing order ===\n";
// Now let's check an existing successful order to see what products look like in the label
// First let's get the latest orders
$ch = curl_init("{$merchantBase}/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "GET /orders => Status: {$code}\n";
$ordersData = json_decode($res, true);
if (is_array($ordersData)) {
    $content = $ordersData['content'] ?? $ordersData;
    if (is_array($content)) {
        echo "Orders count: " . count($content) . "\n";
        foreach (array_slice($content, 0, 3) as $o) {
            echo "  #{$o['orderNumber']} - {$o['customerName']} {$o['customerSurname']} | Status: {$o['status']} | Notes: " . ($o['notes'] ?? 'null') . " | Products: " . json_encode($o['products'] ?? null) . "\n";
        }
    }
}

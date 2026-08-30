<?php

$apiKey = 'pk_cfc393e0be164f4688117d2dd9f8c5d7';
$apiSecret = 'sk_1b60bade9d92402fe69f80010aed2d59d9d67af6e6ae363d3edd05f811939e0d';
$baseUrl = 'https://back.porego.com/depokargo/api/v1/merchant-api/v1';
$internalBaseUrl = 'https://back.porego.com/depokargo/api/v1';

function makeRequest($method, $url, $payload = null, $headers = []) {
    global $apiKey, $apiSecret;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $defaultHeaders = [
        'X-Api-Key: ' . $apiKey,
        'X-Api-Secret: ' . $apiSecret,
        'Accept: application/json',
    ];
    
    if ($payload) {
        $defaultHeaders[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }
    
    $headers = array_merge($defaultHeaders, $headers);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST') curl_setopt($ch, CURLOPT_POST, true);
    elseif ($method !== 'GET') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['status' => $httpCode, 'body' => $response];
}

$orderNum = 'TEST-' . time();

$basePayload = [
    'customerName' => 'Test',
    'customerSurname' => 'User',
    'customerPhone' => '05555555555',
    'customerEmail' => 'test@example.com',
    'address' => 'Test Mah. Test Sok. No:1',
    'city' => 'İstanbul',
    'district' => 'Kadıköy',
    'neighborhood' => 'Caferağa',
    'paymentType' => 'PREPAID',
    'platform' => 'WOOCOMMERCE',
    'orderNumber' => $orderNum,
    'totalAmount' => 100,
    'currency' => 'TRY'
];

$itemsArray = [
    [
        'sku' => 'TEST-SKU-1',
        'name' => 'Test Product',
        'quantity' => 1,
        'price' => 100
    ]
];

$productsJsonString = json_encode($itemsArray);

echo "--- Starting Porego Tests ---\n\n";

// a) Create with items
echo "Test A: POST /orders with items array\n";
$payloadA = array_merge($basePayload, ['items' => $itemsArray, 'orderNumber' => $orderNum . '-A']);
$resA = makeRequest('POST', "$baseUrl/orders", $payloadA);
echo "Status: {$resA['status']}\nBody: {$resA['body']}\n\n";

// k) ONLY items, without products
echo "Test K: POST /orders with ONLY items\n";
$payloadK = array_merge($basePayload, ['items' => $itemsArray, 'orderNumber' => $orderNum . '-K']);
$resK = makeRequest('POST', "$baseUrl/orders", $payloadK);
echo "Status: {$resK['status']}\nBody: {$resK['body']}\n\n";

// g) Products as JSON string
echo "Test G: POST /orders with products as JSON string\n";
$payloadG = array_merge($basePayload, ['products' => $productsJsonString, 'orderNumber' => $orderNum . '-G']);
$resG = makeRequest('POST', "$baseUrl/orders", $payloadG);
echo "Status: {$resG['status']}\nBody: {$resG['body']}\n\n";

// b) PATCH /orders/{orderNumber}
echo "Test B: PATCH /orders/{$orderNum}-A\n";
$resB = makeRequest('PATCH', "$baseUrl/orders/{$orderNum}-A", ['products' => $productsJsonString, 'items' => $itemsArray]);
echo "Status: {$resB['status']}\nBody: {$resB['body']}\n\n";

// c) POST /orders/{orderNumber}/items
echo "Test C: POST /orders/{$orderNum}-A/items\n";
$resC = makeRequest('POST', "$baseUrl/orders/{$orderNum}-A/items", ['items' => $itemsArray]);
echo "Status: {$resC['status']}\nBody: {$resC['body']}\n\n";

// d) POST /orders/{orderNumber}/products
echo "Test D: POST /orders/{$orderNum}-A/products\n";
$resD = makeRequest('POST', "$baseUrl/orders/{$orderNum}-A/products", ['products' => $itemsArray]);
echo "Status: {$resD['status']}\nBody: {$resD['body']}\n\n";

// e) POST /orders/{orderNumber}/update
echo "Test E: POST /orders/{$orderNum}-A/update\n";
$resE = makeRequest('POST', "$baseUrl/orders/{$orderNum}-A/update", ['items' => $itemsArray]);
echo "Status: {$resE['status']}\nBody: {$resE['body']}\n\n";

// f) Internal API POST /orders
echo "Test F: Internal API POST /orders\n";
$payloadF = array_merge($basePayload, ['items' => $itemsArray, 'products' => $itemsArray, 'orderNumber' => $orderNum . '-F']);
$resF = makeRequest('POST', "$internalBaseUrl/orders", $payloadF);
echo "Status: {$resF['status']}\nBody: {$resF['body']}\n\n";

// h) storeId
echo "Test H: POST /orders with storeId\n";
$payloadH = array_merge($basePayload, ['items' => $itemsArray, 'storeId' => '1', 'orderNumber' => $orderNum . '-H']);
$resH = makeRequest('POST', "$baseUrl/orders", $payloadH);
echo "Status: {$resH['status']}\nBody: {$resH['body']}\n\n";

// i) POST /ecommerce/orders
echo "Test I: POST /ecommerce/orders\n";
$payloadI = array_merge($basePayload, ['items' => $itemsArray, 'orderNumber' => $orderNum . '-I']);
$resI = makeRequest('POST', "$baseUrl/ecommerce/orders", $payloadI);
echo "Status: {$resI['status']}\nBody: {$resI['body']}\n\n";

// Also test store parameter
echo "Test J: POST /orders with 'store'\n";
$payloadJ = array_merge($basePayload, ['items' => $itemsArray, 'store' => 'woocommerce', 'orderNumber' => $orderNum . '-J']);
$resJ = makeRequest('POST', "$baseUrl/orders", $payloadJ);
echo "Status: {$resJ['status']}\nBody: {$resJ['body']}\n\n";

// Cancel created orders
foreach (['A', 'K', 'G', 'F', 'H', 'J'] as $sfx) {
    makeRequest('DELETE', "$baseUrl/orders/{$orderNum}-{$sfx}");
}

echo "Done.\n";

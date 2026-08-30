<?php

$apiKey = 'pk_cfc393e0be164f4688117d2dd9f8c5d7';
$apiSecret = 'sk_1b60bade9d92402fe69f80010aed2d59d9d67af6e6ae363d3edd05f811939e0d';
$baseUrl = 'https://back.porego.com/depokargo/api/v1/merchant-api/v1';

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

$orderNum = 'TEST-2-' . time();

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
    'platformOrderId' => $orderNum,
    'platformOrderNumber' => $orderNum,
    'totalAmount' => 100,
    'currency' => 'TRY'
];

$itemsArray = [
    [
        'sku' => 'TEST-SKU-1',
        'name' => 'Test Product',
        'quantity' => 1,
        'price' => 100,
        'productName' => 'Test Product',
        'count' => 1,
        'amount' => 100
    ]
];

echo "--- Starting Porego Tests 2 ---\n\n";

// Test 1: shipments endpoint
echo "Test 1: POST /shipments\n";
$res1 = makeRequest('POST', "$baseUrl/shipments", array_merge($basePayload, ['items' => $itemsArray]));
echo "Status: {$res1['status']}\nBody: {$res1['body']}\n\n";

// Test 2: orderItems instead of items
echo "Test 2: POST /orders with orderItems\n";
$res2 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['orderItems' => $itemsArray]));
echo "Status: {$res2['status']}\nBody: {$res2['body']}\n\n";

// Test 3: lines
echo "Test 3: POST /orders with lines\n";
$res3 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['lines' => $itemsArray]));
echo "Status: {$res3['status']}\nBody: {$res3['body']}\n\n";

// Test 4: details
echo "Test 4: POST /orders with details\n";
$res4 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['details' => $itemsArray]));
echo "Status: {$res4['status']}\nBody: {$res4['body']}\n\n";

// Test 5: cargoNotes and other note fields
echo "Test 5: POST /orders with various text fields\n";
$text = "1x Test Product (SKU-1)";
$res5 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, [
    'cargoNote' => $text,
    'shippingNote' => $text,
    'waybillNote' => $text,
    'waybillDescription' => $text,
    'content' => $text,
    'contents' => $text,
    'packageContent' => $text,
    'shipmentContent' => $text,
    'labelNote' => $text,
    'printNote' => $text,
    'customerNote' => $text,
]));
echo "Status: {$res5['status']}\nBody: {$res5['body']}\n\n";

// Check if we can GET the created order from Test 5
if ($res5['status'] == 200) {
    $orderData = json_decode($res5['body'], true);
    if (isset($orderData['orderNumber'])) {
        $poregoOrderNumber = $orderData['orderNumber'];
        echo "Test 6: GET /orders/{$poregoOrderNumber}\n";
        $res6 = makeRequest('GET', "$baseUrl/orders/{$poregoOrderNumber}");
        echo "Status: {$res6['status']}\nBody: {$res6['body']}\n\n";
    }
}

echo "Done.\n";

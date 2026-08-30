<?php

$apiKey = 'pk_cfc393e0be164f4688117d2dd9f8c5d7';
$apiSecret = 'sk_1b60bade9d92402fe69f80010aed2d59d9d67af6e6ae363d3edd05f811939e0d';
$baseUrl = 'https://back.porego.com/depokargo/api/v1/merchant-api/v1';

function makeRequest($method, $url, $payload = null) {
    global $apiKey, $apiSecret;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $headers = [
        'X-Api-Key: ' . $apiKey,
        'X-Api-Secret: ' . $apiSecret,
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($payload) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    if ($method === 'POST') curl_setopt($ch, CURLOPT_POST, true);
    elseif ($method !== 'GET') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['status' => $httpCode, 'body' => $response];
}

$orderNum = 'TEST-4-' . time();
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
    'totalAmount' => 100,
    'currency' => 'TRY'
];

$itemsArray = [
    ['sku' => 'TEST-1', 'name' => 'Test', 'quantity' => 1, 'price' => 100]
];

echo "Test 1: products as ARRAY\n";
$res1 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['products' => $itemsArray]));
echo "Status: {$res1['status']}\nBody: {$res1['body']}\n\n";

echo "Test 2: packages\n";
$res2 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['packages' => [['items' => $itemsArray]]]));
echo "Status: {$res2['status']}\nBody: {$res2['body']}\n\n";

echo "Test 3: shipmentPackages\n";
$res3 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['shipmentPackages' => [['products' => $itemsArray]]]));
echo "Status: {$res3['status']}\nBody: {$res3['body']}\n\n";

echo "Done.\n";

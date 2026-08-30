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

$orderNum = 'TEST-3-' . time();

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
        'price' => 100
    ]
];

echo "Test: Create Order\n";
$createRes = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['items' => $itemsArray]));
echo "Status: {$createRes['status']}\nBody: {$createRes['body']}\n\n";

if ($createRes['status'] == 200) {
    $orderData = json_decode($createRes['body'], true);
    $poregoOrderNumber = $orderData['orderNumber'];
    
    echo "Test: /createbarcode\n";
    $barcodePayload = [
        'orderNumber' => $poregoOrderNumber,
        'platformOrderId' => $orderNum,
        'platformOrderNumber' => $orderNum,
        'products' => json_encode($itemsArray, JSON_UNESCAPED_UNICODE),
        'items' => $itemsArray,
        'productInfo' => '1x Test Product',
        'note' => 'Test Note'
    ];
    $barcodeRes = makeRequest('POST', "$baseUrl/createbarcode", $barcodePayload);
    echo "Status: {$barcodeRes['status']}\nBody: {$barcodeRes['body']}\n\n";
}

echo "Done.\n";

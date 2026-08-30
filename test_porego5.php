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

$basePayload = [
    'customerName' => 'Test',
    'customerSurname' => 'User',
    'customerPhone' => '05555555555',
    'customerEmail' => 'test@example.com',
    'address' => 'Test Mah. Test Sok. No:1',
    'city' => 'İstanbul',
    'district' => 'Kadıköy',
    'paymentType' => 'PREPAID',
    'totalAmount' => 100,
    'currency' => 'TRY'
];

echo "Test 1: notes\n";
$res1 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['notes' => 'PRODUCT INFO HERE', 'orderNumber' => 'TN1']));
echo "Status: {$res1['status']}\nBody: {$res1['body']}\n\n";

echo "Test 2: note\n";
$res2 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['note' => 'PRODUCT INFO HERE', 'orderNumber' => 'TN2']));
echo "Status: {$res2['status']}\nBody: {$res2['body']}\n\n";

echo "Test 3: description\n";
$res3 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['description' => 'PRODUCT INFO HERE', 'orderNumber' => 'TN3']));
echo "Status: {$res3['status']}\nBody: {$res3['body']}\n\n";

echo "Test 4: cargoNote\n";
$res4 = makeRequest('POST', "$baseUrl/orders", array_merge($basePayload, ['cargoNote' => 'PRODUCT INFO HERE', 'orderNumber' => 'TN4']));
echo "Status: {$res4['status']}\nBody: {$res4['body']}\n\n";

echo "Done.\n";

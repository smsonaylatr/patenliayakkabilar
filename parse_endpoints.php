<?php
$env = file_get_contents(__DIR__ . '/.env');
preg_match('/POREGO_API_KEY=(.*)/', $env, $k);
preg_match('/POREGO_API_SECRET=(.*)/', $env, $s);
preg_match('/POREGO_API_URL=(.*)/', $env, $u);

$apiKey = trim($k[1] ?? '');
$apiSecret = trim($s[1] ?? '');
$apiUrl = trim($u[1] ?? '') ?: 'https://back.porego.com/depokargo/api/v1/merchant-api/v1';

$testPayload = [
    'customerName' => 'Test',
    'customerSurname' => 'Deneme',
    'customerPhone' => '5551234567',
    'customerEmail' => 'test@patenliayakkabilar.com',
    'address' => 'Test Mah. Test Sok. No:1',
    'city' => 'İstanbul',
    'district' => 'Kadıköy',
    'neighbourhood' => 'Caferağa',
    'paymentType' => 'PREPAID',
    'totalWeight' => 1.0,
    'totalDeci' => 1.0,
    'items' => [
        [
            'sku' => 'TEST-SKU-001',
            'name' => 'Alessio Patenli Spor Ayakkabı (Beden: 34)',
            'quantity' => 1,
            'price' => 1999.90,
        ]
    ],
    'platformOrderId' => 'TEST-' . time(),
    'platformOrderNumber' => 'TEST-' . time(),
    'notes' => 'Test Siparişi',
];

$ch = curl_init("{$apiUrl}/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Api-Key: {$apiKey}",
    "X-Api-Secret: {$apiSecret}",
    "Accept: application/json",
    "Content-Type: application/json",
]);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "POST /orders status: {$code}\n";
echo "Response: {$res}\n\n";

$created = json_decode($res, true);
if (!empty($created['orderNumber'])) {
    $orderNo = $created['orderNumber'];
    echo "Querying created order: {$orderNo}\n";
    $ch2 = curl_init("{$apiUrl}/orders/{$orderNo}");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        "X-Api-Key: {$apiKey}",
        "X-Api-Secret: {$apiSecret}",
        "Accept: application/json",
    ]);
    $res2 = curl_exec($ch2);
    curl_close($ch2);
    echo "GET /orders/{$orderNo}: {$res2}\n\n";

    // Now cancel test order
    $ch3 = curl_init("{$apiUrl}/orders/{$orderNo}/cancel");
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch3, CURLOPT_HTTPHEADER, [
        "X-Api-Key: {$apiKey}",
        "X-Api-Secret: {$apiSecret}",
        "Accept: application/json",
    ]);
    $res3 = curl_exec($ch3);
    curl_close($ch3);
    echo "Cancel result: {$res3}\n";
}




















































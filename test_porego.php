<?php 
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\Http;

$apiKey = env('POREGO_API_KEY');
$apiSecret = env('POREGO_API_SECRET');
$apiUrl = env('POREGO_API_URL');

$resp = Http::withHeaders([
    'X-Api-Key' => $apiKey,
    'X-Api-Secret' => $apiSecret,
    'Accept' => 'application/json',
])->timeout(10)->get("{$apiUrl}/orders", ['page' => 0, 'size' => 50]);

foreach ($resp->json()['content'] as $o) {
    if ($o['platformOrderNumber'] === 'TR639133') {
        echo json_encode($o, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;
    }
}

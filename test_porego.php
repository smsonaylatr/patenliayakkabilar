<?php 
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\Http;

use App\Models\Setting;

$apiKey = Setting::where('key', 'porego_api_key')->value('value') ?: env('POREGO_API_KEY');
$apiSecret = Setting::where('key', 'porego_api_secret')->value('value') ?: env('POREGO_API_SECRET');
$apiUrl = Setting::where('key', 'porego_api_url')->value('value') ?: env('POREGO_API_URL', 'https://back.porego.com/depokargo/api/v1/merchant-api/v1');

$resp = Http::withHeaders([
    'X-Api-Key' => $apiKey,
    'X-Api-Secret' => $apiSecret,
    'Accept' => 'application/json',
])->timeout(10)->get("{$apiUrl}/orders", ['page' => 0, 'size' => 10]);

if ($resp->successful()) {
    $content = $resp->json()['content'] ?? [];
    foreach (array_slice($content, 0, 5) as $idx => $o) {
        echo "=== ORDER " . ($idx + 1) . " ===\n";
        echo json_encode($o, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    }
} else {
    echo "Error: " . $resp->status() . " - " . $resp->body();
}


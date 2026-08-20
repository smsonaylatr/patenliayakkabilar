<?php 
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = env('POREGO_API_KEY');
$apiSecret = env('POREGO_API_SECRET');
$apiUrl = env('POREGO_API_URL');

// Tüm siparişleri paginate ederek TR639133'ü bul
echo "=== TÜM SİPARİŞLERDE TR639133 ARANYOR ===\n";

$page = 0;
$found = null;

while ($page < 10) {
    $resp = Http::withHeaders([
        'X-Api-Key' => $apiKey,
        'X-Api-Secret' => $apiSecret,
        'Accept' => 'application/json',
    ])->timeout(10)->get("{$apiUrl}/orders?page={$page}&size=20");
    
    if (!$resp->successful()) {
        echo "Sayfa {$page} hatası: " . $resp->status() . "\n";
        break;
    }
    
    $body = $resp->json();
    $content = $body['content'] ?? [];
    $totalPages = $body['totalPages'] ?? 1;
    
    echo "Sayfa {$page}/{$totalPages} - {$body['numberOfElements']} sipariş\n";
    
    foreach ($content as $order) {
        $pon = $order['platformOrderNumber'] ?? '?';
        $tid = $order['trackingNumber'] ?? '?';
        $ptid = $order['platformCargoTrackingNumber'] ?? '-';
        $st = $order['status'] ?? '?';
        echo "  {$pon} | porego_tracking: {$tid} | platform_cargo: {$ptid} | status: {$st}\n";
        
        if ($pon === 'TR639133') {
            $found = $order;
            echo "\n  >>> TR639133 BULUNDU! <<<\n";
        }
    }
    
    if ($body['last'] ?? false) break;
    $page++;
}

if ($found) {
    echo "\n=== TR639133 DETAYI ===\n";
    echo json_encode($found, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "\nTR639133 Porego'da bulunamadı.\n";
}

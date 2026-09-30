<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TÜM BENZERSİZ BRAND DEĞERLERİ ===\n";
$brands = DB::table('products')
    ->select('brand', DB::raw('COUNT(*) as cnt'))
    ->whereNotNull('brand')
    ->where('brand', '!=', '')
    ->groupBy('brand')
    ->orderBy('brand')
    ->get();

foreach ($brands as $b) {
    echo "'{$b->brand}' => {$b->cnt} ürün\n";
}

echo "\n=== BENZER/ÇİFT BRAND TESPİTİ ===\n";
$brandNames = $brands->pluck('brand')->toArray();
$checked = [];

foreach ($brandNames as $i => $a) {
    foreach ($brandNames as $j => $b) {
        if ($i >= $j) continue;
        $key = "$i-$j";
        
        $nameA = mb_strtolower(trim($a));
        $nameB = mb_strtolower(trim($b));
        
        // Exact case-insensitive match
        if ($nameA === $nameB) {
            echo "EXACT DUPLICATE: '$a' == '$b'\n";
            continue;
        }
        
        // Trim/whitespace differences
        if (preg_replace('/\s+/', '', $nameA) === preg_replace('/\s+/', '', $nameB)) {
            echo "WHITESPACE DIFF: '$a' <-> '$b'\n";
            continue;
        }
        
        // One contains the other
        if (mb_strlen($nameA) > 2 && mb_strlen($nameB) > 2) {
            if (str_contains($nameA, $nameB) || str_contains($nameB, $nameA)) {
                echo "CONTAINS: '$a' <-> '$b'\n";
                continue;
            }
        }
        
        // Levenshtein similarity
        if (strlen($nameA) > 2 && strlen($nameB) > 2) {
            $lev = levenshtein($nameA, $nameB);
            $maxLen = max(strlen($nameA), strlen($nameB));
            if ($lev <= 2 && $maxLen > 3) {
                echo "SIMILAR (lev=$lev): '$a' <-> '$b'\n";
            }
        }
    }
}

echo "\n=== DETAYLI ÜRÜN LİSTESİ (BRAND + ID + İSİM) ===\n";
$products = DB::table('products')
    ->select('id', 'name', 'brand')
    ->whereNotNull('brand')
    ->where('brand', '!=', '')
    ->orderBy('brand')
    ->orderBy('name')
    ->get();

foreach ($products as $p) {
    echo "[{$p->id}] {$p->brand} => {$p->name}\n";
}

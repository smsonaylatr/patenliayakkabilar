<?php
// Find the axios instance 'z' in index-C2NzNLce.js
// It should have a baseURL configuration
$c = file_get_contents('index-C2NzNLce.js');

// Search for baseURL or base_url
echo "=== Searching for baseURL ===\n";
$pos = 0;
while (($pos = strpos($c, 'baseURL', $pos)) !== false) {
    echo "At $pos: " . substr($c, max(0, $pos - 100), 300) . "\n---\n";
    $pos += 7;
    if ($pos > 500000) break; // Safety
}

echo "\n=== Searching for axios.create ===\n";
preg_match_all('#axios\.create\s*\(\s*\{[^}]{0,500}\}#', $c, $m, PREG_OFFSET_CAPTURE);
foreach ($m[0] as $match) {
    echo "At {$match[1]}: {$match[0]}\n---\n";
}

echo "\n=== Searching for depokargo or back.porego ===\n";
$terms = ['depokargo', 'back.porego.com', '/api/v1'];
foreach ($terms as $t) {
    $pos = 0;
    $found = 0;
    while (($pos = strpos($c, $t, $pos)) !== false && $found < 3) {
        echo "'$t' at $pos: " . substr($c, max(0, $pos - 80), 250) . "\n---\n";
        $pos += strlen($t);
        $found++;
    }
}

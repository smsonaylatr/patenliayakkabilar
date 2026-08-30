<?php
// Find where labelHtml is populated - trace it from ShipmentsPage
$files = ['ShipmentsPage-DMRBgOg5.js', 'porego_chunk_ShipmentLabelDialog.js'];

foreach ($files as $f) {
    $c = file_get_contents($f);
    
    // Find where labelHtml is set/fetched
    echo "=== Searching '$f' for labelHtml assignment ===\n";
    
    // Look for setLabelHtml or labelHtml: or labelHtml =
    $patterns = ['setLabelHtml', 'labelHtml:', 'labelHtml=', 'labelHtml =', 'label_html', 'label-html'];
    foreach ($patterns as $p) {
        $pos = 0;
        while (($pos = strpos($c, $p, $pos)) !== false) {
            echo "  '$p' at offset $pos:\n";
            echo "  " . substr($c, max(0, $pos - 200), 500) . "\n  ---\n";
            $pos += strlen($p);
        }
    }
    
    // Look for the API call that fetches label data
    echo "\n=== Searching '$f' for /shipments/ or /labels/ API calls ===\n";
    preg_match_all('#[`"\'][^`"\']*(?:/shipments/|/labels/|/orders/)[^`"\']*label[^`"\']*[`"\']#i', $c, $m);
    foreach ($m[0] as $match) {
        echo "  Endpoint: $match\n";
    }
    
    // Find viewLabel or fetchLabel or getLabel functions
    echo "\n=== Searching '$f' for viewLabel/fetchLabel/getLabel ===\n";
    $patterns2 = ['viewLabel', 'fetchLabel', 'getLabel', 'printLabel', 'showLabel', 'openLabel', 'labelData'];
    foreach ($patterns2 as $p) {
        $pos = 0;
        while (($pos = strpos($c, $p, $pos)) !== false) {
            echo "  '$p' at offset $pos:\n";
            echo "  " . substr($c, max(0, $pos - 150), 400) . "\n  ---\n";
            $pos += strlen($p);
        }
    }
}

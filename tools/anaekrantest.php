<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';
require_once dirname(__DIR__) . '/lib/katalogdepo.php';
require_once dirname(__DIR__) . '/lib/sablonyukle.php';
require_once dirname(__DIR__) . '/lib/sabloncalistir.php';
require_once dirname(__DIR__) . '/lib/theme/anaekran.php';

$pdo = ded_pdo();
if (!$pdo || !ded_db_ready()) {
    fwrite(STDERR, "DB not ready\n");
    exit(1);
}

$html = ded_template_render('index.php');
$pat = '#<reveal-items selector="\.product-list > \*">\s*<product-list class="product-list"[^>]*>.*?</product-list>\s*</reveal-items>#s';
echo 'matches before: ' . preg_match_all($pat, $html) . "\n";

$out = ded_patch_index_apply($pdo, $html);
echo 'matches after: ' . preg_match_all($pat, $out) . "\n";

if (preg_match('#handle="dhhdhf"#', $out)) {
    echo "NEW product dhhdhf found in output\n";
} else {
    echo "NEW product dhhdhf NOT in output\n";
}

foreach (ded_collections_all($pdo) as $i => $c) {
    $d = ded_collection_by_id($pdo, (string) $c['id']);
    $n = count($d['products'] ?? []);
    echo "col[$i] {$c['id']} => $n products\n";
}

$slugs = $pdo->query('SELECT slug FROM ded_products ORDER BY id DESC')->fetchAll(PDO::FETCH_COLUMN);
echo 'all slugs: ' . implode(', ', $slugs) . "\n";

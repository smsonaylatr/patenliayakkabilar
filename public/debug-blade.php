<?php
/**
 * Show the ACTUAL content of show.blade.php on the server
 */
$base = dirname(__FILE__) . '/../';
$file = $base . 'resources/views/products/show.blade.php';

echo "<h2>Server File Content</h2>";
echo "<p>File: $file</p>";
echo "<p>Exists: " . (file_exists($file) ? 'YES' : 'NO') . "</p>";
echo "<p>Size: " . filesize($file) . " bytes</p>";
echo "<p>MD5: " . md5_file($file) . "</p>";
echo "<hr>";
echo "<h3>Content (htmlspecialchars):</h3>";
echo "<pre>" . htmlspecialchars(file_get_contents($file)) . "</pre>";

echo "<hr><h3>All Blade files with potential issues:</h3><pre>";
// Check all blade files for unclosed @if
$bladeDir = $base . 'resources/views/';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($bladeDir));
foreach ($iterator as $f) {
    if ($f->getExtension() !== 'php') continue;
    $content = file_get_contents($f->getPathname());
    $ifs = substr_count($content, '@if(') + substr_count($content, '@if (');
    $endifs = substr_count($content, '@endif');
    $foreachs = substr_count($content, '@foreach');
    $endforeachs = substr_count($content, '@endforeach');
    $fors = substr_count($content, '@for(') + substr_count($content, '@for ');
    $endfors = substr_count($content, '@endfor');
    
    $relPath = str_replace($bladeDir, '', $f->getPathname());
    
    if ($ifs !== $endifs || $foreachs !== $endforeachs) {
        echo "❌ $relPath: @if=$ifs @endif=$endifs | @foreach=$foreachs @endforeach=$endforeachs\n";
    }
}
echo "Done checking.\n";
echo "</pre>";

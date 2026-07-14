<?php
$file = 'resources/views/filament/pages/influencer-marketing.blade.php';
$c = file_get_contents($file);
$c = preg_replace('/(<x-heroicon-[a-z0-9\-]+\s+class="[^"]+")(\s*\/>)/i', '$1 style="width: 2rem; height: 2rem; flex-shrink: 0;"$2', $c);
file_put_contents($file, $c);
echo "Fixed SVG sizes inline.\n";

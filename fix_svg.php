<?php
$file = 'resources/views/filament/pages/influencer-marketing.blade.php';
$c = file_get_contents($file);
$c = preg_replace("/@svg\('([^']+)',\s*'([^']+)'\)/", "@svg('$1', ['class' => '$2', 'style' => 'width: 2rem; height: 2rem; flex-shrink: 0;'])", $c);
file_put_contents($file, $c);
echo "OK";

<?php
$file = 'resources/views/filament/pages/influencer-marketing.blade.php';
$content = file_get_contents($file);
$content = preg_replace('/@svg\(\s*\'([^\']+)\'\s*,\s*\'([^\']+)\'\s*\)/', '<x-$1 class="$2" />', $content);
file_put_contents($file, $content);
echo "Fixed SVGs.\n";

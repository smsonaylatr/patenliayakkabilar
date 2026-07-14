<?php
$file = 'resources/views/filament/pages/influencer-marketing.blade.php';
$c = file_get_contents($file);
// Replace <x-heroicon-s-rocket-launch class="..."> with <x-filament::icon icon="heroicon-s-rocket-launch" class="..." />
$c = preg_replace('/<x-([a-zA-Z0-9\-]+)\s+class="([^"]+)"(?:\s+style="[^"]+")?\s*\/>/i', '<x-filament::icon icon="$1" class="$2" style="width: 2rem; height: 2rem; flex-shrink: 0;" />', $c);
file_put_contents($file, $c);
echo "Fixed to use Filament Icon component.\n";

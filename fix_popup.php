<?php
$f = 'app/Filament/Pages/PopupSettings.php';
$c = file_get_contents($f);
$c = str_replace('public function form(Schema $schema)', 'public function schema(Schema $schema)', $c);
$c = str_replace('$schema->schema(', '$schema->components(', $c);
file_put_contents($f,$c);
echo "Fixed $f\n";

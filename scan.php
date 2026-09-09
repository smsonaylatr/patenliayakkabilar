<?php
$dir = __DIR__ . '/app';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$regexes = [
    '/Filament\\\\Forms\\\\Set/' => 'Filament\Schemas\Components\Utilities\Set',
    '/Filament\\\\Forms\\\\Get/' => 'Filament\Schemas\Components\Utilities\Get',
    '/Filament\\\\Forms\\\\Form/' => 'Filament\Schemas\Schema',
    '/Filament\\\\Forms\\\\Components\\\\Section/' => 'Filament\Schemas\Components\Section',
    '/Filament\\\\Forms\\\\Components\\\\Tabs/' => 'Filament\Schemas\Components\Tabs',
    '/Filament\\\\Forms\\\\Components\\\\Actions/' => 'Filament\Schemas\Components\Actions',
    '/Filament\\\\Forms\\\\Components\\\\Grid/' => 'Filament\Schemas\Components\Grid',
    '/Filament\\\\Forms\\\\Components\\\\Fieldset/' => 'Filament\Schemas\Components\Fieldset',
    '/Filament\\\\Forms\\\\Components\\\\Wizard/' => 'Filament\Schemas\Components\Wizard',
    '/Filament\\\\Forms\\\\Components\\\\Split/' => 'Filament\Schemas\Components\Split',
    '/->form\(\$form\)/' => '->schema($schema)', // this might be risky, need manual check
    '/\$form->schema/' => '$schema->components',
    '/function form\(Form/' => 'function schema(Schema',
    '/->form\(\[/' => '->schema([', // Need to be careful here
];

$results = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $matches = [];
        foreach ($regexes as $regex => $replacement) {
            if (preg_match($regex, $content)) {
                $matches[] = $regex;
            }
        }
        if (!empty($matches)) {
            $results[$file->getRealPath()] = $matches;
        }
    }
}

print_r($results);

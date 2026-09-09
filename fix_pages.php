<?php
$dir = __DIR__ . '/app/Filament/Pages';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $original = $content;

        // Replace public function form(Schema $form): Schema
        $content = preg_replace('/public function form\((\\\\?Filament\\\\Schemas\\\\)?Schema\s+\$form\):\s*(\\\\?Filament\\\\Schemas\\\\)?Schema/', 'public function schema($1Schema $schema): $2Schema', $content);

        // Within schema method, change $form->schema( to $schema->components(
        // And also $form-> to $schema-> for chainability
        // This is a bit tricky, but since it's only in Settings pages, we can just replace $form->schema with $schema->components
        // and also just replace $form with $schema if they occur after public function schema(
        
        // Actually, just replacing `$form->schema` with `$schema->components` and `$form` with `$schema` in these classes where `function schema` exists should be safe enough if we do it carefully.
        if ($content !== $original) {
            $content = str_replace('$form->schema(', '$schema->components(', $content);
            $content = str_replace('return $form', 'return $schema', $content);
            
            file_put_contents($file->getRealPath(), $content);
            echo "Updated: " . $file->getRealPath() . "\n";
        }
    }
}

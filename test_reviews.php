<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach(\App\Models\Review::orderBy('created_at', 'desc')->take(12)->get() as $r) {
    echo $r->id . ' - ' . $r->name . ' - ' . $r->created_at . PHP_EOL;
}

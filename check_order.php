<?php 
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$o = App\Models\Order::where('order_number', 'TR639133')->first();
if ($o) {
    echo json_encode($o->toArray(), JSON_PRETTY_PRINT);
} else {
    echo "Not found";
}

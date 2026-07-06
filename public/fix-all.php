<?php
$basePath = dirname(__FILE__) . '/../';
echo "<h2>Plesk Server Fixer</h2><pre>";
if (function_exists('opcache_reset')) { opcache_reset(); echo "✅ OPcache cleared!\n"; }
$sl = dirname(__FILE__) . '/storage';
$tg = dirname(__FILE__) . '/../storage/app/public';
if (!file_exists($sl)) { @symlink($tg, $sl); }
@mkdir($tg . '/livewire-tmp', 0775, true);
@mkdir(dirname(__FILE__) . '/../storage/app/livewire-tmp', 0775, true);
require_once $basePath . 'vendor/autoload.php';
$app = require_once $basePath . 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
\Illuminate\Support\Facades\Artisan::call('optimize:clear');
echo "✅ Laravel cache cleared!\n</pre><h2 style='color:green'>All done! Please try uploading now.</h2>";

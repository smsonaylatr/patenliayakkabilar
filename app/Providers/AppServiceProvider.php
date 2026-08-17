<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\SchemaService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        // Mlevent\Fatura kütüphanesi için yedek autoloader
        if (!class_exists(\Mlevent\Fatura\Gib::class)) {
            spl_autoload_register(function ($class) {
                if (str_starts_with($class, 'Mlevent\\Fatura\\')) {
                    $relative = str_replace('Mlevent\\Fatura\\', '', $class);
                    $file = base_path('vendor/mlevent/fatura/src/' . str_replace('\\', '/', $relative) . '.php');
                    if (file_exists($file)) {
                        require_once $file;
                    }
                }
            });

            $helpersFile = base_path('vendor/mlevent/fatura/src/Utils/Helpers.php');
            if (file_exists($helpersFile)) {
                require_once $helpersFile;
            }
        }

        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
    }
}

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
        if (!class_exists(\Mlevent\Fatura\Models\InvoiceModel::class)) {
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
        \App\Models\ContactMessage::observe(\App\Observers\ContactMessageObserver::class);

        // Mail event subscriber — gönderilen tüm e-postaları loglar
        \Illuminate\Support\Facades\Event::subscribe(\App\Listeners\MailEventSubscriber::class);

        // Ayakkabı modellerinin açıklama ve SEO verilerini tek seferlik otomatik senkronize et
        if (!$this->app->runningInConsole()) {
            try {
                if (!\Illuminate\Support\Facades\Cache::get('shoe_seo_descriptions_v1_applied')) {
                    $applied = \App\Models\Setting::where('key', 'shoe_seo_descriptions_v1_applied')->value('value');
                    if ($applied !== '1') {
                        $products = \App\Models\Product::with(['categories', 'features', 'variants'])->get();
                        foreach ($products as $product) {
                            if ($product->applyShoeDescriptionAndSeo(true, true)) {
                                $product->saveQuietly();
                            }
                        }
                        \App\Models\Product::clearProductCaches();
                        \App\Models\Setting::updateOrCreate(
                            ['key' => 'shoe_seo_descriptions_v1_applied'],
                            ['value' => '1']
                        );
                    }
                    \Illuminate\Support\Facades\Cache::forever('shoe_seo_descriptions_v1_applied', true);
                }
            } catch (\Throwable) {
                // Veritabanı henüz hazır değilse sessizce geç
            }
        }
    }
}

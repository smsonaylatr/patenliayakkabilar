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

        // Livewire global payload güvenlik filtresi (Bot/Scanner TypeError ve Locked property manipülasyon koruması)
        $this->registerLivewirePayloadSanitizer();

        // Ürün başlıklarından ve slug'lardan "Spor" kelimesini temizle + Search Console / Sitemap / SEO senkronize et
        if (!$this->app->runningInConsole()) {
            try {
                if (!\Illuminate\Support\Facades\Cache::get('shoe_remove_spor_and_gsc_v4_applied')) {
                    $applied = \App\Models\Setting::where('key', 'shoe_remove_spor_and_gsc_v4_applied')->value('value');
                    if ($applied !== '1') {
                        app(\App\Services\GoogleSearchConsoleService::class)->syncAll(false);
                        \App\Models\Setting::updateOrCreate(
                            ['key' => 'shoe_remove_spor_and_gsc_v4_applied'],
                            ['value' => '1']
                        );
                    }
                    \Illuminate\Support\Facades\Cache::forever('shoe_remove_spor_and_gsc_v4_applied', true);
                }
            } catch (\Throwable) {
                // Veritabanı henüz hazır değilse sessizce geç
            }
        }
    }

    /**
     * Livewire update isteklerinde botların veya güvenlik tarayıcılarının
     * skaler alanlara (int, string, float, bool) dizi/nesne göndererek
     * TypeError fırlatmasını veya #[Locked] alanları manipüle etmesini
     * hydration öncesinde engeller ve temizler.
     */
    protected function registerLivewirePayloadSanitizer(): void
    {
        if (!function_exists('\\Livewire\\on')) {
            return;
        }

        \Livewire\on('request', function ($payload) {
            return function ($payload) {
                if (!is_array($payload)) {
                    return $payload;
                }

                foreach ($payload as $idx => $componentPayload) {
                    if (!is_array($componentPayload) || empty($componentPayload['updates']) || !is_array($componentPayload['updates'])) {
                        continue;
                    }

                    $snapshotJson = $componentPayload['snapshot'] ?? null;
                    if (!is_string($snapshotJson)) {
                        continue;
                    }

                    $snapshot = json_decode($snapshotJson, true);
                    $componentName = $snapshot['memo']['name'] ?? null;
                    if (!is_string($componentName)) {
                        continue;
                    }

                    try {
                        $componentClass = app('livewire.finder')->resolveClassComponentClassName($componentName);
                        if (!$componentClass || !class_exists($componentClass)) {
                            continue;
                        }
                        $reflection = new \ReflectionClass($componentClass);
                    } catch (\Throwable) {
                        continue;
                    }

                    $cleanUpdates = [];

                    foreach ($componentPayload['updates'] as $path => $value) {
                        if (!is_string($path)) {
                            continue;
                        }

                        $segments = explode('.', $path);
                        $rootProp = $segments[0];

                        if (!$reflection->hasProperty($rootProp)) {
                            $cleanUpdates[$path] = $value;
                            continue;
                        }

                        $prop = $reflection->getProperty($rootProp);
                        if (!$prop->isPublic() || $prop->isStatic()) {
                            continue;
                        }

                        // #[Locked] attribute kontrolü — istemciden gelen güncellemeyi sessizce düşür
                        $isLocked = false;
                        foreach ($prop->getAttributes() as $attr) {
                            if (is_a($attr->getName(), \Livewire\Features\SupportLockedProperties\BaseLocked::class, true)) {
                                $isLocked = true;
                                break;
                            }
                        }
                        if ($isLocked) {
                            continue;
                        }

                        $type = $prop->getType();
                        if ($type instanceof \ReflectionNamedType) {
                            $typeName = $type->getName();
                            $ScalarTypes = ['int', 'float', 'string', 'bool'];

                            if (in_array($typeName, $ScalarTypes, true)) {
                                // Skaler bir alana nokta notasyonuyla (örn: cartCount.foo) erişilmeye çalışılıyorsa engelle
                                if (count($segments) > 1) {
                                    continue;
                                }

                                // Skaler bir alana array veya object gönderilmişse uygun skaler tipe dönüştür veya düşür
                                if (is_array($value) || is_object($value)) {
                                    $first = is_array($value) ? reset($value) : null;
                                    $value = match ($typeName) {
                                        'string' => (is_string($first) || is_numeric($first)) ? trim((string) $first) : '',
                                        'int' => is_numeric($first) ? (int) $first : 0,
                                        'float' => is_numeric($first) ? (float) $first : 0.0,
                                        'bool' => (bool) $first,
                                    };
                                } elseif ($value !== null && $value !== '') {
                                    $value = match ($typeName) {
                                        'int' => is_numeric($value) ? (int) $value : 0,
                                        'float' => is_numeric($value) ? (float) $value : 0.0,
                                        'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                                        'string' => (string) $value,
                                    };
                                }
                            }
                        }

                        $cleanUpdates[$path] = $value;
                    }

                    $payload[$idx]['updates'] = $cleanUpdates;
                }

                return $payload;
            };
        });
    }
}

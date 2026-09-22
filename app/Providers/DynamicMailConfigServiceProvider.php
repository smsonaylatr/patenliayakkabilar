<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class DynamicMailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Migration çalışmadan önce veya cache config durumunda DB'ye erişmeye çalışmayı önle
        if (!app()->runningInConsole() || app()->runningUnitTests()) {
            $this->applyDynamicMailConfig();
        }
        
        // Console'da (örneğin queue worker) yapılandırmanın uygulanabilmesi için booted hook'u
        $this->app->booted(function () {
            $this->applyDynamicMailConfig();
        });
    }
    
    /**
     * Veritabanından (veya Cache'den) mail ayarlarını alıp config'i override eder
     */
    private function applyDynamicMailConfig(): void
    {
        try {
            // Eğer Setting modeli henüz oluşmamışsa atla (Kurulum/migration aşaması)
            if (!class_exists(\App\Models\Setting::class)) {
                return;
            }

            $settings = Cache::remember('mail_smtp_settings', 300, function () {
                return \App\Models\Setting::whereIn('key', [
                    'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
                    'smtp_from_address', 'smtp_from_name', 'smtp_encryption',
                ])->pluck('value', 'key')->toArray();
            });
            
            // Sadece veritabanında host ayarlanmışsa override işlemine başla
            if (!empty($settings['smtp_host'])) {
                config([
                    'mail.mailers.smtp.host' => $settings['smtp_host'],
                    'mail.mailers.smtp.port' => (int)($settings['smtp_port'] ?? 465),
                    'mail.mailers.smtp.username' => $settings['smtp_username'] ?? null,
                    'mail.mailers.smtp.password' => $settings['smtp_password'] ?? null,
                ]);
                
                // Encryption/Scheme ayarı
                $port = (int)($settings['smtp_port'] ?? 465);
                $encryption = $settings['smtp_encryption'] ?? null;
                
                if ($port === 465 || $encryption === 'ssl') {
                    config(['mail.mailers.smtp.scheme' => 'smtps']);
                } elseif ($port === 587 || $encryption === 'tls') {
                    config(['mail.mailers.smtp.scheme' => 'smtp']);
                }
            }
            
            // Gönderen e-posta adresi ayarı
            if (!empty($settings['smtp_from_address'])) {
                config([
                    'mail.from.address' => $settings['smtp_from_address'],
                    'mail.from.name' => $settings['smtp_from_name'] ?? config('mail.from.name'),
                ]);
            }
        } catch (\Throwable $e) {
            // DB henüz hazır değilse veya hata olursa sessizce devam et
            // config/mail.php içindeki çevresel değişken ayarları geçerli kalır
        }
    }
}

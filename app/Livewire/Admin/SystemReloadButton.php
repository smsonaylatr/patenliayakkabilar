<?php

namespace App\Livewire\Admin;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class SystemReloadButton extends Component
{
    public function reload(): void
    {
        if (! filament()->auth()->check()) {
            return;
        }

        try {
            // 1. php artisan optimize:clear
            Artisan::call('optimize:clear');

            // 2. OPcache temizleme (PHP-FPM ortamı için)
            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            // 3. Octane Reload (RoadRunner)
            $octaneSuccess = false;

            if (class_exists(\Laravel\Octane\Commands\ReloadCommand::class)) {
                $kernel = app(\Illuminate\Contracts\Console\Kernel::class);
                $kernel->registerCommand(app(\Laravel\Octane\Commands\ReloadCommand::class));

                try {
                    $exitCode = Artisan::call('octane:reload');
                    $octaneSuccess = ($exitCode === 0);
                } catch (\Throwable) {
                    $octaneSuccess = false;
                }
            }

            // POSIX kısıtlaması olan ortamlarda doğrudan RPC reset denemesi
            if (! $octaneSuccess && app()->bound(\Laravel\Octane\RoadRunner\ServerProcessInspector::class)) {
                try {
                    $inspector = app(\Laravel\Octane\RoadRunner\ServerProcessInspector::class);
                    $inspector->reloadServer();
                    $octaneSuccess = true;
                } catch (\Throwable) {
                    $octaneSuccess = false;
                }
            }

            $message = $octaneSuccess
                ? 'Önbellek temizlendi ve Octane worker\'ları başarıyla yeniden başlatıldı.'
                : 'Uygulama önbelleği (yapılandırma, rotalar, görünümler) ve OPcache başarıyla temizlendi.';

            Notification::make()
                ->title('Sistem Yenilendi')
                ->body($message)
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Yenileme Başarısız')
                ->body('Hata: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.admin.system-reload-button');
    }
}

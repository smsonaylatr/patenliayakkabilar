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

            // 2. Ensure Octane commands are registered in web context
            if (class_exists(\Laravel\Octane\Commands\ReloadCommand::class)) {
                $kernel = app(\Illuminate\Contracts\Console\Kernel::class);
                $kernel->registerCommand(app(\Laravel\Octane\Commands\ReloadCommand::class));
            }

            $octaneSuccess = false;
            $octaneOutput = '';

            try {
                $exitCode = Artisan::call('octane:reload');
                $octaneOutput = trim(Artisan::output());
                $octaneSuccess = ($exitCode === 0);
            } catch (\Symfony\Component\Console\Exception\CommandNotFoundException) {
                // Direct fallback to RoadRunner inspector if command registration was bypassed
                if (app()->bound(\Laravel\Octane\RoadRunner\ServerProcessInspector::class)) {
                    $inspector = app(\Laravel\Octane\RoadRunner\ServerProcessInspector::class);
                    if ($inspector->serverIsRunning()) {
                        $inspector->reloadServer();
                        $octaneSuccess = true;
                        $octaneOutput = 'Workers reloaded.';
                    } else {
                        $octaneOutput = 'Octane server is not running.';
                    }
                }
            }

            $body = 'Önbellek başarıyla temizlendi (optimize:clear).';
            if ($octaneSuccess) {
                $body .= ' Octane worker\'ları yeniden başlatıldı.';
            } elseif (! empty($octaneOutput)) {
                // Sadece bilgi amaçlı ekle
                $body .= ' (Octane: ' . strip_tags($octaneOutput) . ')';
            }

            Notification::make()
                ->title('Sistem Yenilendi')
                ->body($body)
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

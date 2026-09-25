<?php

namespace App\Livewire\Admin;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class SystemReloadButton extends Component
{
    public function reload(): void
    {
        try {
            // 1. php artisan optimize:clear
            Artisan::call('optimize:clear');

            // 2. php artisan octane:reload
            $octaneExitCode = Artisan::call('octane:reload');
            $octaneOutput = trim(Artisan::output());

            $statusText = 'Önbellek başarıyla temizlendi.';
            if ($octaneExitCode === 0) {
                $statusText .= ' Octane worker\'ları yeniden başlatıldı.';
            } elseif (! empty($octaneOutput)) {
                $statusText .= " ({$octaneOutput})";
            }

            Notification::make()
                ->title('Sistem Yenilendi')
                ->body($statusText)
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

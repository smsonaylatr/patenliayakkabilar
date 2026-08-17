<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncPoregoStock')
                ->label('Porego Stok Senkronizasyonu')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Porego / Paketfy Stok Senkronizasyonu')
                ->modalDescription('Sitedeki tüm aktif ürün ve varyantların güncel fiyat, stok ve SKU bilgilerini Porego stok sistemine aktarır.')
                ->modalSubmitActionLabel('Şimdi Senkronize Et')
                ->action(function (): void {
                    $result = app(\App\Services\PoregoApiService::class)->syncProducts();
                    if ($result['success']) {
                        \Filament\Notifications\Notification::make()
                            ->title('Porego Stok Senkronizasyonu Tamamlandı')
                            ->body($result['message'])
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Porego Senkronizasyon Uyarısı')
                            ->body($result['message'])
                            ->warning()
                            ->send();
                    }
                }),
            CreateAction::make()->label('Yeni Ürün'),
        ];
    }
}

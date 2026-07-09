<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetSort')
                ->label('Sıralamayı Sıfırla')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Sıralamayı Sıfırla')
                ->modalDescription('Tüm ürünlerin anasayfa sıralaması sıfırlanacak. Devam etmek istiyor musunuz?')
                ->modalSubmitActionLabel('Evet, Sıfırla')
                ->action(function () {
                    Product::query()->update(['homepage_sort' => 0]);
                    Cache::forget('home_product_grid_v2');

                    \Filament\Notifications\Notification::make()
                        ->title('Sıralama sıfırlandı')
                        ->body('Tüm ürünlerin anasayfa sıralaması sıfırlandı.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Console\Commands\GenerateReviewsForEmptyProducts;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_reviews')
                ->label('✨ Yorumsuz Ürünlere Yorum Üret (5 Adet)')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Yorumsuz Ürünlere Otomatik Yorum Ekle')
                ->modalDescription('Henüz hiç yorumu bulunmayan tüm ürünlere, ürün özelliklerine uygun gerçekçi ve övgü dolu 5 adet müşteri yorumu eklenecektir. Onaylıyor musunuz?')
                ->action(function () {
                    $products = Product::doesntHave('reviews')->get();
                    if ($products->isEmpty()) {
                        Notification::make()
                            ->title('Tüm ürünlerin zaten yorumu var.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $totalAdded = 0;
                    foreach ($products as $product) {
                        $totalAdded += GenerateReviewsForEmptyProducts::generateReviewsForProduct($product, 5);
                    }

                    Notification::make()
                        ->title("İşlem Başarılı: {$totalAdded} adet yorum eklendi!")
                        ->body("{$products->count()} adet yorumsuz ürün için onaylı değerlendirmeler oluşturuldu.")
                        ->success()
                        ->send();
                }),
            CreateAction::make()->label('Yeni Değerlendirme'),
        ];
    }
}

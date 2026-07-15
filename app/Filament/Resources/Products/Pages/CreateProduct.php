<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('create')
                ->label('Oluştur')
                ->action('create')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // feature_keys form'dan çıkar (ayrı tabloya kaydedilecek)
        unset($data['feature_keys']);
        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            $featureKeys = $this->data['feature_keys'] ?? [];

            if (!empty($featureKeys)) {
                $order = 0;
                foreach ($featureKeys as $key) {
                    $this->record->features()->create([
                        'feature_key' => $key,
                        'sort_order'  => $order++,
                    ]);
                }
            } else {
                $this->record->autoFillFeatures();
            }
        } catch (\Exception $e) {
            // product_features tablosu henüz oluşturulmamışsa sessizce geç
        }
    }
}

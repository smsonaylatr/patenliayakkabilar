<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Mevcut özellikleri form'a yükle
        $data['feature_keys'] = $this->record->features()->pluck('feature_key')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['feature_keys']);
        return $data;
    }

    protected function afterSave(): void
    {
        $featureKeys = $this->data['feature_keys'] ?? [];

        // Mevcut özellikleri temizle ve yeniden kaydet
        $this->record->features()->delete();

        $order = 0;
        foreach ($featureKeys as $key) {
            $this->record->features()->create([
                'feature_key' => $key,
                'sort_order'  => $order++,
            ]);
        }
    }
}

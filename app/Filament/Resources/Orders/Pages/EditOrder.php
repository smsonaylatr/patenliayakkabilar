<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Form kaydedilmeden önce tutarları kalemlerden yeniden hesapla.
     * Bu sayede Observer'ın hesapladığı değerler korunur ve
     * shipping_price/discount_total değişiklikleri grand_total'a yansır.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $order = $this->record;

        // Kalemlerden subtotal hesapla
        $subtotal = $order->items()
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0;

        $data['subtotal'] = round((float) $subtotal, 2);
        $data['grand_total'] = round(
            $data['subtotal'] + (float) ($data['shipping_price'] ?? 0) - (float) ($data['discount_total'] ?? 0),
            2
        );

        return $data;
    }
}

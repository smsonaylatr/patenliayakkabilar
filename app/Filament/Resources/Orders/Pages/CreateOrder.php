<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['traffic_source'] = $data['traffic_source'] ?? 'Admin Paneli';
        $data['device_type'] = $data['device_type'] ?? 'Masaüstü';

        return $data;
    }
}

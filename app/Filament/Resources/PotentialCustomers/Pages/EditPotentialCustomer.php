<?php

namespace App\Filament\Resources\PotentialCustomers\Pages;

use App\Filament\Resources\PotentialCustomers\PotentialCustomerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPotentialCustomer extends EditRecord
{
    protected static string $resource = PotentialCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

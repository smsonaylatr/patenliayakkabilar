<?php

namespace App\Filament\Resources\PotentialCustomers\Pages;

use App\Filament\Resources\PotentialCustomers\PotentialCustomerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPotentialCustomers extends ListRecords
{
    protected static string $resource = PotentialCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Backlinks\Pages;

use App\Filament\Resources\Backlinks\BacklinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBacklink extends EditRecord
{
    protected static string $resource = BacklinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

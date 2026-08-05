<?php

namespace App\Filament\Resources\GiveawayEntries\Pages;

use App\Filament\Resources\GiveawayEntries\GiveawayEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGiveawayEntry extends EditRecord
{
    protected static string $resource = GiveawayEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

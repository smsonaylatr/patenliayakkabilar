<?php

namespace App\Filament\Resources\GiveawayEntries\Pages;

use App\Filament\Resources\GiveawayEntries\GiveawayEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGiveawayEntries extends ListRecords
{
    protected static string $resource = GiveawayEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni Katılım Ekle'),
        ];
    }
}

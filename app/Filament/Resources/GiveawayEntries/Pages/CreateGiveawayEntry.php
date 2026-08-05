<?php

namespace App\Filament\Resources\GiveawayEntries\Pages;

use App\Filament\Resources\GiveawayEntries\GiveawayEntryResource;
use App\Models\GiveawayEntry;
use Filament\Resources\Pages\CreateRecord;

class CreateGiveawayEntry extends CreateRecord
{
    protected static string $resource = GiveawayEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['ticket_code'])) {
            $data['ticket_code'] = GiveawayEntry::generateTicketCode();
        }
        return $data;
    }
}

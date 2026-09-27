<?php

namespace App\Filament\Resources\CannedVoiceMessages\Pages;

use App\Filament\Resources\CannedVoiceMessages\CannedVoiceMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCannedVoiceMessages extends ListRecords
{
    protected static string $resource = CannedVoiceMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('🎙️ Yeni Sesli Anons Ekle'),
        ];
    }
}

<?php

namespace App\Filament\Resources\CannedVoiceMessages\Pages;

use App\Filament\Resources\CannedVoiceMessages\CannedVoiceMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCannedVoiceMessage extends EditRecord
{
    protected static string $resource = CannedVoiceMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Sil'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

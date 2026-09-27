<?php

namespace App\Filament\Resources\CannedVoiceMessages\Pages;

use App\Filament\Resources\CannedVoiceMessages\CannedVoiceMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCannedVoiceMessage extends CreateRecord
{
    protected static string $resource = CannedVoiceMessageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\Backlinks\Pages;

use App\Filament\Resources\Backlinks\BacklinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBacklinks extends ListRecords
{
    protected static string $resource = BacklinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni Backlink Kaynağı Ekle'),
        ];
    }
}

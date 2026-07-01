<?php

namespace App\Filament\Resources\Segments\Pages;

use App\Filament\Resources\Segments\SegmentResource;
use Filament\Resources\Pages\EditRecord;

class EditSegment extends EditRecord
{
    protected static string $resource = SegmentResource::class;
    protected static ?string $title = 'Segment Düzenle';
}

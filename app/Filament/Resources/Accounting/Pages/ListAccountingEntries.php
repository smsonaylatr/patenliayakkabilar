<?php

namespace App\Filament\Resources\Accounting\Pages;

use App\Filament\Resources\Accounting\AccountingEntryResource;
use App\Models\AccountingEntry;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListAccountingEntries extends ListRecords
{
    protected static string $resource = AccountingEntryResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AccountingSummaryWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

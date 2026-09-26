<?php

namespace App\Filament\Resources\StockNotifications\Pages;

use App\Filament\Resources\StockNotifications\StockNotificationResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class ListStockNotifications extends ListRecords
{
    protected static string $resource = StockNotificationResource::class;

    public function mount(): void
    {
        if (!Schema::hasTable('stock_notifications')) {
            Artisan::call('migrate', ['--force' => true]);
        }
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

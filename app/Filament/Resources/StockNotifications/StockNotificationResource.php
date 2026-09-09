<?php

namespace App\Filament\Resources\StockNotifications;

use App\Filament\Resources\StockNotifications\Pages\ListStockNotifications;
use App\Filament\Resources\StockNotifications\Schemas\StockNotificationForm;
use App\Filament\Resources\StockNotifications\Tables\StockNotificationsTable;
use App\Models\StockNotification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockNotificationResource extends Resource
{
    protected static ?string $model = StockNotification::class;
    protected static ?string $modelLabel = 'Stok Bildirimi';
    protected static ?string $pluralModelLabel = 'Gelince Haber Ver Talepleri';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    public static function getNavigationGroup(): ?string
    {
        return 'E-Ticaret';
    }

    public static function getNavigationLabel(): string
    {
        return 'Gelince Haber Ver';
    }

    public static function form(Schema $schema): Schema
    {
        return StockNotificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockNotificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockNotifications::route('/'),
        ];
    }
}

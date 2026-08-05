<?php

namespace App\Filament\Resources\GiveawayEntries;

use App\Filament\Resources\GiveawayEntries\Pages\CreateGiveawayEntry;
use App\Filament\Resources\GiveawayEntries\Pages\EditGiveawayEntry;
use App\Filament\Resources\GiveawayEntries\Pages\ListGiveawayEntries;
use App\Filament\Resources\GiveawayEntries\Schemas\GiveawayEntryForm;
use App\Filament\Resources\GiveawayEntries\Tables\GiveawayEntriesTable;
use App\Models\GiveawayEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GiveawayEntryResource extends Resource
{
    protected static ?string $model = GiveawayEntry::class;
    protected static ?string $modelLabel = 'Çekiliş Katılımı';
    protected static ?string $pluralModelLabel = 'Instagram Çekiliş Katılımları';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Pazarlama & Büyüme';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Instagram Çekilişi';
    }

    protected static ?string $recordTitleAttribute = 'ticket_code';

    public static function form(Schema $schema): Schema
    {
        return GiveawayEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GiveawayEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGiveawayEntries::route('/'),
            'create' => CreateGiveawayEntry::route('/create'),
            'edit' => EditGiveawayEntry::route('/{record}/edit'),
        ];
    }
}

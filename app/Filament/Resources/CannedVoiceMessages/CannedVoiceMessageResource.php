<?php

namespace App\Filament\Resources\CannedVoiceMessages;

use App\Filament\Resources\CannedVoiceMessages\Pages\CreateCannedVoiceMessage;
use App\Filament\Resources\CannedVoiceMessages\Pages\EditCannedVoiceMessage;
use App\Filament\Resources\CannedVoiceMessages\Pages\ListCannedVoiceMessages;
use App\Filament\Resources\CannedVoiceMessages\Schemas\CannedVoiceMessageForm;
use App\Filament\Resources\CannedVoiceMessages\Tables\CannedVoiceMessagesTable;
use App\Models\CannedVoiceMessage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CannedVoiceMessageResource extends Resource
{
    protected static ?string $model = CannedVoiceMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSpeakerWave;

    protected static string|\UnitEnum|null $navigationGroup = 'Müşteriler';

    protected static ?string $navigationLabel = 'Sesli Anons Kayıtları';

    protected static ?string $modelLabel = 'Sesli Anons';

    protected static ?string $pluralModelLabel = 'Sesli Anons Kayıtları';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CannedVoiceMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CannedVoiceMessagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCannedVoiceMessages::route('/'),
            'create' => CreateCannedVoiceMessage::route('/create'),
            'edit' => EditCannedVoiceMessage::route('/{record}/edit'),
        ];
    }
}

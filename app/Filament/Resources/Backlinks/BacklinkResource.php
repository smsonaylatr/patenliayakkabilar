<?php

namespace App\Filament\Resources\Backlinks;

use App\Filament\Resources\Backlinks\Pages\CreateBacklink;
use App\Filament\Resources\Backlinks\Pages\EditBacklink;
use App\Filament\Resources\Backlinks\Pages\ListBacklinks;
use App\Filament\Resources\Backlinks\Schemas\BacklinkForm;
use App\Filament\Resources\Backlinks\Tables\BacklinksTable;
use App\Models\Backlink;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BacklinkResource extends Resource
{
    protected static ?string $model = Backlink::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|\UnitEnum|null $navigationGroup = 'Pazarlama';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Backlink Kaynağı';
    protected static ?string $pluralModelLabel = 'Backlink & SEO Outreach';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return BacklinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BacklinksTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('backlinks')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\BacklinkSeeder',
                    '--force' => true,
                ]);
            } catch (\Throwable $e) {
                // Ignore if already migrating
            }
        }

        return parent::getEloquentQuery();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBacklinks::route('/'),
            'create' => CreateBacklink::route('/create'),
            'edit' => EditBacklink::route('/{record}/edit'),
        ];
    }
}

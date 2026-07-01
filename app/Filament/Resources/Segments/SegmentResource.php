<?php

namespace App\Filament\Resources\Segments;

use App\Filament\Resources\Segments\Pages\CreateSegment;
use App\Filament\Resources\Segments\Pages\EditSegment;
use App\Filament\Resources\Segments\Pages\ListSegments;
use App\Models\CustomerSegment;
use BackedEnum;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SegmentResource extends Resource
{
    protected static ?string $model = CustomerSegment::class;
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static string|\UnitEnum|null $navigationGroup = 'Müşteriler';
    protected static ?string $modelLabel = 'Segment';
    protected static ?string $pluralModelLabel = 'Müşteri Segmentleri';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Segment Bilgileri')
                ->icon('heroicon-o-tag')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Segment Adı')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Otomatik oluşturulur'),
                    Select::make('type')
                        ->label('Tür')
                        ->options([
                            'static' => 'Statik (Manuel)',
                            'dynamic' => 'Dinamik (Otomatik)',
                        ])
                        ->default('dynamic')
                        ->native(false),
                    TextInput::make('color')
                        ->label('Renk')
                        ->default('#6b7280')
                        ->type('color'),
                    Textarea::make('description')
                        ->label('Açıklama')
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Segment')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'static' => 'Statik',
                        'dynamic' => 'Dinamik',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'static' => 'gray',
                        'dynamic' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('customer_count')
                    ->label('Müşteri Sayısı')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color('gray'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSegments::route('/'),
            'create' => CreateSegment::route('/create'),
            'edit' => EditSegment::route('/{record}/edit'),
        ];
    }
}

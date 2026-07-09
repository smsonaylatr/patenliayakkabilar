<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'Varyantlar (Numara / Renk)';
    protected static ?string $modelLabel = 'Varyant';
    protected static ?string $pluralModelLabel = 'Varyantlar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('color')
                    ->label('Renk')
                    ->options([
                        'Beyaz' => 'Beyaz',
                        'Siyah' => 'Siyah',
                        'Kırmızı' => 'Kırmızı',
                        'Mavi' => 'Mavi',
                        'Pembe' => 'Pembe',
                        'Yeşil' => 'Yeşil',
                        'Mor' => 'Mor',
                        'Turuncu' => 'Turuncu',
                        'Gri' => 'Gri',
                        'Lacivert' => 'Lacivert',
                    ])
                    ->multiple()
                    ->searchable()
                    ->native(false)
                    ->required()
                    ->helperText('Birden fazla renk seçebilirsiniz'),
                Select::make('size')
                    ->label('Numara')
                    ->options(
                        collect(range(28, 45))->mapWithKeys(fn ($size) => [(string) $size => (string) $size])->toArray()
                    )
                    ->searchable()
                    ->required(),
                Select::make('wheel_type')
                    ->label('Teker Tipi')
                    ->options([
                        'single' => 'Tek Teker',
                        'double' => 'Çift Teker',
                        'quad' => 'Dört Teker',
                        'led' => 'LED Tekerlekli',
                    ])
                    ->searchable(),
                TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0),
                TextInput::make('price_extra')
                    ->label('Fiyat Farkı')
                    ->numeric()
                    ->prefix('₺')
                    ->default(0)
                    ->step(0.01)
                    ->helperText('Ana fiyata eklenecek tutar'),
                TextInput::make('sku')
                    ->label('SKU')
                    ->helperText('Boş bırakılırsa otomatik oluşturulur'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('color')
                    ->label('Renk')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => is_array($state) ? implode(' / ', $state) : ($state ?? '-'))
                    ->searchable(),
                TextColumn::make('size')
                    ->label('Numara')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('wheel_type')
                    ->label('Teker Tipi')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'single' => 'Tek Teker',
                        'double' => 'Çift Teker',
                        'quad' => 'Dört Teker',
                        'led' => 'LED Tekerlekli',
                        default => $state ?? '-',
                    }),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->color(fn (int $state): string => $state <= 3 ? 'danger' : ($state <= 10 ? 'warning' : 'success'))
                    ->weight('bold'),
                TextColumn::make('price_extra')
                    ->label('Fiyat Farkı')
                    ->getStateUsing(fn ($record) => $record->price_extra > 0 ? '+' . number_format($record->price_extra, 2) . ' ₺' : '-')
                    ->color(fn ($record) => $record->price_extra > 0 ? 'warning' : 'gray'),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->color('gray'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Varyant Ekle'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Sipariş Kalemleri';
    protected static ?string $modelLabel = 'Kalem';
    protected static ?string $pluralModelLabel = 'Kalemler';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40),
                Tables\Columns\TextColumn::make('variant.color')
                    ->label('Renk')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => is_array($state) ? implode(' / ', $state) : ($state ?? '-'))
                    ->default('-'),
                Tables\Columns\TextColumn::make('variant.size')
                    ->label('Numara')
                    ->default('-'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Adet')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Birim Fiyat')
                    ->getStateUsing(fn ($record) => number_format($record->unit_price, 2) . ' ₺')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Toplam')
                    ->getStateUsing(fn ($record) => number_format($record->quantity * $record->unit_price, 2) . ' ₺')
                    ->weight('bold')
                    ->alignEnd(),
            ])
            ->paginated(false);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}

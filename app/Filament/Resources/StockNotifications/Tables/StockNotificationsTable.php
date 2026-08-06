<?php

namespace App\Filament\Resources\StockNotifications\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StockNotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variant.size')
                    ->label('Beden/Varyant')
                    ->badge()
                    ->placeholder('Tüm Ürün'),

                TextColumn::make('email')
                    ->label('E-Posta')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->placeholder('—'),

                IconColumn::make('is_notified')
                    ->label('Bildirildi')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Talebe Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('notified_at')
                    ->label('Bildirilme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_notified')
                    ->label('Bildirim Durumu')
                    ->trueLabel('Bildirildi')
                    ->falseLabel('Bekliyor'),
            ]);
    }
}

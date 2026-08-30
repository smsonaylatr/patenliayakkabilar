<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->product?->name)
                    ->url(fn ($record) => $record->product_id
                        ? route('filament.admin.resources.reviews.index', ['tableFilters[product_id][value]' => $record->product_id])
                        : null
                    )
                    ->color('primary'),
                TextColumn::make('name')
                    ->label('Müşteri')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Puan')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('comment')
                    ->label('Yorum')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->comment)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('order.order_number')
                    ->label('Sipariş')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('status')
                    ->label('Onay')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                SelectFilter::make('rating')
                    ->label('Puan')
                    ->options([
                        '5' => '⭐⭐⭐⭐⭐ (5)',
                        '4' => '⭐⭐⭐⭐ (4)',
                        '3' => '⭐⭐⭐ (3)',
                        '2' => '⭐⭐ (2)',
                        '1' => '⭐ (1)',
                    ])
                    ->native(false),
                TernaryFilter::make('status')
                    ->label('Onay Durumu')
                    ->trueLabel('Onaylı')
                    ->falseLabel('Beklemede')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

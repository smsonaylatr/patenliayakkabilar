<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('homepage_sort')
            ->afterReordering(function () {
                \Illuminate\Support\Facades\Cache::forget('home_product_grid_v2');
            })
            ->columns([
                ImageColumn::make('images.image_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->defaultImageUrl(fn () => 'https://placehold.co/80x80/f1f5f9/94a3b8?text=Ürün')
                    ->width(40)
                    ->height(40),
                TextColumn::make('name')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->name),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Fiyat')
                    ->getStateUsing(function ($record) {
                        if ($record->discount_price && $record->discount_price < $record->price) {
                            return number_format($record->discount_price, 2) . ' ₺';
                        }
                        return number_format($record->price, 2) . ' ₺';
                    })
                    ->description(fn ($record) =>
                        $record->discount_price && $record->discount_price < $record->price
                            ? number_format($record->price, 2) . ' ₺'
                            : null
                    )
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('brand')
                    ->label('Marka')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                IconColumn::make('status')
                    ->label('Aktif')
                    ->boolean(),
                IconColumn::make('featured')
                    ->label('Öne Çıkan')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('best_seller')
                    ->label('Çok Satan')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('variants_count')
                    ->label('Varyant')
                    ->counts('variants')
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Eklenme')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('status')
                    ->label('Aktiflik')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
                TernaryFilter::make('featured')
                    ->label('Öne Çıkan'),
                TernaryFilter::make('best_seller')
                    ->label('Çok Satan'),
                SelectFilter::make('stock_level')
                    ->label('Stok Durumu')
                    ->options([
                        'out' => 'Stokta Yok',
                        'low' => 'Düşük (1-5)',
                        'ok' => 'Yeterli (6+)',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'out' => $query->where('stock', 0),
                            'low' => $query->whereBetween('stock', [1, 5]),
                            'ok' => $query->where('stock', '>', 5),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('homepage_sort', 'asc');
    }
}

<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('Görsel')
                    ->disk('public')
                    ->circular()
                    ->size(40)
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Kategori Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Kopyalandı')
                    ->prefix('/kategori/')
                    ->limit(30),
                TextColumn::make('parent.name')
                    ->label('Üst Kategori')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('products_count')
                    ->label('Ürün')
                    ->counts('products')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),
                IconColumn::make('status')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                \Filament\Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Vitrinde')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('meta_title')
                    ->label('SEO')
                    ->limit(30)
                    ->placeholder('⚠️ Eksik')
                    ->color(fn ($state) => $state ? 'gray' : 'warning')
                    ->toggleable(),
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
                TernaryFilter::make('seo_durumu')
                    ->label('SEO Durumu')
                    ->placeholder('Tümü')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('meta_title')->where('meta_title', '!=', ''),
                        false: fn ($query) => $query->where(fn ($q) => $q->whereNull('meta_title')->orWhere('meta_title', '')),
                    )
                    ->trueLabel('SEO Tam')
                    ->falseLabel('SEO Eksik'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz kategori yok')
            ->emptyStateDescription('Ürünlerinizi organize etmek için kategori ekleyin.')
            ->emptyStateIcon('heroicon-o-tag');
    }
}

<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
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
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([10, 25, 50, 100])
            ->reorderable('homepage_sort')
            ->afterReordering(function () {
                \Illuminate\Support\Facades\Cache::forget('home_product_grid_v2');
            })
            ->columns([
                ImageColumn::make('images.image_path')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->stacked()
                    ->limit(1)
                    ->defaultImageUrl(fn () => 'https://placehold.co/80x80/f1f5f9/94a3b8?text=Ürün')
                    ->width(80)
                    ->height(80),
                TextColumn::make('name')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->name),
                TextColumn::make('categories.name')
                    ->label('Kategoriler')
                    ->searchable()
                    ->badge()
                    ->color('info'),
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
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('status')
                    ->label('Aktif')
                    ->boolean(),
                \Filament\Tables\Columns\ToggleColumn::make('featured')
                    ->label('Öne Çıkan')
                    ->sortable(),
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
                SelectFilter::make('categories')
                    ->label('Kategoriler')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->multiple()
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
                \Filament\Actions\Action::make('duplicate')
                    ->label('Çoğalt')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Ürünü Çoğalt')
                    ->modalDescription('Bu ürünün varyantları, görselleri ve özellikleriyle birlikte bir kopyası (taslak olarak) oluşturulacaktır. Onaylıyor musunuz?')
                    ->action(function (Product $record) {
                        $record->duplicate();
                        \Filament\Notifications\Notification::make()
                            ->title('Ürün başarıyla çoğaltıldı.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('replicate')
                        ->label('Çoğalt')
                        ->icon('heroicon-o-document-duplicate')
                        ->requiresConfirmation()
                        ->modalHeading('Seçili Ürünleri Çoğalt')
                        ->modalDescription('Seçili ürünlerin varyantları, görselleri ve özellikleriyle birlikte kopyaları oluşturulacaktır. Onaylıyor musunuz?')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            foreach ($records as $record) {
                                $record->duplicate();
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Seçili ürünler başarıyla çoğaltıldı.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    \Filament\Actions\BulkAction::make('syncToPorego')
                        ->label("Porego'ya Senkronize Et")
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $result = app(\App\Services\PoregoApiService::class)->syncProducts($records);
                            if ($result['success']) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Porego Senkronizasyonu Başarılı')
                                    ->body($result['message'])
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Porego Senkronizasyon Uyarısı')
                                    ->body($result['message'])
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\BulkAction::make('syncToGoogle')
                        ->label("Google Merchant'a Gönder")
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            foreach ($records as $record) {
                                \App\Jobs\SyncProductToGoogleMerchant::dispatch($record);
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Ürünler sıraya eklendi')
                                ->body('Seçilen ürünler arka planda Google Merchant Center\'a gönderilecektir.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('homepage_sort', 'asc');
    }
}

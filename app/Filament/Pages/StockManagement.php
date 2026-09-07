<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StockManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Stok Yönetimi';
    protected static ?string $title = 'Stok Yönetimi';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Mağaza';
    }

    protected static string $view = 'filament.pages.stock-management';

    public function getViewData(): array
    {
        return [
            'totalProducts' => Product::where('status', true)->count(),
            'inStock'       => Product::where('status', true)->where('stock', '>', 0)->count(),
            'outOfStock'    => Product::where('status', true)->where('stock', '<=', 0)->count(),
            'lowStock'      => Product::where('status', true)->where('stock', '>', 0)->where('stock', '<=', 5)->count(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->with('product')
                    ->whereHas('product', fn ($q) => $q->where('status', true))
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->product?->name),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->size('sm')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('size')
                    ->label('Beden')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('color')
                    ->label('Renk')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->size('sm'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 3 => 'danger',
                        $state <= 5 => 'warning',
                        default     => 'success',
                    })
                    ->formatStateUsing(fn (int $state): string => match (true) {
                        $state <= 0 => '❌ Tükendi',
                        $state <= 3 => "🔴 {$state} Adet",
                        $state <= 5 => "⚠️ {$state} Adet",
                        default     => "✅ {$state} Adet",
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' ₺')
                    ->sortable(),
            ])
            ->defaultSort('stock', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Stok Durumu')
                    ->options([
                        'out_of_stock' => '❌ Tükendi',
                        'low_stock'    => '⚠️ Düşük Stok (1-5)',
                        'in_stock'     => '✅ Yeterli Stok (6+)',
                    ])
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'out_of_stock' => $query->where('stock', '<=', 0),
                            'low_stock'    => $query->where('stock', '>', 0)->where('stock', '<=', 5),
                            'in_stock'     => $query->where('stock', '>', 5),
                            default        => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('update_stock')
                    ->label('Stok Güncelle')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->form([
                        TextInput::make('new_stock')
                            ->label('Yeni Stok Miktarı')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(fn ($record) => $record->stock),
                    ])
                    ->action(function (ProductVariant $record, array $data): void {
                        $oldStock = (int) $record->stock;
                        $newStock = (int) $data['new_stock'];

                        if ($newStock === $oldStock) return;

                        $record->update(['stock' => $newStock]);
                        $record->product?->syncFromVariants();

                        StockMovement::record(
                            productId: $record->product_id,
                            variantId: $record->id,
                            type: StockMovement::TYPE_ADJUSTMENT,
                            quantity: abs($newStock - $oldStock),
                            oldStock: $oldStock,
                            newStock: $newStock,
                            reference: null,
                            note: "Admin panelden hızlı stok güncelleme ({$oldStock} → {$newStock})",
                        );

                        Notification::make()
                            ->title('Stok güncellendi')
                            ->body("{$record->product?->name} (Beden: {$record->size}) → {$oldStock} → {$newStock}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('add_stock')
                    ->label('Stok Ekle')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('add_amount')
                            ->label('Eklenecek Miktar')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(10),
                    ])
                    ->action(function (ProductVariant $record, array $data): void {
                        $addQty = (int) $data['add_amount'];
                        $record->safeIncrement(
                            $addQty,
                            StockMovement::TYPE_RESTOCK,
                            null,
                            "Admin panelden stok ekleme"
                        );
                        $record->product?->syncFromVariants();

                        Notification::make()
                            ->title('Stok eklendi')
                            ->body("{$record->product?->name} (Beden: {$record->size}) → +{$addQty} adet. Yeni: {$record->stock}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_add_stock')
                    ->label('Toplu Stok Ekle')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->label('Her Varyanta Eklenecek Miktar')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(10),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $amount = (int) $data['amount'];
                        if ($amount <= 0) return;

                        foreach ($records as $record) {
                            $record->safeIncrement(
                                $amount,
                                StockMovement::TYPE_RESTOCK,
                                null,
                                "Toplu stok ekleme"
                            );
                            $record->product?->syncFromVariants();
                        }

                        Notification::make()
                            ->title($records->count() . " varyanta {$amount}'er adet stok eklendi")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }
}

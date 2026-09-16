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

    protected static ?int $navigationSort = 3;

    public function getView(): string
    {
        return 'filament.pages.stock-management';
    }

    public static function getNavigationIcon(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'heroicon-o-cube';
    }

    public static function getNavigationLabel(): string
    {
        return 'Stok Yönetimi';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Stok Yönetimi';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Satışlar';
    }

    public function getViewData(): array
    {
        $activeProducts = Product::with(['variants', 'images'])->where('status', true)->orderBy('name')->get();
        $matrix = [];
        $sizes = range(29, 42);

        foreach ($activeProducts as $product) {
            if ($product->variants->isEmpty()) continue;
            
            $productRow = [
                'product_id' => $product->id,
                'name' => $product->name,
                'image' => $product->images->first()?->image_path,
                'sizes' => [],
                'total' => 0,
            ];
            
            foreach ($sizes as $size) {
                $variant = $product->variants->where('size', (string)$size)->first();
                $productRow['sizes'][$size] = [
                    'stock' => $variant ? (int) $variant->stock : null,
                    'variant_id' => $variant?->id,
                ];
                if ($variant) {
                    $productRow['total'] += (int) $variant->stock;
                }
            }
            
            $matrix[] = $productRow;
        }

        return [
            'totalProducts' => Product::where('status', true)->count(),
            'inStock'       => Product::where('status', true)->where('stock', '>', 0)->count(),
            'outOfStock'    => Product::where('status', true)->where('stock', '<=', 0)->count(),
            'lowStock'      => Product::where('status', true)->where('stock', '>', 0)->where('stock', '<=', 5)->count(),
            'matrix'        => $matrix,
            'sizes'         => $sizes,
        ];
    }

    /**
     * Beden matrisinden inline stok güncelleme
     */
    public function updateMatrixStock(int $variantId, int $newStock): void
    {
        $variant = ProductVariant::find($variantId);
        if (!$variant) return;

        $oldStock = (int) $variant->stock;
        if ($newStock === $oldStock) return;
        if ($newStock < 0) $newStock = 0;

        $variant->update(['stock' => $newStock]);
        $variant->product?->syncFromVariants();

        StockMovement::record(
            productId: $variant->product_id,
            variantId: $variant->id,
            type: StockMovement::TYPE_ADJUSTMENT,
            quantity: abs($newStock - $oldStock),
            oldStock: $oldStock,
            newStock: $newStock,
            reference: null,
            note: "Beden matrisinden hızlı güncelleme ({$oldStock} → {$newStock})",
        );

        Notification::make()
            ->title('Stok güncellendi')
            ->body("{$variant->product?->name} (Beden: {$variant->size}) → {$oldStock} → {$newStock}")
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->with(['product.images'])
                    ->whereHas('product', fn ($q) => $q->where('status', true))
            )
            ->groups([
                Tables\Grouping\Group::make('product.name')
                    ->label('Ürün')
                    ->collapsible(),
            ])
            ->defaultGroup('product.name')
            ->columns([
                Tables\Columns\ImageColumn::make('product.images.0.image_path')
                    ->label('Görsel')
                    ->disk('public')
                    ->square()
                    ->size(76)
                    ->defaultImageUrl(url('/favicon.png'))
                    ->getStateUsing(fn ($record) => $record->product?->images?->first()?->image_path),
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
            ->defaultSort('product.name', 'asc')
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
                Tables\Filters\SelectFilter::make('size')
                    ->label('Beden')
                    ->options(array_combine(range(28, 45), range(28, 45)))
                    ->multiple()
                    ->native(false),
            ])
            ->actions([
                \Filament\Actions\Action::make('view_detail')
                    ->label('Detay')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalWidth('xl')
                    ->modalHeading(fn (ProductVariant $record) => $record->product?->name . ' - Stok Detayı')
                    ->modalContent(fn (ProductVariant $record) => view('filament.pages.product-stock-detail-modal', [
                        'product' => $record->product,
                        'movements' => StockMovement::where('product_id', $record->product_id)->latest()->take(10)->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (\Filament\Actions\StaticAction $action) => $action->label('Kapat')),
                \Filament\Actions\Action::make('update_stock')
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
                \Filament\Actions\Action::make('add_stock')
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
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulk_set_stock')
                        ->label('Toplu Stok Ayarla')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('new_stock')
                                ->label('Yeni Stok Miktarı')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $newStock = (int) $data['new_stock'];
                            foreach ($records as $record) {
                                $oldStock = (int) $record->stock;
                                if ($oldStock === $newStock) continue;
                                
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
                                    note: "Admin panelden toplu stok ayarlama ({$oldStock} → {$newStock})",
                                );
                            }
                            Notification::make()
                                ->title("Seçili varyantların stoğu {$newStock} olarak ayarlandı.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\BulkAction::make('bulk_zero_stock')
                        ->label('Stoğu Sıfırla')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $oldStock = (int) $record->stock;
                                if ($oldStock === 0) continue;
                                
                                $record->update(['stock' => 0]);
                                $record->product?->syncFromVariants();
                                
                                StockMovement::record(
                                    productId: $record->product_id,
                                    variantId: $record->id,
                                    type: StockMovement::TYPE_ADJUSTMENT,
                                    quantity: $oldStock,
                                    oldStock: $oldStock,
                                    newStock: 0,
                                    reference: null,
                                    note: "Admin panelden toplu stok sıfırlama ({$oldStock} → 0)",
                                );
                            }
                            Notification::make()
                                ->title("Seçili varyantların stoğu sıfırlandı.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\BulkAction::make('bulk_add_stock')
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
                ]),
            ]);
    }
}

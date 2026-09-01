<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\OrderItem;
use App\Models\CartItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopSellingProducts extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '🏆 En Çok Satan Ürünler (Top 50)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->addSelect([
                        'total_sold' => OrderItem::query()
                            ->selectRaw('COALESCE(SUM(order_items.quantity), 0)')
                            ->whereColumn('order_items.product_id', 'products.id'),
                        'abandoned_count' => CartItem::query()
                            ->selectRaw('COALESCE(SUM(cart_items.quantity), 0)')
                            ->whereColumn('cart_items.product_id', 'products.id')
                            ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
                            ->where('carts.updated_at', '<=', now()->subHours(2)),
                    ])
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('order_items')
                            ->whereColumn('order_items.product_id', 'products.id');
                    })
                    ->orderByDesc('total_sold')
                    ->limit(50)
            )
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('#')
                    ->rowIndex(),
                Tables\Columns\ImageColumn::make('product_image')
                    ->label('Görsel')
                    ->getStateUsing(fn ($record) => $record->images()->orderBy('sort_order')->first()?->image_path)
                    ->disk('public')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->weight('bold')
                    ->tooltip(fn ($record) => $record->name),
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Satış Adedi')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('size_breakdown')
                    ->label('Numara Bazlı Satış')
                    ->getStateUsing(function ($record) {
                        $breakdown = OrderItem::query()
                            ->where('order_items.product_id', $record->id)
                            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
                            ->select('product_variants.size', DB::raw('SUM(order_items.quantity) as qty'))
                            ->groupBy('product_variants.size')
                            ->orderBy('product_variants.size')
                            ->get();

                        if ($breakdown->isEmpty()) {
                            return '-';
                        }

                        return $breakdown
                            ->map(fn ($item) => $item->size . ': ' . $item->qty . ' ad.')
                            ->implode(' | ');
                    })
                    ->wrap()
                    ->html(),
                Tables\Columns\TextColumn::make('abandoned_count')
                    ->label('Sepette Terk')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('abandoned_size_breakdown')
                    ->label('Terk — Numara Detayı')
                    ->getStateUsing(function ($record) {
                        $breakdown = CartItem::query()
                            ->where('cart_items.product_id', $record->id)
                            ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
                            ->join('product_variants', 'product_variants.id', '=', 'cart_items.product_variant_id')
                            ->where('carts.updated_at', '<=', now()->subHours(2))
                            ->select('product_variants.size', DB::raw('SUM(cart_items.quantity) as qty'))
                            ->groupBy('product_variants.size')
                            ->orderBy('product_variants.size')
                            ->get();

                        if ($breakdown->isEmpty()) {
                            return '-';
                        }

                        return $breakdown
                            ->map(fn ($item) => $item->size . ': ' . $item->qty . ' ad.')
                            ->implode(' | ');
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->getStateUsing(fn ($record) => number_format($record->discount_price ?? $record->price, 2) . ' ₺')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Mevcut Stok')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('total_sold', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(50)
            ->striped()
            ->emptyStateHeading('Henüz satış yok')
            ->emptyStateDescription('Sipariş verildiğinde en çok satan ürünler burada görünecek.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}

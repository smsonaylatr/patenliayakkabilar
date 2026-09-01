<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
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
        // Sipariş edilen ürünlerin ID'lerini satış adedine göre sırala
        $topProductIds = OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(50)
            ->pluck('total_qty', 'product_id');

        $orderedIds = $topProductIds->keys()->toArray();

        return $table
            ->query(
                Product::query()
                    ->withTrashed()
                    ->whereIn('id', $orderedIds ?: [0])
                    ->orderByRaw(
                        $orderedIds
                            ? 'FIELD(id, ' . implode(',', $orderedIds) . ')'
                            : 'id'
                    )
            )
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('#')
                    ->rowIndex(),
                Tables\Columns\ImageColumn::make('product_image')
                    ->label('Görsel')
                    ->getStateUsing(fn ($record) => $record->images()->orderBy('sort_order')->first()?->image_path)
                    ->disk('public')
                    ->square()
                    ->size(56),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->getStateUsing(fn ($record) => $record->sku ?: $record->name)
                    ->weight('bold')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('SKU kopyalandı!')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->name),
                Tables\Columns\TextColumn::make('sold_qty')
                    ->label('Satış Adedi')
                    ->getStateUsing(fn ($record) => OrderItem::where('product_id', $record->id)->sum('quantity'))
                    ->badge()
                    ->color('success')
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
                    ->wrap(),
                Tables\Columns\TextColumn::make('abandoned_qty')
                    ->label('Sepette Terk')
                    ->getStateUsing(function ($record) {
                        return CartItem::query()
                            ->where('cart_items.product_id', $record->id)
                            ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
                            ->where('carts.updated_at', '<=', now()->subHours(2))
                            ->sum('cart_items.quantity');
                    })
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray')
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
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(50)
            ->striped()
            ->emptyStateHeading('Henüz satış yok')
            ->emptyStateDescription('Sipariş verildiğinde en çok satan ürünler burada görünecek.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}

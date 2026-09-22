<?php

namespace App\Filament\Pages;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ProductSalesReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?int $navigationSort = 4;

    public function getView(): string
    {
        return 'filament.pages.product-sales-report';
    }

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationLabel(): string
    {
        return 'Satış Raporu';
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Ürün Bazlı Satış Raporu';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Satışlar';
    }

    /**
     * Ürün bazlı satış özet verileri
     */
    public function getViewData(): array
    {
        // Teslim edilen siparişler
        $deliveredQuery = OrderItem::whereHas('order', fn($q) => $q->where('status', 'delivered'));
        $totalDelivered = (clone $deliveredQuery)->sum('quantity');
        $totalRevenue = (clone $deliveredQuery)->sum('total_price');
        $uniqueCustomers = OrderItem::whereHas('order', fn($q) => $q->where('status', 'delivered'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->distinct('orders.customer_phone')
            ->count('orders.customer_phone');
        $uniqueProducts = (clone $deliveredQuery)->distinct('product_id')->count('product_id');

        // İade edilen siparişler
        $returnedQuery = OrderItem::whereHas('order', fn($q) => $q->whereIn('status', ['returned', 'return_started']));
        $totalReturned = (clone $returnedQuery)->sum('quantity');
        $totalReturnedRevenue = (clone $returnedQuery)->sum('total_price');
        $returnRate = $totalDelivered > 0 ? round(($totalReturned / ($totalDelivered + $totalReturned)) * 100, 1) : 0;

        // Ürün bazlı satış özeti (teslim edilen)
        $productSummary = OrderItem::query()
            ->select('product_id', 'product_name')
            ->selectRaw('SUM(quantity) as total_qty')
            ->selectRaw('SUM(total_price) as total_revenue')
            ->selectRaw('COUNT(DISTINCT order_id) as order_count')
            ->whereHas('order', fn($q) => $q->where('status', 'delivered'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->get();

        // Ürün bazlı iade özeti
        $returnSummary = OrderItem::query()
            ->select('product_id', 'product_name')
            ->selectRaw('SUM(quantity) as total_qty')
            ->selectRaw('SUM(total_price) as total_revenue')
            ->selectRaw('COUNT(DISTINCT order_id) as order_count')
            ->whereHas('order', fn($q) => $q->whereIn('status', ['returned', 'return_started']))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->get();

        // Beden bazlı satış dağılımı (teslim edilen)
        $sizeSummary = OrderItem::query()
            ->select('variant_info')
            ->selectRaw('SUM(quantity) as total_qty')
            ->whereHas('order', fn($q) => $q->where('status', 'delivered'))
            ->whereNotNull('variant_info')
            ->groupBy('variant_info')
            ->orderByDesc('total_qty')
            ->limit(20)
            ->get();

        return [
            'totalDelivered' => (int) $totalDelivered,
            'totalRevenue' => $totalRevenue,
            'uniqueCustomers' => (int) $uniqueCustomers,
            'uniqueProducts' => (int) $uniqueProducts,
            'totalReturned' => (int) $totalReturned,
            'totalReturnedRevenue' => $totalReturnedRevenue,
            'returnRate' => $returnRate,
            'productSummary' => $productSummary,
            'returnSummary' => $returnSummary,
            'sizeSummary' => $sizeSummary,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->with(['order', 'product.images', 'variant'])
                    ->whereHas('order', fn(Builder $q) => $q->whereIn('status', ['delivered', 'returned', 'return_started']))
                    ->latest('order_items.created_at')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('product.images.0.image_path')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->size(45)
                    ->defaultImageUrl(url('/favicon.png'))
                    ->getStateUsing(fn ($record) => $record->product?->images?->first()?->image_path),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('variant_info')
                    ->label('Beden / Renk')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Adet')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (OrderItem $record) => match ($record->order?->status) {
                        'returned', 'return_started' => 'danger',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Toplam')
                    ->money('TRY')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('order.status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'delivered' => '✅ Teslim Edildi',
                        'returned' => '🔄 İade Edildi',
                        'return_started' => '📦 İade Sürecinde',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'delivered' => 'success',
                        'returned' => 'danger',
                        'return_started' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->sortable()
                    ->color('warning')
                    ->url(fn (OrderItem $record) => $record->order
                        ? \App\Filament\Resources\Orders\OrderResource::getUrl('edit', ['record' => $record->order_id])
                        : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('order.customer_name')
                    ->label('Müşteri')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('order.customer_phone')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('order.shipping_city')
                    ->label('Şehir')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('order.payment_method')
                    ->label('Ödeme')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'credit_card' => '💳 Kredi Kartı',
                        'cash_on_delivery' => '🚚 Kapıda Ödeme',
                        'wire_transfer' => '🏦 Havale/EFT',
                        default => $state ?? '-',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('order.created_at')
                    ->label('Sipariş Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_status')
                    ->label('Durum')
                    ->options([
                        'delivered' => '✅ Teslim Edildi',
                        'returned' => '🔄 İade Edildi',
                        'return_started' => '📦 İade Sürecinde',
                    ])
                    ->query(fn (Builder $query, array $data) =>
                        $query->when($data['value'] ?? null, fn($q, $v) =>
                            $q->whereHas('order', fn($oq) => $oq->where('status', $v))
                        )
                    )
                    ->native(false),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Ürün')
                    ->options(fn () => Product::where('status', true)->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->native(false),

                Tables\Filters\SelectFilter::make('variant_info')
                    ->label('Beden')
                    ->options(fn () => OrderItem::query()
                        ->whereHas('order', fn($q) => $q->whereIn('status', ['delivered', 'returned', 'return_started']))
                        ->whereNotNull('variant_info')
                        ->distinct()
                        ->pluck('variant_info', 'variant_info')
                        ->sort()
                        ->toArray()
                    )
                    ->searchable()
                    ->native(false),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç Tarihi')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Bitiş Tarihi')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date) => $q->whereHas('order', fn($oq) => $oq->whereDate('created_at', '>=', $date))
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date) => $q->whereHas('order', fn($oq) => $oq->whereDate('created_at', '<=', $date))
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Başlangıç: ' . \Carbon\Carbon::parse($data['from'])->format('d.m.Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Bitiş: ' . \Carbon\Carbon::parse($data['until'])->format('d.m.Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Ödeme Yöntemi')
                    ->options([
                        'credit_card' => '💳 Kredi Kartı',
                        'cash_on_delivery' => '🚚 Kapıda Ödeme',
                        'wire_transfer' => '🏦 Havale/EFT',
                    ])
                    ->query(fn (Builder $query, array $data) =>
                        $query->when($data['value'] ?? null, fn($q, $v) =>
                            $q->whereHas('order', fn($oq) => $oq->where('payment_method', $v))
                        )
                    )
                    ->native(false),

                Tables\Filters\SelectFilter::make('shipping_city')
                    ->label('Şehir')
                    ->options(fn () => \App\Models\Order::query()
                        ->whereIn('status', ['delivered', 'returned', 'return_started'])
                        ->whereNotNull('shipping_city')
                        ->distinct()
                        ->pluck('shipping_city', 'shipping_city')
                        ->sort()
                        ->toArray()
                    )
                    ->query(fn (Builder $query, array $data) =>
                        $query->when($data['value'] ?? null, fn($q, $v) =>
                            $q->whereHas('order', fn($oq) => $oq->where('shipping_city', $v))
                        )
                    )
                    ->searchable()
                    ->native(false),
            ])
            ->defaultSort('order_items.created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->poll('60s');
    }
}

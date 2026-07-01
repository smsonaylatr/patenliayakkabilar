<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '⚠️ Düşük Stok Uyarısı';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '<=', 5)
                    ->where('status', true)
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state): string => $state === 0 ? 'danger' : 'warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->getStateUsing(fn ($record) => number_format($record->price, 2) . ' ₺'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Tüm stoklar yeterli')
            ->emptyStateDescription('Stoku 5 ve altına düşen ürün yok.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

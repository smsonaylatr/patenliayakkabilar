<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '⚠️ Düşük Stok & Tükenen Ürünler';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->with('product')
                    ->whereHas('product', fn ($q) => $q->where('status', true))
                    ->where('stock', '<=', 5)
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->weight('bold')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->product?->name),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->color('gray')
                    ->size('sm'),
                Tables\Columns\TextColumn::make('size')
                    ->label('Beden')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('color')
                    ->label('Renk')
                    ->getStateUsing(fn ($record) => is_array($record->color) ? implode(', ', $record->color) : $record->color)
                    ->size('sm'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 3 => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (int $state): string => $state <= 0 ? '❌ Tükendi' : "⚠️ {$state} Adet")
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->getStateUsing(fn ($record) => number_format($record->price ?: $record->product?->price ?: 0, 2) . ' ₺')
                    ->size('sm'),
            ])
            ->actions([
                Action::make('quickStock')
                    ->label('Stok Ekle')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('new_stock')
                            ->label('Eklenecek Stok Miktarı')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(10),
                    ])
                    ->action(function (ProductVariant $record, array $data): void {
                        $addQty = (int) $data['new_stock'];
                        $record->safeIncrement(
                            $addQty,
                            \App\Models\StockMovement::TYPE_RESTOCK,
                            null,
                            "Admin panelden hızlı stok ekleme"
                        );
                        $record->product?->syncFromVariants();

                        Notification::make()
                            ->title('Stok güncellendi')
                            ->body("{$record->product?->name} (Beden: {$record->size}) → +{$addQty} adet eklendi. Yeni stok: {$record->stock}")
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated(false)
            ->emptyStateHeading('Tüm stoklar yeterli 🎉')
            ->emptyStateDescription('Stoku 5 ve altına düşen varyant yok.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

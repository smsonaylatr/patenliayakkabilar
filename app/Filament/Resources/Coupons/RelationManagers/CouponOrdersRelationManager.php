<?php

namespace App\Filament\Resources\Coupons\RelationManagers;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Bu Kuponu Kullanan Siparişler';

    protected static ?string $modelLabel = 'Sipariş';

    protected static ?string $pluralModelLabel = 'Siparişler';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->weight('bold')
                    ->url(fn ($record) => OrderResource::getUrl('edit', ['record' => $record]))
                    ->color('primary'),
                TextColumn::make('customer_name')
                    ->label('Müşteri Adı')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('Telefon')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label('E-posta'),
                TextColumn::make('grand_total')
                    ->label('Sipariş Tutarı')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' ₺')
                    ->sortable(),
                TextColumn::make('discount_total')
                    ->label('İndirim')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '-' . number_format($state, 2) . ' ₺' : '—')
                    ->color('danger'),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'confirmed' => 'Onaylandı',
                        'preparing' => 'Hazırlanıyor',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal',
                        'returned' => 'İade',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'preparing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'returned' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Sipariş Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Henüz kullanılmamış')
            ->emptyStateDescription('Bu kupon henüz hiçbir siparişte kullanılmadı.')
            ->emptyStateIcon('heroicon-o-shopping-cart');
    }
}

<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistory';
    protected static ?string $title = 'Durum Geçmişi';
    protected static ?string $modelLabel = 'Durum Değişikliği';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('old_status')
                    ->label('Eski Durum')
                    ->badge()
                    ->color(fn (?string $state) => $this->statusColor($state))
                    ->formatStateUsing(fn (?string $state) => $this->statusLabel($state))
                    ->default('-'),
                TextColumn::make('new_status')
                    ->label('Yeni Durum')
                    ->badge()
                    ->color(fn (?string $state) => $this->statusColor($state))
                    ->formatStateUsing(fn (?string $state) => $this->statusLabel($state)),
                TextColumn::make('old_payment_status')
                    ->label('Eski Ödeme')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state) => $this->paymentLabel($state))
                    ->default('-'),
                TextColumn::make('new_payment_status')
                    ->label('Yeni Ödeme')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state) => $this->paymentLabel($state))
                    ->default('-'),
                TextColumn::make('changedBy.name')
                    ->label('Değiştiren')
                    ->default('Sistem'),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    private function statusColor(?string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Beklemede',
            'processing' => 'Hazırlanıyor',
            'shipped' => 'Kargoda',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal',
            default => $status ?? '-',
        };
    }

    private function paymentLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Beklemede',
            'paid' => 'Ödendi',
            'refunded' => 'İade',
            'failed' => 'Başarısız',
            default => $status ?? '-',
        };
    }
}

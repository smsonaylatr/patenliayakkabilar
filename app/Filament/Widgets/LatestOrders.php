<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Son Siparişler';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Müşteri')
                    ->icon(fn (Order $record) => match (true) {
                        str_contains(strtolower($record->traffic_source ?? ''), 'instagram') => 'heroicon-m-camera',
                        str_contains(strtolower($record->traffic_source ?? ''), 'google ads') => 'heroicon-m-arrow-trending-up',
                        str_contains(strtolower($record->traffic_source ?? ''), 'google') => 'heroicon-m-magnifying-glass',
                        str_contains(strtolower($record->traffic_source ?? ''), 'facebook') || str_contains(strtolower($record->traffic_source ?? ''), 'meta') => 'heroicon-m-globe-alt',
                        str_contains(strtolower($record->traffic_source ?? ''), 'tiktok') => 'heroicon-m-video-camera',
                        str_contains(strtolower($record->traffic_source ?? ''), 'whatsapp') => 'heroicon-m-chat-bubble-oval-left',
                        str_contains(strtolower($record->traffic_source ?? ''), 'admin') => 'heroicon-m-cog-6-tooth',
                        default => 'heroicon-m-cursor-arrow-rays',
                    })
                    ->iconPosition(IconPosition::After)
                    ->iconColor(fn (Order $record) => match (true) {
                        str_contains(strtolower($record->traffic_source ?? ''), 'instagram') => 'pink',
                        str_contains(strtolower($record->traffic_source ?? ''), 'google ads') => 'info',
                        str_contains(strtolower($record->traffic_source ?? ''), 'google') => 'primary',
                        str_contains(strtolower($record->traffic_source ?? ''), 'facebook') || str_contains(strtolower($record->traffic_source ?? ''), 'meta') => 'indigo',
                        str_contains(strtolower($record->traffic_source ?? ''), 'tiktok') => 'gray',
                        str_contains(strtolower($record->traffic_source ?? ''), 'whatsapp') => 'success',
                        str_contains(strtolower($record->traffic_source ?? ''), 'admin') => 'purple',
                        default => 'gray',
                    })
                    ->tooltip(fn (Order $record) => match (true) {
                        str_contains(strtolower($record->traffic_source ?? ''), 'instagram') => '📸 Instagram (' . ($record->device_type ?: 'Mobil') . ')' . ($record->utm_campaign ? ' — Kampanya: ' . $record->utm_campaign : ''),
                        str_contains(strtolower($record->traffic_source ?? ''), 'google ads') => '🎯 Google Ads Reklamı (' . ($record->device_type ?: 'Mobil') . ')' . ($record->utm_campaign ? ' — Kampanya: ' . $record->utm_campaign : ''),
                        str_contains(strtolower($record->traffic_source ?? ''), 'google') => '🔍 Google Doğal Arama (' . ($record->device_type ?: 'Mobil') . ')',
                        str_contains(strtolower($record->traffic_source ?? ''), 'facebook') || str_contains(strtolower($record->traffic_source ?? ''), 'meta') => '👥 Facebook (' . ($record->device_type ?: 'Mobil') . ')',
                        str_contains(strtolower($record->traffic_source ?? ''), 'tiktok') => '🎵 TikTok (' . ($record->device_type ?: 'Mobil') . ')',
                        str_contains(strtolower($record->traffic_source ?? ''), 'whatsapp') => '💬 WhatsApp (' . ($record->device_type ?: 'Mobil') . ')',
                        str_contains(strtolower($record->traffic_source ?? ''), 'admin') => '⚙️ Admin Manuel Sipariş',
                        default => '⚡ Doğrudan Giriş (' . ($record->device_type ?: 'Mobil') . ')',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Beklemede',
                        'processing' => 'Hazırlanıyor',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Tutar')
                    ->getStateUsing(fn ($record) => number_format($record->grand_total, 2) . ' ₺')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d M Y H:i')
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}

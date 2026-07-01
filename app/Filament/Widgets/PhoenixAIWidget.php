<?php

namespace App\Filament\Widgets;

use App\Models\AiRecommendation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PhoenixAIWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '🧠 Phoenix AI Önerileri';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AiRecommendation::query()
                    ->where('status', 'pending')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    })
                    ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'critical' => 'Kritik',
                        'high' => 'Yüksek',
                        'medium' => 'Orta',
                        'low' => 'Düşük',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label('Öneri')
                    ->weight('bold')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Detay')
                    ->limit(80)
                    ->color('gray')
                    ->wrap(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'stock_alert' => 'Stok',
                        'customer_retention' => 'Müşteri Kaybı',
                        'vip_at_risk' => 'VIP Risk',
                        'revenue_drop' => 'Gelir',
                        'abandoned_carts' => 'Terk Sepet',
                        'vip_opportunity' => 'VIP Fırsat',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->since()
                    ->color('gray'),
            ])
            ->actions([
                \Filament\Actions\Action::make('accept')
                    ->label('Uygula')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (AiRecommendation $record) => $record->update(['status' => 'completed'])),
                \Filament\Actions\Action::make('dismiss')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->action(fn (AiRecommendation $record) => $record->update(['status' => 'dismissed'])),
            ])
            ->emptyStateHeading('Şu an öneri yok')
            ->emptyStateDescription('Phoenix AI sürekli analiz yapıyor. Yeni öneriler otomatik görünecek.')
            ->emptyStateIcon('heroicon-o-sparkles')
            ->paginated(false);
    }
}

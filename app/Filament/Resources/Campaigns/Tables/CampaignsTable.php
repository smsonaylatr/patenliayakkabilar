<?php

namespace App\Filament\Resources\Campaigns\Tables;

use App\Models\Campaign;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kampanya Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(function (Campaign $record): string {
                        $signals = $record->audience_signals ?? [];
                        $keywords = count((array)($signals['intent_keywords'] ?? []));
                        $goal = match ($record->conversion_goal) {
                            'purchase' => 'Alışveriş',
                            'add_to_cart' => 'Sepet',
                            'lead' => 'Potansiyel Müşteri',
                            default => 'Genel',
                        };
                        return "Hedef: {$goal} • {$keywords} Niyet Sinyali";
                    }),

                TextColumn::make('channel')
                    ->label('Kanal')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'google_ads' => '🎯 Google Ads',
                        'meta_ads' => '📱 Meta Ads',
                        'email' => '✉️ E-posta',
                        'sms' => '💬 SMS',
                        'omnichannel' => '🌐 Omnichannel',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'google_ads' => 'info',
                        'meta_ads' => 'primary',
                        'email' => 'warning',
                        'sms' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('customerSegment.name')
                    ->label('Hedef Kitle')
                    ->badge()
                    ->color('secondary')
                    ->default(fn (Campaign $record) => !empty($record->audience_signals['demographics']) ? 'Özel Sinyal' : 'Tüm Kitle'),

                TextColumn::make('ad_strength')
                    ->label('Varlık Gücü')
                    ->state(function (Campaign $record): string {
                        $strength = $record->calculateAdStrength();
                        return "{$strength['grade']} (%{$strength['score']})";
                    })
                    ->badge()
                    ->color(fn (Campaign $record): string => $record->calculateAdStrength()['color']),

                TextColumn::make('conversion_metrics')
                    ->label('Dönüşüm & Ciro')
                    ->state(function (Campaign $record): string {
                        $rev = number_format((float)$record->conversion_revenue, 2, ',', '.') . ' ₺';
                        return "{$record->conversions_count} Sipariş / {$rev}";
                    })
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('conversion_revenue', $direction)),

                TextColumn::make('roas')
                    ->label('ROAS')
                    ->state(function (Campaign $record): string {
                        $roas = $record->roas;
                        return $roas !== null ? "{$roas}x" : '-';
                    })
                    ->badge()
                    ->color(function (Campaign $record): string {
                        $roas = $record->roas;
                        if ($roas === null) return 'gray';
                        $target = (float) $record->target_roas;
                        if ($target > 0 && $roas >= $target) return 'success';
                        return $roas >= 2.5 ? 'info' : 'warning';
                    }),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Taslak',
                        'scheduled' => 'Zamanlandı',
                        'running' => 'Yayında',
                        'paused' => 'Durduruldu',
                        'completed' => 'Tamamlandı',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'running' => 'success',
                        'paused' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('scheduled_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->label('Kanal')
                    ->native(false)
                    ->options([
                        'google_ads' => 'Google Ads',
                        'meta_ads' => 'Meta Ads',
                        'email' => 'E-posta',
                        'sms' => 'SMS',
                        'omnichannel' => 'Omnichannel',
                    ]),
                SelectFilter::make('status')
                    ->label('Durum')
                    ->native(false)
                    ->options([
                        'draft' => 'Taslak',
                        'scheduled' => 'Zamanlandı',
                        'running' => 'Yayında',
                        'paused' => 'Durduruldu',
                        'completed' => 'Tamamlandı',
                    ]),
            ])
            ->recordActions([
                Action::make('copy_link')
                    ->label('UTM Linki')
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->action(function (Campaign $record) {
                        $url = $record->generateTrackingUrl();
                        Notification::make()
                            ->title('UTM Takip Linki Hazır')
                            ->body($url)
                            ->success()
                            ->persistent()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

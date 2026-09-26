<?php

namespace App\Filament\Resources\StockNotifications\Tables;

use App\Models\StockNotification;
use App\Services\StockNotificationService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class StockNotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('variant.size')
                    ->label('Beden/Varyant')
                    ->badge()
                    ->placeholder('Tüm Ürün')
                    ->color('gray'),

                TextColumn::make('current_stock')
                    ->label('Mevcut Stok')
                    ->state(function (StockNotification $record): string {
                        if ($record->variant) {
                            return (string) $record->variant->stock;
                        }
                        return (string) ($record->product?->stock ?? 0);
                    })
                    ->badge()
                    ->color(fn (string $state): string => (int) $state > 0 ? 'success' : 'danger'),

                TextColumn::make('email')
                    ->label('E-Posta')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->placeholder('—'),

                IconColumn::make('is_notified')
                    ->label('Bildirildi')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Talep Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('notified_at')
                    ->label('Bildirilme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_notified')
                    ->label('Bildirim Durumu')
                    ->trueLabel('Bildirildi')
                    ->falseLabel('Bekliyor'),

                SelectFilter::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->actions([
                Action::make('send_notification')
                    ->label('Bildirimi Gönder')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Stok Bildirimi Gönder')
                    ->modalDescription('Müşteriye e-posta ve varsa SMS bildirimi iletilecektir. Devam etmek istiyor musunuz?')
                    ->action(function (StockNotification $record) {
                        $success = StockNotificationService::notifySingle($record);
                        if ($success) {
                            Notification::make()
                                ->title('Bildirim Gönderildi')
                                ->body("{$record->email} adresine stok bildirimi iletildi.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Hata')
                                ->body('Bildirim gönderilirken bir sorun oluştu.')
                                ->danger()
                                ->send();
                        }
                    }),

                DeleteAction::make()
                    ->label('Sil'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('send_bulk_notification')
                        ->label('Seçilenlere Bildirim Gönder')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Toplu Stok Bildirimi')
                        ->modalDescription('Seçili tüm müşterilere stok bildirimleri gönderilecek.')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (StockNotificationService::notifySingle($record)) {
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title('Toplu Bildirim Tamamlandı')
                                ->body("{$count} müşteriye stok bildirimi başarıyla iletildi.")
                                ->success()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Order;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('customer_name')
                    ->label('Müşteri')
                    ->searchable()
                    ->description(fn (Order $record) => $record->user ? 'Hesap: ' . $record->user->name : 'Misafir')
                    ->limit(25),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
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
                    })
                    ->action(
                        Action::make('updateStatus')
                            ->modalHeading('Durumu Güncelle')
                            ->modalSubmitActionLabel('Kaydet')
                            ->modalCancelActionLabel('Vazgeç')
                            ->form([
                                \Filament\Forms\Components\Select::make('status')
                                    ->label('Durum')
                                    ->options([
                                        'pending' => 'Beklemede',
                                        'processing' => 'Hazırlanıyor',
                                        'shipped' => 'Kargoda',
                                        'delivered' => 'Teslim Edildi',
                                        'cancelled' => 'İptal',
                                    ])
                                    ->placeholder('Seçiniz')
                                    ->default(fn (Order $record) => $record->status)
                                    ->required(),
                            ])
                            ->action(function (Order $record, array $data): void {
                                $record->update(['status' => $data['status']]);
                            })
                    ),
                TextColumn::make('payment_status')
                    ->label('Ödeme')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'refunded' => 'info',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Beklemede',
                        'paid' => 'Ödendi',
                        'refunded' => 'İade',
                        'failed' => 'Başarısız',
                        default => $state,
                    })
                    ->action(
                        Action::make('updatePaymentStatus')
                            ->modalHeading('Ödeme Durumunu Güncelle')
                            ->modalSubmitActionLabel('Kaydet')
                            ->modalCancelActionLabel('Vazgeç')
                            ->form([
                                \Filament\Forms\Components\Select::make('payment_status')
                                    ->label('Ödeme Durumu')
                                    ->options([
                                        'pending' => 'Beklemede',
                                        'paid' => 'Ödendi',
                                        'refunded' => 'İade',
                                        'failed' => 'Başarısız',
                                    ])
                                    ->placeholder('Seçiniz')
                                    ->default(fn (Order $record) => $record->payment_status)
                                    ->required(),
                            ])
                            ->action(function (Order $record, array $data): void {
                                $record->update(['payment_status' => $data['payment_status']]);
                            })
                    ),
                TextColumn::make('grand_total')
                    ->label('Toplam')
                    ->getStateUsing(fn ($record) => number_format($record->grand_total, 2) . ' ₺')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('cargo_company')
                    ->label('Kargo')
                    ->badge()
                    ->color('gray')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cargo_tracking_code')
                    ->label('Takip Kodu')
                    ->copyable()
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_method')
                    ->label('Ödeme Yöntemi')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'credit_card' => 'Kredi Kartı',
                        'wire_transfer' => 'Havale/EFT',
                        'cash_on_delivery' => 'Kapıda Ödeme',
                        default => $state ?? '-',
                    }),
                TextColumn::make('shipping_city')
                    ->label('Şehir')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('items_count')
                    ->label('Kalem')
                    ->counts('items')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Sipariş Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'processing' => 'Hazırlanıyor',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Ödeme Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'paid' => 'Ödendi',
                        'refunded' => 'İade',
                        'failed' => 'Başarısız',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Ödeme Yöntemi')
                    ->options([
                        'credit_card' => 'Kredi Kartı',
                        'wire_transfer' => 'Havale/EFT',
                        'cash_on_delivery' => 'Kapıda Ödeme',
                    ]),
            ])
            ->actions([
                Action::make('createInvoice')
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('E-Fatura Kes (Porego)')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Porego (QNB) E-Fatura Kes')
                    ->modalDescription('Bu işlem siparişi Porego QNB Entegrasyonu üzerinden e-fatura/e-arşiv olarak resmileştirecektir. Onaylıyor musunuz?')
                    ->modalSubmitActionLabel('Evet, Fatura Kes')
                    ->action(function (Order $record): void {
                        try {
                            $service = app(\App\Services\PoregoApiService::class);
                            $result = $service->createInvoice($record);
                            
                            if ($result['success']) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Fatura Başarıyla Kesildi')
                                    ->body($result['message'])
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Fatura Kesilemedi')
                                    ->body($result['message'])
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Sistem Hatası')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Order $record): bool => !$record->is_invoiced),
                EditAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Düzenle'),
                Action::make('addCargo')
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Kargo Gir')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\Select::make('cargo_company')
                            ->label('Kargo Firması')
                            ->options([
                                'yurtici' => 'Yurtiçi Kargo',
                                'aras' => 'Aras Kargo',
                                'mng' => 'MNG Kargo',
                                'surat' => 'Sürat Kargo',
                                'ptt' => 'PTT Kargo',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('cargo_tracking_code')
                            ->label('Takip Kodu')
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update([
                            'cargo_company' => $data['cargo_company'],
                            'cargo_tracking_code' => $data['cargo_tracking_code'],
                            'status' => 'shipped',
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Kargo bilgisi eklendi ve müşteri uyarıldı.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'processing'])),

                Action::make('cancelOrder')
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('İptal Et')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Siparişi İptal Et')
                    ->modalDescription('Siparişi iptal etmek istediğinize emin misiniz? Müşteriye bildirim gidecek ve stoklar geri yüklenecektir.')
                    ->modalSubmitActionLabel('Evet, İptal Et')
                    ->modalCancelActionLabel('Vazgeç')
                    ->action(function (Order $record): void {
                        $record->update(['status' => 'cancelled']);
                        
                        // Stokları geri yükle
                        foreach ($record->items as $item) {
                            if ($item->variant) {
                                $variant = clone $item->variant;
                                $variant->increment('stock', $item->quantity);
                            }
                            
                            $product = clone $item->product;
                            if ($product) {
                                $product->increment('stock', $item->quantity);
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Sipariş iptal edildi ve stoklar güncellendi.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'processing'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

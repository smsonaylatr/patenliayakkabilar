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
                        'bank_transfer' => 'Havale/EFT',
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
                        'bank_transfer' => 'Havale/EFT',
                        'cash_on_delivery' => 'Kapıda Ödeme',
                    ]),
            ])
            ->actions([
                Action::make('createInvoice')
                    ->label('Fatura Kes (Paraşüt)')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Paraşüt E-Fatura Kes ve Gönder')
                    ->modalDescription('Bu işlem Paraşüt üzerinde otomatik bir cari oluşturacak, satışı e-fatura/e-arşiv olarak resmileştirecek ve faturayı e-posta ile müşteriye gönderecektir. Onaylıyor musunuz?')
                    ->modalSubmitActionLabel('Evet, Kes ve Gönder')
                    ->action(function (Order $record): void {
                        try {
                            $service = app(\App\Services\ParasutInvoiceService::class);
                            $pdfUrl = $service->createInvoiceFromOrder($record);
                            
                            // Configure SMTP dynamically from settings
                            $settings = \App\Models\Setting::whereIn('key', [
                                'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password'
                            ])->pluck('value', 'key')->toArray();
                            
                            if (!empty($settings['smtp_host'])) {
                                config([
                                    'mail.mailers.smtp.host' => $settings['smtp_host'],
                                    'mail.mailers.smtp.port' => $settings['smtp_port'] ?? 587,
                                    'mail.mailers.smtp.username' => $settings['smtp_username'] ?? '',
                                    'mail.mailers.smtp.password' => $settings['smtp_password'] ?? '',
                                ]);
                            }
                            
                            // Send custom email
                            \Illuminate\Support\Facades\Mail::to($record->customer_email)
                                ->send(new \App\Mail\OrderInvoiceMail($record, $pdfUrl));
                                
                            $record->update(['status' => 'completed']);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Fatura Başarıyla Kesildi ve Gönderildi')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Fatura Kesilemedi')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make()->label('Düzenle'),
                Action::make('addCargo')
                    ->label('Kargo Gir')
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
                    ->label('İptal Et')
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

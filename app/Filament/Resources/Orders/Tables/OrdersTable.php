<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('customer_name')
                        ->label('Müşteri')
                        ->searchable()
                        ->sortable()
                        ->weight('bold')
                        ->description(fn (Order $record) => $record->customer_email ?: ($record->user?->email ?: '-'))
                        ->grow(true),

                    TextColumn::make('shipping_city')
                        ->label('Şehir')
                        ->getStateUsing(fn (Order $record) => $record->shipping_city ?: ($record->billing_city ?: 'İstanbul'))
                        ->grow(false),

                    TextColumn::make('grand_total')
                        ->label('Tutar')
                        ->getStateUsing(fn ($record) => '₺' . number_format($record->grand_total, 0, ',', '.'))
                        ->weight('bold')
                        ->grow(false),

                    TextColumn::make('payment_method')
                        ->label('Ödeme')
                        ->badge()
                        ->color(fn (?string $state) => match ($state) {
                            'credit_card' => 'info',
                            'wire_transfer' => 'success',
                            'cash_on_delivery' => 'warning',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (?string $state) => match ($state) {
                            'credit_card' => 'Kredi Kartı',
                            'wire_transfer' => 'Havale/EFT',
                            'cash_on_delivery' => 'Kapıda',
                            default => $state ?: '-',
                        })
                        ->grow(false),

                    TextColumn::make('created_at')
                        ->label('Tarih')
                        ->dateTime('d.m.Y H:i')
                        ->sortable()
                        ->grow(false),

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
                        ->grow(false)
                        ->action(
                            Action::make('updateStatus')
                                ->modalHeading('Durumu Güncelle')
                                ->modalSubmitActionLabel('Kaydet')
                                ->modalCancelActionLabel('Vazgeç')
                                ->form([
                                    Select::make('status')
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
                                        ->native(false)
                                        ->required(),
                                ])
                                ->action(function (Order $record, array $data): void {
                                    $record->update(['status' => $data['status']]);
                                    \Filament\Notifications\Notification::make()
                                        ->title('Sipariş durumu güncellendi')
                                        ->success()
                                        ->send();
                                })
                        ),

                    TextColumn::make('payment_status')
                        ->label('Ödeme Durumu')
                        ->badge()
                        ->color(fn (?string $state) => match ($state) {
                            'paid' => 'success',
                            'unpaid' => 'danger',
                            'pending' => 'warning',
                            'refunded' => 'gray',
                            default => 'success',
                        })
                        ->formatStateUsing(fn (?string $state) => match ($state) {
                            'paid' => 'Ödendi',
                            'unpaid' => 'Ödenmedi',
                            'pending' => 'Bekliyor',
                            'refunded' => 'İade',
                            default => 'Ödendi',
                        })
                        ->grow(false),
                ])->from('md'),

                Panel::make([
                    \Filament\Tables\Columns\ViewColumn::make('details')
                        ->view('filament.orders.order-details-accordion'),
                ])->collapsible()->collapsed(),
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
                    ])
                    ->native(false),
                SelectFilter::make('payment_status')
                    ->label('Ödeme Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'paid' => 'Ödendi',
                        'refunded' => 'İade',
                        'failed' => 'Başarısız',
                    ])
                    ->native(false),
                SelectFilter::make('payment_method')
                    ->label('Ödeme Yöntemi')
                    ->options([
                        'credit_card' => 'Kredi Kartı',
                        'wire_transfer' => 'Havale/EFT',
                        'cash_on_delivery' => 'Kapıda Ödeme',
                    ])
                    ->native(false),
            ])
            ->actions([
                Action::make('createGibInvoice')
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('GİB E-Arşiv Fatura Kes')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->modalHeading('GİB E-Arşiv Faturası Kes (Portal)')
                    ->modalDescription('Aşağıdaki fatura bilgilerini kontrol edip faturayı GİB Portalına taslak olarak oluşturabilirsiniz.')
                    ->modalSubmitActionLabel('Faturayı Kes ve Kaydet')
                    ->modalCancelActionLabel('İptal')
                    ->form([
                        TextInput::make('customer_name')
                            ->label('Müşteri Ad Soyad')
                            ->default(fn (Order $record) => $record->customer_name)
                            ->required(),
                        TextInput::make('tax_number')
                            ->label('TCKN / VKN (11 haneli şahıs / 10 haneli kurumsal)')
                            ->default(fn (Order $record) => $record->tax_number ?: '11111111111')
                            ->required(),
                        TextInput::make('company_name')
                            ->label('Firma Unvanı (Kurumsal ise)')
                            ->default(fn (Order $record) => $record->company_name),
                        TextInput::make('tax_office')
                            ->label('Vergi Dairesi')
                            ->default(fn (Order $record) => $record->tax_office),
                        Select::make('kdv_rate')
                            ->label('KDV Oranı (%)')
                            ->options([
                                '20' => '%20 (Standart)',
                                '10' => '%10 (İndirimli)',
                                '0'  => '%0 (İstisna)',
                            ])
                            ->default('20')
                            ->native(false)
                            ->required(),
                        TextInput::make('invoice_note')
                            ->label('Fatura Notu')
                            ->default(fn (Order $record) => 'Sipariş No: #' . $record->order_number),
                    ])
                    ->action(function (Order $record, array $data): void {
                        try {
                            $service = app(\App\Services\GibEArsivService::class);
                            $result = $service->createInvoice($record, $data);
                            
                            if ($result['success']) {
                                \Filament\Notifications\Notification::make()
                                    ->title('GİB Faturası Başarıyla Oluşturuldu')
                                    ->body($result['message'])
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('GİB Fatura Hatası')
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

                Action::make('viewGibInvoice')
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('GİB Faturasını İncele / Yazdır')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
                    ->url(fn (Order $record): string => route('orders.gib-invoice', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Order $record): bool => (bool) $record->is_invoiced || !empty($record->gib_invoice_uuid)),

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
                        Select::make('cargo_company')
                            ->label('Kargo Firması')
                            ->options([
                                'yurtici' => 'Yurtiçi Kargo',
                                'aras' => 'Aras Kargo',
                                'mng' => 'MNG Kargo',
                                'surat' => 'Sürat Kargo',
                                'ptt' => 'PTT Kargo',
                            ])
                            ->native(false)
                            ->required(),
                        TextInput::make('cargo_tracking_code')
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
                            ->title('Kargo bilgisi eklendi ve sipariş kargoya verildi.')
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
                    BulkAction::make('bulkCreateGibInvoice')
                        ->label('Toplu GİB E-Arşiv Faturası Kes')
                        ->icon('heroicon-o-document-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Seçilen Siparişlere GİB Faturası Kes')
                        ->modalDescription('Seçtiğiniz siparişler için GİB E-Arşiv portalında sırayla taslak faturalar oluşturulacaktır. Onaylıyor musunuz?')
                        ->modalSubmitActionLabel('Evet, Faturaları Kes')
                        ->action(function (Collection $records) {
                            $successCount = 0;
                            $failCount = 0;
                            $service = app(\App\Services\GibEArsivService::class);

                            foreach ($records as $order) {
                                if ($order->is_invoiced) {
                                    continue;
                                }
                                $res = $service->createInvoice($order);
                                if ($res['success']) {
                                    $successCount++;
                                } else {
                                    $failCount++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Toplu Fatura İşlemi Tamamlandı')
                                ->body("{$successCount} adet sipariş faturası kesildi. {$failCount} adet sipariş başarısız oldu.")
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('exportSelectedOfflineConversions')
                        ->label('Google Çevrimdışı Dönüşüm CSV İndir (Seçilenler)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Seçilen Siparişleri CSV Olarak Aktar')
                        ->modalDescription('Seçtiğiniz siparişler Google Çevrimdışı Dönüşüm (Google Ads) formatında indirilecek ve dönüşüm aktarıldı olarak işaretlenecektir.')
                        ->modalSubmitActionLabel('CSV İndir')
                        ->action(function (Collection $records) {
                            if ($records->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Seçili sipariş bulunamadı')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $csvData = [];
                            $csvData[] = ['Parameters:TimeZone', 'Google Click ID', 'Conversion Name', 'Conversion Time', 'Conversion Value', 'Conversion Currency'];
                            
                            foreach ($records as $order) {
                                $csvData[] = [
                                    'Europe/Istanbul',
                                    $order->gclid ?? ('OFFLINE_' . $order->order_number),
                                    'Teslim Edilen Sipariş',
                                    $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
                                    $order->grand_total,
                                    'TRY'
                                ];
                            }

                            Order::whereIn('id', $records->pluck('id'))->update(['is_offline_conversion_exported' => true]);

                            $callback = function() use ($csvData) {
                                $file = fopen('php://output', 'w');
                                foreach ($csvData as $row) {
                                    fputcsv($file, $row);
                                }
                                fclose($file);
                            };

                            return response()->streamDownload($callback, 'google_ads_selected_conversions_' . date('Y-m-d') . '.csv', [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="google_ads_selected_conversions_' . date('Y-m-d') . '.csv"',
                            ]);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}

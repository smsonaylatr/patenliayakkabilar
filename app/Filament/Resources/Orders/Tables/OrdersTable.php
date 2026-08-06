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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                    TextColumn::make('order_number')
                        ->label('#SİPARİŞ')
                        ->searchable()
                        ->getStateUsing(fn ($record) => '#' . $record->order_number)
                        ->weight('bold')
                        ->copyable()
                        ->extraAttributes(['style' => 'width: 140px; flex: 0 0 140px;']),
                    TextColumn::make('customer_name')
                        ->label('MÜŞTERİ')
                        ->searchable()
                        ->weight('bold')
                        ->description(fn (Order $record) => $record->customer_email ?: ($record->user?->email ?: 'misafir@mail.com'))
                        ->limit(25)
                        ->extraAttributes(['style' => 'width: 250px; flex: 0 0 250px;']),
                    TextColumn::make('items_count')
                        ->label('ÜRÜNLER')
                        ->counts('items')
                        ->formatStateUsing(fn ($state) => ($state ?: 1) . ' ürün')
                        ->extraAttributes(['style' => 'width: 100px; flex: 0 0 100px; text-align: center; justify-content: center;']),
                    TextColumn::make('grand_total')
                        ->label('TUTAR')
                        ->getStateUsing(fn ($record) => '₺' . number_format($record->grand_total, 0, ',', '.'))
                        ->weight('bold')
                        ->extraAttributes(['style' => 'width: 110px; flex: 0 0 110px; text-align: right; justify-content: flex-end;']),
                    TextColumn::make('payment_method')
                        ->label('ÖDEME')
                        ->formatStateUsing(fn (?string $state) => match ($state) {
                            'credit_card' => 'Kredi Kartı',
                            'wire_transfer' => 'Havale/EFT',
                            'cash_on_delivery' => 'Kapıda Ödeme',
                            default => $state ?: 'Kredi Kartı',
                        })
                        ->extraAttributes(['style' => 'width: 120px; flex: 0 0 120px; text-align: center; justify-content: center;']),
                    TextColumn::make('payment_status')
                        ->label('ÖDEME DURUMU')
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
                            'refunded' => 'İade Edildi',
                            default => 'Ödendi',
                        })
                        ->extraAttributes(['style' => 'width: 110px; flex: 0 0 110px; text-align: center; justify-content: center;']),
                    TextColumn::make('created_at')
                        ->label('TARİH')
                        ->dateTime('d M Y')
                        ->extraAttributes(['style' => 'width: 130px; flex: 0 0 130px; text-align: center; justify-content: center;']),
                    TextColumn::make('status')
                        ->label('DURUM')
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
                ])
                ->grow(true)
                ->extraAttributes([
                    'class' => 'cursor-pointer select-none w-full flex-1 justify-between',
                    'style' => 'flex: 1 1 auto !important; width: 100% !important; justify-content: space-between !important;',
                    'x-on:click' => '$event.target.closest("button, select, a, input, form") ? null : $el.closest("tr, .fi-ta-row, div")?.querySelector(".fi-ta-collapsible-trigger, button[x-on\\\\:click], [x-on\\\\:click*=\'isCollapsed\']")?.click()',
                ]),
                \Filament\Tables\Columns\Layout\Panel::make([
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
            ->headerActions([
                Action::make('exportOfflineConversions')
                    ->label('Google Çevrimdışı Dönüşüm CSV İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->modalHeading('Google Çevrimdışı Dönüşüm CSV Dışa Aktar')
                    ->modalDescription('Filtrelere uyan siparişler Google Ads CSV formatında indirilecek ve aktarıldı olarak işaretlenecektir.')
                    ->modalSubmitActionLabel('CSV İndir')
                    ->form([
                        Select::make('status_filter')
                            ->label('Sipariş Durumu')
                            ->options([
                                'delivered' => 'Sadece Teslim Edilenler (Teslim Edildi)',
                                'delivered_processing' => 'Teslim Edilenler & Hazırlananlar',
                                'all' => 'Tüm Durumlar (Tüm Siparişler)',
                            ])
                            ->default('delivered')
                            ->native(false)
                            ->required(),
                        Select::make('payment_filter')
                            ->label('Ödeme Yöntemi')
                            ->options([
                                'cod_wire' => 'Kapıda Ödeme & Havale / EFT',
                                'all' => 'Tüm Ödeme Yöntemleri (Kredi Kartı Dahil)',
                            ])
                            ->default('cod_wire')
                            ->native(false)
                            ->required(),
                        Toggle::make('only_new')
                            ->label('Sadece daha önce aktarılmayan siparişleri getir')
                            ->default(true),
                        Toggle::make('only_with_gclid')
                            ->label('Sadece Google Click ID (GCLID) içerenleri getir')
                            ->default(true),
                    ])
                    ->action(function (array $data) {
                        $query = Order::query();

                        if ($data['status_filter'] === 'delivered') {
                            $query->where('status', 'delivered');
                        } elseif ($data['status_filter'] === 'delivered_processing') {
                            $query->whereIn('status', ['delivered', 'processing']);
                        }

                        if ($data['payment_filter'] === 'cod_wire') {
                            $query->whereIn('payment_method', ['cash_on_delivery', 'wire_transfer']);
                        }

                        if (!empty($data['only_new'])) {
                            $query->where('is_offline_conversion_exported', false);
                        }

                        if (!empty($data['only_with_gclid'])) {
                            $query->whereNotNull('gclid');
                        }

                        $orders = $query->get();

                        if ($orders->isEmpty()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Aktarılacak sipariş bulunamadı')
                                ->body('Seçilen filtrelere uygun sipariş kaydı bulunamadı. Sipariş durumunu/filtreleri değiştirebilir veya tablodan sipariş seçip "Toplu İşlemler -> Seçilenler" seçeneğiyle indirebilirsiniz.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $csvData = [];
                        $csvData[] = ['Parameters:TimeZone', 'Google Click ID', 'Conversion Name', 'Conversion Time', 'Conversion Value', 'Conversion Currency'];
                        
                        foreach ($orders as $order) {
                            $csvData[] = [
                                'Europe/Istanbul',
                                $order->gclid ?? ('OFFLINE_' . $order->order_number),
                                'Teslim Edilen Sipariş',
                                $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
                                $order->grand_total,
                                'TRY'
                            ];
                        }

                        Order::whereIn('id', $orders->pluck('id'))->update(['is_offline_conversion_exported' => true]);

                        $callback = function() use ($csvData) {
                            $file = fopen('php://output', 'w');
                            foreach ($csvData as $row) {
                                fputcsv($file, $row);
                            }
                            fclose($file);
                        };

                        return response()->streamDownload($callback, 'google_ads_offline_conversions_' . date('Y-m-d') . '.csv', [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => 'attachment; filename="google_ads_offline_conversions_' . date('Y-m-d') . '.csv"',
                        ]);
                    })
            ])
            ->defaultSort('created_at', 'desc');
    }
}

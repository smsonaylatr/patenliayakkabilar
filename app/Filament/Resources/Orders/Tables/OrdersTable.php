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
use Filament\Tables\Columns\ImageColumn;
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
            ->columns([
                ImageColumn::make('product_image')
                    ->label('')
                    ->getStateUsing(function (Order $record) {
                        $firstItem = $record->items->first();
                        if ($firstItem && $firstItem->product) {
                            $firstItem->product->loadMissing('images');
                            $img = $firstItem->product->images->first()?->image_url;
                            if ($img) return $img;
                        }
                        return asset('favicon.png');
                    })
                    ->square()
                    ->size(60)
                    ->extraImgAttributes([
                        'style' => 'border: 1px solid rgba(255, 255, 255, 0.18) !important; border-radius: 8px !important; object-fit: cover !important; transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease !important; cursor: zoom-in !important; transform-origin: center left !important; position: relative !important; z-index: 10 !important;',
                        'class' => 'hover:scale-[2.6] hover:z-[9999] hover:shadow-2xl hover:rounded-xl hover:border-2 hover:border-sky-400',
                    ]),

                TextColumn::make('customer_name')
                    ->label('MÜŞTERİ')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Order $record) => $record->customer_email ?: ($record->user?->email ?: '-'))
                    ->limit(30),

                TextColumn::make('shipping_city')
                    ->label('ŞEHİR')
                    ->sortable()
                    ->getStateUsing(fn (Order $record) => $record->shipping_city ?: ($record->billing_city ?: 'İstanbul')),

                TextColumn::make('grand_total')
                    ->label('TUTAR')
                    ->sortable()
                    ->weight('bold')
                    ->getStateUsing(fn ($record) => '₺' . number_format($record->grand_total, 0, ',', '.')),

                TextColumn::make('payment_method')
                    ->label('ÖDEME')
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
                        'cash_on_delivery' => 'Kapıda Ödeme',
                        default => $state ?: '-',
                    }),

                TextColumn::make('created_at')
                    ->label('TARİH')
                    ->sortable()
                    ->dateTime('d.m.Y H:i'),

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

                TextColumn::make('payment_status')
                    ->label('ÖDEME DURUMU')
                    ->badge()
                    ->color(fn (?string $state, Order $record) => match ($state) {
                        'paid' => 'success',
                        'unpaid', 'failed' => 'danger',
                        'pending' => 'warning',
                        'refunded' => 'gray',
                        default => $record->payment_method === 'cash_on_delivery' ? 'warning' : 'danger',
                    })
                    ->formatStateUsing(fn (?string $state, Order $record) => match ($state) {
                        'paid' => 'Ödendi',
                        'unpaid' => 'Ödenmedi',
                        'failed' => 'Başarısız',
                        'pending' => 'Bekliyor',
                        'refunded' => 'İade Edildi',
                        default => $record->payment_method === 'cash_on_delivery' ? 'Bekliyor' : 'Ödenmedi',
                    })
                    ->action(
                        Action::make('updatePaymentStatus')
                            ->modalHeading('Ödeme Durumunu Güncelle')
                            ->modalSubmitActionLabel('Kaydet')
                            ->modalCancelActionLabel('Vazgeç')
                            ->form([
                                Select::make('payment_status')
                                    ->label('Ödeme Durumu')
                                    ->options([
                                        'pending' => 'Bekliyor',
                                        'paid' => 'Ödendi',
                                        'unpaid' => 'Ödenmedi',
                                        'failed' => 'Başarısız',
                                        'refunded' => 'İade Edildi',
                                    ])
                                    ->default(fn (Order $record) => $record->payment_status ?: 'pending')
                                    ->native(false)
                                    ->required(),
                            ])
                            ->action(function (Order $record, array $data): void {
                                $record->update(['payment_status' => $data['payment_status']]);
                                \Filament\Notifications\Notification::make()
                                    ->title('Ödeme durumu güncellendi')
                                    ->success()
                                    ->send();
                            })
                    ),
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
                Action::make('viewDetails')
                    ->extraAttributes(['style' => 'display: none !important;'])
                    ->modalHeading(fn (Order $record) => 'Sipariş Detayı #' . $record->order_number)
                    ->modalWidth('5xl')
                    ->modalContent(fn (Order $record) => view('filament.orders.order-details-accordion', ['getRecord' => fn () => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat'),

                Action::make('manageGibInvoice')
                    ->iconButton()
                    ->size('lg')
                    ->tooltip(fn (Order $record) => $record->gib_invoice_status === 'signed' ? 'Faturayı Görüntüle' : 'GİB Faturası İşlemleri')
                    ->icon(fn (Order $record) => match(true) {
                        $record->gib_invoice_status === 'signed' => 'heroicon-o-document-check',
                        $record->is_invoiced => 'heroicon-o-document-text',
                        default => 'heroicon-o-document-plus',
                    })
                    ->color(fn (Order $record) => match(true) {
                        $record->gib_invoice_status === 'signed' => 'success',
                        $record->is_invoiced => 'warning',
                        default => 'primary',
                    })
                    ->modalHeading('GİB E-Arşiv Faturası')
                    ->modalWidth('4xl')
                    ->modalCancelAction(false)
                    ->modalSubmitActionLabel(fn (Order $record) => $record->gib_invoice_status === 'signed' ? 'Kapat' : 'İmzala')
                    ->form([
                        \Filament\Forms\Components\Placeholder::make('html_preview')
                            ->hiddenLabel()
                            ->content(function (Order $record) {
                                if (!$record->gib_invoice_html) {
                                    return new \Illuminate\Support\HtmlString('<i>Fatura içeriği yüklenemedi.</i>');
                                }
                                $html = htmlspecialchars($record->gib_invoice_html, ENT_QUOTES, 'UTF-8');
                                return new \Illuminate\Support\HtmlString('
                                    <div style="background-color: #525659; padding: 1rem; border-radius: 0.5rem; display: flex; justify-content: center; overflow: hidden; height: 75vh;">
                                        <div style="width: 100%; position: relative; overflow: hidden;">
                                            <iframe srcdoc="' . $html . '" style="position: absolute; top: 0; left: 0; width: 160%; height: 160%; transform: scale(0.625); transform-origin: 0 0; border: none; background: #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);"></iframe>
                                        </div>
                                    </div>
                                ');
                            }),
                        \Filament\Forms\Components\Hidden::make('operation_id'),
                        \Filament\Forms\Components\TextInput::make('sms_code')
                            ->label('SMS Şifresi')
                            ->required(fn (Order $record) => $record->gib_invoice_status !== 'signed')
                            ->placeholder('Telefonunuza gelen SMS şifresini girin')
                            ->hidden(fn (Order $record) => $record->gib_invoice_status === 'signed'),
                    ])
                    ->mountUsing(function (\Filament\Schemas\Schema $form, Order $record) {
                        try {
                            $service = app(\App\Services\GibEArsivService::class);
                            $shouldStartSms = false;

                            if (!$record->is_invoiced) {
                                $result = $service->createInvoice($record, []);
                                if (!$result['success']) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Fatura Kesilemedi')
                                        ->body($result['message'])
                                        ->danger()
                                        ->send();
                                    throw new \Filament\Support\Exceptions\Halt();
                                }
                                $record->refresh();
                                $shouldStartSms = true;
                            } elseif ($record->gib_invoice_status === 'draft') {
                                // Otomatik senkronizasyon: Açarken GİB'den durum kontrolü yap
                                $gib = $service->getGibClient();
                                $startDate = $record->gib_invoice_date ? $record->gib_invoice_date->copy()->subDays(2)->format('d/m/Y') : date('d/m/Y', strtotime('-2 days'));
                                $endDate = $record->gib_invoice_date ? $record->gib_invoice_date->copy()->addDays(2)->format('d/m/Y') : date('d/m/Y', strtotime('+2 days'));
                                
                                $docs = $gib->findEttn($record->gib_invoice_uuid)->getAll($startDate, $endDate);
                                if (!empty($docs)) {
                                    $doc = reset($docs); // array key 0 hatasını önlemek için reset()
                                    if (str_contains($doc['onayDurumu'] ?? '', 'Onayland')) {
                                        $newHtml = $service->getInvoiceHtml($record->gib_invoice_uuid);
                                        $record->update([
                                            'gib_invoice_status' => 'signed',
                                            'gib_invoice_html' => $newHtml,
                                        ]);
                                        
                                        // Formu kapatıp yeniden açmalarına gerek kalmaması için html'i güncelleyelim.
                                        $shouldStartSms = false;
                                        
                                        \Filament\Notifications\Notification::make()
                                            ->title('Fatura Zaten İmzalanmış')
                                            ->body('GİB üzerinden kontrol edildi, fatura daha önce imzalandığı için otomatik güncellendi.')
                                            ->success()
                                            ->send();
                                    } else {
                                        $shouldStartSms = true;
                                    }
                                } else {
                                    $shouldStartSms = true;
                                }
                            }

                            if ($shouldStartSms) {
                                $smsResult = $service->startSmsVerification();
                                if ($smsResult['success']) {
                                    $form->fill([
                                        'operation_id' => $smsResult['operation_id'],
                                    ]);
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS Gönderilemedi')
                                        ->body($smsResult['message'])
                                        ->danger()
                                        ->send();
                                    throw new \Filament\Support\Exceptions\Halt();
                                }
                            }
                        } catch (\Exception $e) {
                            if (!($e instanceof \Filament\Support\Exceptions\Halt)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Sistem Hatası')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                            throw new \Filament\Support\Exceptions\Halt();
                        }
                    })
                    ->action(function (array $data, Order $record): void {
                        if ($record->gib_invoice_status === 'signed') {
                            return;
                        }

                        try {
                            $service = app(\App\Services\GibEArsivService::class);
                            $result = $service->completeSmsVerification($data['sms_code'], $data['operation_id'], [$record->gib_invoice_uuid]);
                            
                            if ($result['success']) {
                                $newHtml = $service->getInvoiceHtml($record->gib_invoice_uuid);
                                $record->update([
                                    'gib_invoice_status' => 'signed',
                                    'gib_invoice_html' => $newHtml,
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Fatura Başarıyla İmzalandı')
                                    ->body($result['message'])
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('İmzalama Hatası')
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
                    }),

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
                        
                        \App\Models\OrderStatusHistory::create([
                            'order_id' => $record->id,
                            'status' => 'shipped',
                            'note' => 'Kargo bilgileri girildi.',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Kargo bilgisi eklendi ve sipariş kargoya verildi.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'processing'])),

                Action::make('sendToPorego')
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Porego\'ya Kargo Gönder')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Porego\'ya Kargo Gönder')
                    ->modalDescription('Sipariş bilgilerini ve net SKU listesini Porego Kargo sistemine aktarır.')
                    ->modalSubmitActionLabel('Gönder')
                    ->action(function (Order $record): void {
                        $result = app(\App\Services\PoregoApiService::class)->sendOrder($record);
                        $isSuccess = is_array($result) ? ($result['success'] ?? false) : (bool)$result;
                        $message = is_array($result) ? ($result['message'] ?? '') : '';

                        if ($isSuccess) {
                            \Filament\Notifications\Notification::make()
                                ->title('Sipariş Porego\'ya başarıyla gönderildi')
                                ->body($message ?: 'Sipariş detayları Porego sistemine iletildi.')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Porego Gönderim Uyarısı')
                                ->body($message ?: 'Sipariş Porego\'ya iletilirken hata oluştu.')
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),

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
                    BulkAction::make('bulkSendToPorego')
                        ->label('Seçilenleri Porego\'ya Gönder')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Seçilen Siparişleri Porego\'ya Gönder')
                        ->modalDescription('Seçtiğiniz siparişler Porego Kargo sistemine ürün detaylarıyla birlikte aktarılacaktır.')
                        ->modalSubmitActionLabel('Gönder')
                        ->action(function (Collection $records) {
                            $successCount = 0;
                            $failCount = 0;
                            $service = app(\App\Services\PoregoApiService::class);

                            foreach ($records as $order) {
                                if ($service->sendOrder($order)) {
                                    $successCount++;
                                } else {
                                    $failCount++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Toplu Porego Gönderimi Tamamlandı')
                                ->body("{$successCount} adet sipariş Porego'ya iletildi. {$failCount} adet sipariş başarısız oldu.")
                                ->success()
                                ->send();
                        }),

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

                    BulkAction::make('bulkResetGibInvoice')
                        ->label('Seçilenlerin Fatura Durumunu Sıfırla')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Fatura Durumlarını Sıfırla')
                        ->modalDescription('GİB Portalından iptal ettiğiniz faturaları yeniden kesebilmek için sistemdeki kayıtlarını sıfırlamanız gerekir. Seçili siparişlerin mevcut fatura kayıtları silinerek yeniden fatura kesilebilir duruma getirilecektir. Onaylıyor musunuz?')
                        ->modalSubmitActionLabel('Evet, Sıfırla')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $order) {
                                if ($order->is_invoiced) {
                                    $order->update([
                                        'is_invoiced' => false,
                                        'gib_invoice_uuid' => null,
                                        'gib_invoice_html' => null,
                                        'gib_invoice_date' => null,
                                        'gib_invoice_status' => 'none',
                                        'gib_invoice_error' => null,
                                    ]);
                                    $count++;
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Fatura Durumları Sıfırlandı')
                                ->body("{$count} adet siparişin faturası sıfırlandı. Artık yeniden fatura kesebilirsiniz.")
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('syncGibInvoiceStatus')
                        ->label('Durumları Eşitle (GİB)')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('GİB Durumlarını Senkronize Et')
                        ->modalDescription('Seçilen taslak faturaların durumları GİB üzerinden kontrol edilecek ve eğer GİB üzerinde imzalanmışsa sisteme İmzalı olarak yansıtılacaktır.')
                        ->action(function (Collection $records): void {
                            $drafts = $records->filter(fn ($r) => $r->is_invoiced && $r->gib_invoice_status === 'draft');
                            if ($drafts->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('İşlem Yapılmadı')
                                    ->body('Seçili kayıtlarda kontrol edilecek taslak fatura bulunmuyor.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            try {
                                $service = app(\App\Services\GibEArsivService::class);
                                $gib = $service->getGibClient();
                                
                                $updatedCount = 0;
                                foreach ($drafts as $order) {
                                    $startDate = $order->gib_invoice_date ? $order->gib_invoice_date->copy()->subDays(2)->format('d/m/Y') : date('d/m/Y', strtotime('-2 days'));
                                    $endDate = $order->gib_invoice_date ? $order->gib_invoice_date->copy()->addDays(2)->format('d/m/Y') : date('d/m/Y', strtotime('+2 days'));

                                    $docs = $gib->findEttn($order->gib_invoice_uuid)->getAll($startDate, $endDate);
                                    
                                    if (!empty($docs)) {
                                        $doc = reset($docs);
                                        if (str_contains($doc['onayDurumu'] ?? '', 'Onayland')) {
                                            $newHtml = $service->getInvoiceHtml($order->gib_invoice_uuid);
                                            $order->update([
                                                'gib_invoice_status' => 'signed',
                                                'gib_invoice_html' => $newHtml,
                                            ]);
                                            $updatedCount++;
                                        }
                                    }
                                }

                                \Filament\Notifications\Notification::make()
                                    ->title('Senkronizasyon Tamamlandı')
                                    ->body("{$updatedCount} adet fatura GİB'den çekilerek imzalı duruma güncellendi.")
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Sistem Hatası')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    BulkAction::make('bulkSignGibInvoice')
                        ->label('Seçilenleri İmzala (SMS)')
                        ->icon('heroicon-o-check-badge')
                        ->color('primary')
                        ->modalHeading('GİB Faturalarını İmzala (SMS)')
                        ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Seçilen taslak faturaları imzalamak için telefonunuza bir SMS şifresi gönderildi.<br><br><b>ÖNEMLİ:</b> Şifrenin ulaşması 5-10 saniye sürebilir. Lütfen bekleyin ve gelen şifreyi girerek onaylayın.'))
                        ->modalSubmitActionLabel('İmzala')
                        ->form([
                            \Filament\Forms\Components\Hidden::make('operation_id'),
                            \Filament\Forms\Components\TextInput::make('sms_code')
                                ->label('SMS Şifresi')
                                ->required()
                                ->placeholder('Gelen şifreyi girin'),
                        ])
                        ->mountUsing(function (\Filament\Schemas\Schema $form, Collection $records) {
                            try {
                                $drafts = $records->filter(fn ($r) => $r->is_invoiced && $r->gib_invoice_status === 'draft');
                                if ($drafts->isEmpty()) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Uyarı')
                                        ->body('İmzalanacak taslak fatura bulunamadı.')
                                        ->warning()
                                        ->send();
                                    throw new \Filament\Support\Exceptions\Halt();
                                }
                                
                                $service = app(\App\Services\GibEArsivService::class);
                                $result = $service->startSmsVerification();
                                if ($result['success']) {
                                    $form->fill([
                                        'operation_id' => $result['operation_id'],
                                    ]);
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS Gönderilemedi')
                                        ->body($result['message'])
                                        ->danger()
                                        ->send();
                                    throw new \Filament\Support\Exceptions\Halt();
                                }
                            } catch (\Exception $e) {
                                if (!($e instanceof \Filament\Support\Exceptions\Halt)) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Sistem Hatası')
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                                throw new \Filament\Support\Exceptions\Halt();
                            }
                        })
                        ->action(function (array $data, Collection $records): void {
                            try {
                                $drafts = $records->filter(fn ($r) => $r->is_invoiced && $r->gib_invoice_status === 'draft');
                                $uuids = $drafts->pluck('gib_invoice_uuid')->toArray();
                                
                                if (empty($uuids)) return;

                                $service = app(\App\Services\GibEArsivService::class);
                                $result = $service->completeSmsVerification($data['sms_code'], $data['operation_id'], $uuids);
                                
                                if ($result['success']) {
                                    foreach ($drafts as $record) {
                                        try {
                                            $newHtml = $service->getInvoiceHtml($record->gib_invoice_uuid);
                                            $record->update([
                                                'gib_invoice_status' => 'signed',
                                                'gib_invoice_html' => $newHtml,
                                            ]);
                                        } catch (\Exception $ex) {
                                            $record->update(['gib_invoice_status' => 'signed']);
                                        }
                                    }

                                    \Filament\Notifications\Notification::make()
                                        ->title('Toplu İmzalama Başarılı')
                                        ->body($result['message'])
                                        ->success()
                                        ->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('İmzalama Hatası')
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
                        }),

                    BulkAction::make('exportSelectedOfflineConversions')
                        ->label('Google Çevrimdışı Dönüşüm CSV İndir (Seçilenler)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->form([
                            TextInput::make('conversion_name')
                                ->label('Google Ads Dönüşüm Eylemi Adı (Conversion Name)')
                                ->default('Satın Alma')
                                ->required()
                                ->placeholder('Örn: Satın Alma, Purchase, Sipariş')
                                ->helperText('Google Ads panelinizdeki (Hedefler > Dönüşümler > Özet) listenizde görünen Dönüşüm Adı ile harfi harfine BİREBİR aynı olmalıdır. Türkçe Google Ads hesaplarında genellikle "Satın Alma", İngilizce hesaplarda "Purchase"dir.'),
                        ])
                        ->modalHeading('Seçilen Siparişleri Google Ads CSV Olarak Aktar')
                        ->modalDescription('Google Ads panelinizdeki Dönüşüm Eylemi adını kontrol ediniz. CSV içindeki isim ile Google Ads hesabınızdaki isim uyuşmazsa Google "Dönüşüm işlemi bulunamıyor" hatası verir.')
                        ->modalSubmitActionLabel('CSV İndir')
                        ->action(function (Collection $records, array $data) {
                            if ($records->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Seçili sipariş bulunamadı')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $gclidRecords = $records->filter(fn (Order $order) => !empty(trim($order->gclid ?? '')));

                            if ($gclidRecords->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('GCLID Bulunamadı')
                                    ->body('Seçtiğiniz siparişlerin hiçbirinde Google Ads Tıklama Kimliği (GCLID) bulunmuyor. Google Ads, GCLID olmadan dönüşüm kabul etmez.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                return;
                            }

                            $conversionName = trim($data['conversion_name'] ?? 'Teslim Edilen Sipariş');
                            $csvData = [];
                            // Google Ads Çevrimdışı Dönüşüm CSV Formatı:
                            // Satır 1: Parameters:TimeZone=Europe/Istanbul
                            // Satır 2: Başlık Satırı (Google Click ID, Conversion Name, Conversion Time, Conversion Value, Conversion Currency)
                            // Satır 3+: Veri Satırları
                            $csvData[] = ['Parameters:TimeZone=Europe/Istanbul'];
                            $csvData[] = ['Google Click ID', 'Conversion Name', 'Conversion Time', 'Conversion Value', 'Conversion Currency'];
                            
                            foreach ($gclidRecords as $order) {
                                $csvData[] = [
                                    $order->gclid,
                                    $conversionName,
                                    $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
                                    number_format((float) $order->grand_total, 2, '.', ''),
                                    'TRY'
                                ];
                            }

                            Order::whereIn('id', $gclidRecords->pluck('id'))->update(['is_offline_conversion_exported' => true]);

                            $skippedCount = $records->count() - $gclidRecords->count();
                            if ($skippedCount > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Bazı Siparişler Atlandı')
                                    ->body("{$gclidRecords->count()} adet GCLID'li sipariş CSV'ye aktarıldı. GCLID'si bulunmayan {$skippedCount} adet sipariş atlandı.")
                                    ->warning()
                                    ->send();
                            }

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
                ])->label('Toplu İşlemler'),
            ])
            ->headerActions([])
            ->defaultSort('created_at', 'desc')
            ->recordAction('viewDetails')
            ->recordUrl(null)
            ->striped();
    }
}

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
use Filament\Tables\Columns\ViewColumn;
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
                ViewColumn::make('product_image')
                    ->label('')
                    ->width('110px')
                    ->view('filament.columns.order-product-images')
                    ->getStateUsing(function (Order $record) {
                        $items = [];
                        foreach ($record->items as $item) {
                            $img = null;
                            if ($item->product) {
                                $item->product->loadMissing('images');
                                $img = $item->product->images->first()?->image_url;
                            }
                            $items[] = [
                                'image' => $img ?: asset('favicon.png'),
                                'quantity' => $item->quantity ?? 1,
                            ];
                        }
                        return !empty($items) ? $items : [['image' => asset('favicon.png'), 'quantity' => 1]];
                    }),

                TextColumn::make('customer_name')
                    ->label('MÜŞTERİ')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(function (Order $record) {
                        $email = $record->customer_email ?: ($record->user?->email ?: '-');
                        if ($record->hasKickSpeedProducts()) {
                            $code = trim((string)$record->cargo_tracking_code);
                            $hasReal = !empty($code) && !str_starts_with($code, '33') && $code !== $record->order_number && $code !== (string)$record->id;
                            if (!$hasReal && !in_array($record->status, ['delivered', 'completed', 'cancelled'])) {
                                return '⚡ Kick Speed (Onay Bekliyor) • ' . $email;
                            }
                            return '⚡ Kick Speed • ' . $email;
                        }
                        return $email;
                    })
                    ->limit(35),

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
                        'return_started' => 'orange',
                        'returned' => 'pink',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Beklemede',
                        'processing' => 'Hazırlanıyor',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim Edildi',
                        'return_started' => 'İade Başlatıldı',
                        'returned' => 'İade',
                        'cancelled' => 'İptal',
                        default => $state,
                    })
                    ->action(
                        Action::make('updateStatus')
                            ->modalHeading('Durumu Güncelle')
                            ->modalSubmitActionLabel('Kaydet')
                            ->modalCancelActionLabel('Vazgeç')
                            ->modalWidth('lg')
                            ->form([
                                Select::make('status')
                                    ->label('Durum')
                                    ->options([
                                        'pending' => 'Beklemede',
                                        'processing' => 'Hazırlanıyor',
                                        'shipped' => 'Kargoda',
                                        'delivered' => 'Teslim Edildi',
                                        'return_started' => 'İade Süreci Başlatıldı',
                                        'returned' => 'İade',
                                        'cancelled' => 'İptal',
                                    ])
                                    ->placeholder('Seçiniz')
                                    ->default(fn (Order $record) => $record->status)
                                    ->native(false)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set, Order $record) {
                                        $smsTypeMap = [
                                            'shipped' => 'shipped',
                                            'delivered' => 'delivered',
                                            'return_started' => 'return_started',
                                            'returned' => 'returned',
                                            'cancelled' => 'cancelled',
                                        ];

                                        if (!isset($smsTypeMap[$state])) {
                                            $set('sms_send', false);
                                            $set('sms_draft', '');
                                            $set('sms_phone_display', '');
                                            return;
                                        }

                                        $phone = $record->customer_phone ?? '';
                                        $set('sms_phone_display', $phone ?: 'Telefon numarası yok');

                                        // SMS şablonunu al
                                        $templateKey = match ($state) {
                                            'shipped' => 'vatansms_shipped_message',
                                            'delivered' => 'vatansms_delivered_message',
                                            'return_started' => 'vatansms_return_started_message',
                                            'returned' => 'vatansms_returned_message',
                                            'cancelled' => 'vatansms_cancelled_message',
                                            default => null,
                                        };

                                        $template = $templateKey 
                                            ? \App\Models\Setting::where('key', $templateKey)->value('value') 
                                            : '';

                                        if (empty($template)) {
                                            $template = match ($state) {
                                                'shipped' => "Sayın {isim}, {siparis_no} numaralı siparişiniz kargoya verilmiştir. Kargo Takip No: {kargo_kodu} https://patenliayakkabilar.com/siparis-takip?order_number={siparis_no}",
                                                'delivered' => "Sayın {isim}, {siparis_no} numaralı siparişiniz teslim edilmiştir. Bizi tercih ettiğiniz için teşekkür ederiz. https://patenliayakkabilar.com",
                                                'return_started' => "Sayın {isim}, {siparis_no} numaralı siparişiniz için iade süreci başlatılmıştır. İade süreciniz hakkında sizi bilgilendireceğiz. - Patenli Ayakkabılar",
                                                'returned' => "Sayın {isim}, {siparis_no} numaralı siparişinizin iade işlemi tamamlanmıştır. İade tutarı en kısa sürede hesabınıza yansıtılacaktır. - Patenli Ayakkabılar",
                                                'cancelled' => "Sayın {isim}, {siparis_no} numaralı siparişiniz iptal edilmiştir. Sorularınız için bizimle iletişime geçebilirsiniz. - Patenli Ayakkabılar",
                                                default => '',
                                            };
                                        }

                                        // Kargo takip kodu
                                        $kargoKodu = trim((string) $record->cargo_tracking_code);
                                        if (empty($kargoKodu) || str_starts_with($kargoKodu, '33')) {
                                            $kargoKodu = '';
                                        }

                                        // Şablondaki değişkenleri doldur
                                        $message = str_replace(
                                            ['{isim}', '{siparis_no}', '{tutar}', '{kargo_kodu}'],
                                            [
                                                $record->customer_name,
                                                $record->order_number,
                                                number_format((float) $record->grand_total, 2) . ' TL',
                                                $kargoKodu,
                                            ],
                                            $template
                                        );

                                        $set('sms_draft', $message);
                                        $set('sms_send', !empty($phone));
                                    }),

                                \Filament\Schemas\Components\Section::make('📱 SMS Bildirimi')
                                    ->description(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('sms_phone_display') ? '📞 ' . $get('sms_phone_display') : '')
                                    ->schema([
                                        \Filament\Forms\Components\Toggle::make('sms_send')
                                            ->label('SMS Gönder')
                                            ->helperText('Kapatırsanız müşteriye SMS gönderilmez.')
                                            ->default(false)
                                            ->live(),

                                        \Filament\Forms\Components\Textarea::make('sms_draft')
                                            ->label('SMS Taslağı')
                                            ->rows(4)
                                            ->helperText('Mesajı düzenleyebilirsiniz.')
                                            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => (bool) $get('sms_send')),

                                        \Filament\Forms\Components\Hidden::make('sms_phone_display'),
                                    ])
                                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => in_array($get('status'), ['shipped', 'delivered', 'return_started', 'returned', 'cancelled']))
                                    ->collapsed(false),
                            ])
                            ->action(function (Order $record, array $data): void {
                                // Admin panelden statü değiştirildiğinde Porego senkronizasyonunu kilitle
                                $lockStatuses = ['returned', 'return_started', 'cancelled', 'pending', 'processing'];
                                $shouldLock = in_array($data['status'], $lockStatuses);
                                $record->update([
                                    'status' => $data['status'],
                                    'porego_sync_locked' => $shouldLock,
                                ]);

                                // SMS gönder (admin onayladıysa)
                                if (!empty($data['sms_send']) && !empty($data['sms_draft']) && !empty($record->customer_phone)) {
                                    try {
                                        $result = app(\App\Services\VatanSmsService::class)->send(
                                            $record->customer_phone,
                                            $data['sms_draft'],
                                            'turkce',
                                            'bilgi'
                                        );

                                        if ($result) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Sipariş durumu güncellendi + SMS gönderildi')
                                                ->body('📱 ' . $record->customer_phone . ' numarasına SMS başarıyla gönderildi.')
                                                ->success()
                                                ->send();
                                        } else {
                                            $error = app(\App\Services\VatanSmsService::class)->getLastError() ?? 'Bilinmeyen hata';
                                            \Filament\Notifications\Notification::make()
                                                ->title('Durum güncellendi ama SMS gönderilemedi')
                                                ->body("Hata: {$error}")
                                                ->warning()
                                                ->send();
                                        }
                                    } catch (\Throwable $e) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Durum güncellendi ama SMS hatası oluştu')
                                            ->body($e->getMessage())
                                            ->warning()
                                            ->send();
                                    }
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Sipariş durumu güncellendi')
                                        ->body(!empty($data['sms_send']) ? 'SMS gönderilmedi: Telefon numarası bulunamadı.' : 'SMS gönderilmedi (atlandı).')
                                        ->success()
                                        ->send();
                                }
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
                        'return_started' => 'İade Başlatıldı',
                        'returned' => 'İade',
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
                SelectFilter::make('invoice_type')
                    ->label('Fatura Tipi')
                    ->options([
                        'individual' => 'Bireysel',
                        'corporate' => 'Kurumsal',
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
                    ->visible(fn (Order $record) => $record->is_invoiced || $record->status === 'delivered')
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
                                
                                // Faturanın dış boşluklarını sıfırlayalım ve yatay kaydırmayı kapatalım
                                $style = '<style>body { margin: 10px !important; padding: 0 !important; overflow-x: hidden !important; }</style>';
                                $html = htmlspecialchars($style . $record->gib_invoice_html, ENT_QUOTES, 'UTF-8');
                                
                                return new \Illuminate\Support\HtmlString('
                                    <div style="display: flex; justify-content: center; align-items: flex-start; width: 100%; background: transparent;">
                                        <div style="width: 100%; max-width: 820px; aspect-ratio: 820 / 1150; background: #fff; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 0.25rem; overflow: hidden; position: relative; container-type: inline-size;">
                                            <iframe srcdoc="' . $html . '" style="position: absolute; top: 0; left: 0; width: 820px; height: 1150px; border: none; transform: scale(calc(100cqw / 820px)); transform-origin: top left;"></iframe>
                                        </div>
                                    </div>
                                ');
                            }),
                        \Filament\Schemas\Components\Actions::make([
                            \Filament\Actions\Action::make('sendSmsCode')
                                ->label(fn ($get) => $get('operation_id') ? 'Şifreyi Tekrar Gönder' : 'SMS Şifresi Gönder')
                                ->icon('heroicon-o-paper-airplane')
                                ->color('warning')
                                ->action(function ($set) {
                                    try {
                                        $service = app(\App\Services\GibEArsivService::class);
                                        $smsResult = $service->startSmsVerification();
                                        if ($smsResult['success']) {
                                            $set('operation_id', $smsResult['operation_id']);
                                            \Filament\Notifications\Notification::make()
                                                ->title('SMS Gönderildi')
                                                ->body('Telefonunuza gelen SMS şifresini aşağıdaki alana girin.')
                                                ->success()
                                                ->send();
                                        } else {
                                            \Filament\Notifications\Notification::make()
                                                ->title('SMS Gönderilemedi')
                                                ->body($smsResult['message'])
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
                                ->hidden(fn (Order $record) => $record->gib_invoice_status === 'signed'),
                        ])->alignCenter(),
                        \Filament\Forms\Components\Hidden::make('operation_id'),
                        \Filament\Forms\Components\TextInput::make('sms_code')
                            ->label('SMS Şifresi')
                            ->required(fn ($get) => (bool)$get('operation_id'))
                            ->placeholder('Telefonunuza gelen SMS şifresini girin')
                            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                            ->dehydrateStateUsing(fn ($state) => strtoupper(trim((string) $state)))
                            ->hidden(fn ($get, Order $record) => $record->gib_invoice_status === 'signed' || !$get('operation_id')),
                    ])
                    ->mountUsing(function (\Filament\Schemas\Schema $form, Order $record) {
                        try {
                            $service = app(\App\Services\GibEArsivService::class);

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
                                        
                                        // Başarıyla imzalanan faturayı müşteriye e-posta olarak gönder
                                        if (!empty($record->customer_email)) {
                                            try {
                                                $service->sendInvoiceMail($record);
                                            } catch (\Exception $mailEx) {
                                                Log::warning("Fatura e-postası gönderilemedi: " . $mailEx->getMessage());
                                            }
                                        }
                                        
                                        \Filament\Notifications\Notification::make()
                                            ->title('Fatura Zaten İmzalanmış')
                                            ->body('GİB üzerinden kontrol edildi, fatura daha önce imzalandığı için otomatik güncellendi.')
                                            ->success()
                                            ->send();
                                    }
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

                        if (empty($data['sms_code'])) {
                            \Filament\Notifications\Notification::make()
                                ->title('Hata')
                                ->body('Lütfen önce SMS şifresi gönderin ve gelen kodu girin.')
                                ->danger()
                                ->send();
                            throw new \Filament\Support\Exceptions\Halt();
                        }

                        try {
                            $service = app(\App\Services\GibEArsivService::class);
                            $smsCode = strtoupper(trim((string) $data['sms_code']));
                            $result = $service->completeSmsVerification($smsCode, $data['operation_id'], [$record->gib_invoice_uuid]);
                            
                            if ($result['success']) {
                                $newHtml = $service->getInvoiceHtml($record->gib_invoice_uuid);
                                $record->update([
                                    'gib_invoice_status' => 'signed',
                                    'gib_invoice_html' => $newHtml,
                                ]);

                                // Başarıyla imzalanan faturayı müşteriye e-posta olarak gönder
                                if (!empty($record->customer_email)) {
                                    try {
                                        $service->sendInvoiceMail($record);
                                    } catch (\Exception $mailEx) {
                                        Log::warning("Fatura e-postası gönderilemedi: " . $mailEx->getMessage());
                                    }
                                }

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
                    }),

                EditAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Düzenle'),

                Action::make('sendToPorego')
                    ->iconButton()
                    ->size('lg')
                    ->visible(fn (Order $record): bool => !in_array($record->status, ['delivered', 'completed', 'cancelled']))
                    ->tooltip(function (Order $record): string {
                        $code = trim((string)$record->cargo_tracking_code);
                        $hasReal = !empty($code) && !str_starts_with($code, '33') && $code !== $record->order_number && $code !== (string)$record->id;
                        
                        if ($hasReal && $record->status === 'shipped') {
                            return 'Kargo Takip: ' . $code;
                        }
                        if (in_array($record->status, ['processing', 'shipped'])) {
                            return 'Kargo Kodu Bekleniyor...';
                        }
                        if ($record->hasKickSpeedProducts()) {
                            return 'Kick Speed: Onayla ve Porego\'ya Gönder';
                        }
                        return 'Kargo Kodu Oluştur';
                    })
                    ->icon(function (Order $record): string {
                        $code = trim((string)$record->cargo_tracking_code);
                        $hasReal = !empty($code) && !str_starts_with($code, '33') && $code !== $record->order_number && $code !== (string)$record->id;
                        
                        if ($hasReal && in_array($record->status, ['shipped', 'processing'])) {
                            return 'heroicon-o-arrow-top-right-on-square';
                        }
                        if (in_array($record->status, ['processing', 'shipped'])) {
                            return 'heroicon-o-clock';
                        }
                        return 'heroicon-o-cube';
                    })
                    ->color(function (Order $record): string {
                        $code = trim((string)$record->cargo_tracking_code);
                        $hasReal = !empty($code) && !str_starts_with($code, '33') && $code !== $record->order_number && $code !== (string)$record->id;
                        
                        if ($hasReal && $record->status === 'shipped') {
                            return 'success';
                        }
                        if ($record->status === 'processing') {
                            return 'info';
                        }
                        return 'warning';
                    })
                    ->url(function (Order $record): ?string {
                        $code = trim((string)$record->cargo_tracking_code);
                        $hasReal = !empty($code) && !str_starts_with($code, '33') && $code !== $record->order_number && $code !== (string)$record->id;
                        
                        if ($hasReal && $record->status === 'shipped') {
                            $cargoName = strtolower((string)$record->cargo_company);
                            if (str_contains($cargoName, 'dhl')) {
                                return 'https://kargotakip.dhlecommerce.com.tr/?takipNo=' . urlencode($code);
                            }
                            if (str_contains($cargoName, 'aras')) {
                                return 'https://kargotakip.araskargo.com.tr/mainpage.aspx?code=' . urlencode($code);
                            }
                            if (str_contains($cargoName, 'yurtiçi') || str_contains($cargoName, 'yurtici')) {
                                return 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=' . urlencode($code);
                            }
                            if (str_contains($cargoName, 'mng')) {
                                return 'https://www.mngkargo.com.tr/gonderi-takip/?q=' . urlencode($code);
                            }
                            return 'https://kargotakip.dhlecommerce.com.tr/?takipNo=' . urlencode($code);
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->requiresConfirmation(function (Order $record): bool {
                        $code = trim((string)$record->cargo_tracking_code);
                        $hasReal = !empty($code) && !str_starts_with($code, '33') && $code !== $record->order_number && $code !== (string)$record->id;
                        // Kargo takip linki açılacaksa onay isteme
                        return !($hasReal && $record->status === 'shipped');
                    })
                    ->modalHeading(fn (Order $record): string => $record->hasKickSpeedProducts() ? 'Kick Speed Siparişini Onayla & Porego\'ya Gönder' : 'Kargo Kodu Oluştur')
                    ->modalDescription(function (Order $record): string {
                        if ($record->hasKickSpeedProducts()) {
                            return 'Bu sipariş Kick Speed marka ürün içermektedir. Kapıda ödeme siparişlerinde olduğu gibi yönetici onayı gereklidir. Onayladığınızda sipariş Porego sistemine iletilecek ve kargo barkodu oluşturulacaktır.';
                        }
                        return 'Kargo barkodu oluşturulacak ve sipariş durumu "Hazırlanıyor" olarak güncellenecektir.';
                    })
                    ->modalSubmitActionLabel(fn (Order $record): string => $record->hasKickSpeedProducts() ? 'Onayla ve Gönder' : 'Oluştur')
                    ->action(function (Order $record): void {
                        // Eğer gerçek kargo kodu varsa action çalışmasın (link açılacak)
                        $code = trim((string)$record->cargo_tracking_code);
                        $hasReal = !empty($code) && !str_starts_with($code, '33') && $code !== $record->order_number && $code !== (string)$record->id;
                        if ($hasReal && in_array($record->status, ['shipped', 'processing'])) {
                            return;
                        }
                        
                        $poregoService = app(\App\Services\PoregoApiService::class);
                        
                        // Sipariş Porego'ya gönder (barkod dahil)
                        $result = $poregoService->sendOrder($record, skipBarcode: false);
                        $isSuccess = is_array($result) ? ($result['success'] ?? false) : (bool)$result;
                        $message = is_array($result) ? ($result['message'] ?? '') : '';

                        if ($isSuccess) {
                            $record->refresh();
                            
                            $trackingCode = trim((string)$record->cargo_tracking_code);
                            $hasRealCode = !empty($trackingCode) 
                                && !str_starts_with($trackingCode, '33')
                                && $trackingCode !== $record->order_number
                                && $trackingCode !== (string)$record->id;
                            
                            if ($hasRealCode) {
                                $record->update(['status' => 'shipped']);
                                \Filament\Notifications\Notification::make()
                                    ->title('Kargo kodu oluşturuldu!')
                                    ->body("Kargo Kodu: {$trackingCode}. Sipariş durumu: Kargoda.")
                                    ->success()
                                    ->send();
                            } else {
                                // Barkod oluştu ama gerçek kargo kodu henüz yok → Hazırlanıyor
                                if (in_array($record->status, ['pending'])) {
                                    $record->update(['status' => 'processing']);
                                }
                                \Filament\Notifications\Notification::make()
                                    ->title('Barkod oluşturuldu')
                                    ->body('Sipariş durumu: Hazırlanıyor. Gerçek kargo kodu Porego senkronizasyonu ile otomatik gelecektir.')
                                    ->success()
                                    ->send();
                            }
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Porego Gönderim Uyarısı')
                                ->body($message ?: 'Sipariş Porego\'ya iletilirken hata oluştu.')
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),

                Action::make('returnOrder')
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('İade Al')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('pink')
                    ->modalHeading('Sipariş İade İşlemi')
                    ->modalDescription('Sipariş iade edilecek, stoklar geri yüklenecek ve muhasebe kaydı oluşturulacaktır.')
                    ->modalSubmitActionLabel('İade İşlemini Tamamla')
                    ->modalCancelActionLabel('Vazgeç')
                    ->form([
                        Select::make('return_reason')
                            ->label('İade Nedeni')
                            ->options(\App\Models\AccountingEntry::RETURN_REASONS)
                            ->required()
                            ->native(false),
                        \Filament\Forms\Components\Textarea::make('return_note')
                            ->label('İade Notu')
                            ->placeholder('İade ile ilgili ek bilgi girebilirsiniz...')
                            ->rows(3),
                    ])
                    ->action(function (Order $record, array $data): void {
                        // 1. Sipariş durumunu iade yap (saveQuietly: Observer'ın çift stok iadesi yapmasını önle)
                        $record->status = 'returned';
                        $record->payment_status = 'refunded';
                        $record->porego_sync_locked = true; // Porego senkronizasyonu bu siparişi artık ezmeyecek
                        $record->saveQuietly();

                        // Audit log kaydı (Observer devre dışı olduğu için manuel ekle)
                        \App\Models\OrderStatusHistory::create([
                            'order_id' => $record->id,
                            'old_status' => $record->getOriginal('status'),
                            'new_status' => 'returned',
                            'changed_by' => auth()->id(),
                            'note' => 'İade işlemi admin panel üzerinden yapıldı. Neden: ' . (\App\Models\AccountingEntry::RETURN_REASONS[$data['return_reason']] ?? $data['return_reason']),
                        ]);

                        // 2. Stokları geri yükle — SADECE daha önce stok düşürülmüş siparişlerde
                        // Kapıda ödeme: her zaman düşürülmüştü
                        // Kredi kartı/Havale: sadece payment_status paid/refunded ise düşürülmüştü
                        $wasStockDecremented = $record->payment_method === 'cash_on_delivery'
                            || in_array($record->getOriginal('payment_status') ?? $record->payment_status, ['paid', 'refunded']);

                        if ($wasStockDecremented) {
                            $record->loadMissing(['items.product', 'items.variant']);
                            foreach ($record->items as $item) {
                                if ($item->variant) {
                                    $item->variant->safeIncrement(
                                        $item->quantity,
                                        \App\Models\StockMovement::TYPE_RETURN,
                                        $record->order_number,
                                        "İade: " . (\App\Models\AccountingEntry::RETURN_REASONS[$data['return_reason']] ?? $data['return_reason'])
                                    );
                                    $item->product?->syncFromVariants();
                                } elseif ($item->product) {
                                    $item->product->safeIncrement(
                                        $item->quantity,
                                        \App\Models\StockMovement::TYPE_RETURN,
                                        $record->order_number,
                                        "İade: " . (\App\Models\AccountingEntry::RETURN_REASONS[$data['return_reason']] ?? $data['return_reason'])
                                    );
                                }
                            }
                            \Illuminate\Support\Facades\Log::info("İade stok geri yükleme: #{$record->order_number}");
                        } else {
                            \Illuminate\Support\Facades\Log::info("İade stok geri yükleme atlandı (stok düşürülmemişti): #{$record->order_number}, payment_status: {$record->payment_status}");
                        }

                        // 3. Muhasebe iade kaydı
                        \App\Models\AccountingEntry::recordRefund(
                            $record,
                            $data['return_reason'],
                            $data['return_note'] ?? null,
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Sipariş iade alındı')
                            ->body("#{$record->order_number} siparişi iade edildi. Stoklar geri yüklendi ve muhasebe kaydı oluşturuldu.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record): bool => in_array($record->status, ['shipped', 'delivered'])),

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
                        $oldStatus = $record->status;
                        
                        // saveQuietly: Observer'ın çift stok iadesi yapmasını önle
                        $record->status = 'cancelled';
                        $record->porego_sync_locked = true; // Porego senkronizasyonu bu siparişi artık ezmeyecek
                        if ($record->payment_status === 'paid') {
                            $record->payment_status = 'refunded';
                        }
                        $record->saveQuietly();

                        // Audit log kaydı (Observer devre dışı olduğu için manuel ekle)
                        \App\Models\OrderStatusHistory::create([
                            'order_id' => $record->id,
                            'old_status' => $oldStatus,
                            'new_status' => 'cancelled',
                            'changed_by' => auth()->id(),
                            'note' => 'Sipariş admin panel üzerinden iptal edildi.',
                        ]);

                        // Stok geri yükleme — SADECE daha önce stok düşürülmüş siparişlerde
                        $wasStockDecremented = $record->payment_method === 'cash_on_delivery' 
                            || in_array($oldStatus, ['processing', 'shipped', 'delivered'])
                            || $record->payment_status === 'refunded'; // Az önce refunded yaptık → paid idi

                        if ($wasStockDecremented) {
                            $record->loadMissing(['items.product', 'items.variant']);
                            foreach ($record->items as $item) {
                                if ($item->variant) {
                                    $item->variant->safeIncrement(
                                        $item->quantity,
                                        \App\Models\StockMovement::TYPE_CANCEL,
                                        $record->order_number,
                                        "Sipariş iptali ile stok geri yüklendi"
                                    );
                                    $item->product?->syncFromVariants();
                                } elseif ($item->product) {
                                    $item->product->safeIncrement(
                                        $item->quantity,
                                        \App\Models\StockMovement::TYPE_CANCEL,
                                        $record->order_number,
                                        "Sipariş iptali ile stok geri yüklendi"
                                    );
                                }
                            }
                        }

                        // Porego'ya iptal bildirimi gönder
                        try {
                            $apiKey = \App\Models\Setting::where('key', 'porego_api_key')->value('value') ?: env('POREGO_API_KEY');
                            $apiSecret = \App\Models\Setting::where('key', 'porego_api_secret')->value('value') ?: env('POREGO_API_SECRET');
                            $apiUrl = \App\Models\Setting::where('key', 'porego_api_url')->value('value') ?: env('POREGO_API_URL', 'https://back.porego.com/depokargo/api/v1/merchant-api/v1');

                            if ($apiKey && $apiSecret) {
                                \Illuminate\Support\Facades\Http::withHeaders([
                                    'X-Api-Key' => $apiKey,
                                    'X-Api-Secret' => $apiSecret,
                                    'Accept' => 'application/json',
                                    'Content-Type' => 'application/json',
                                ])->put("{$apiUrl}/orders/{$record->order_number}", [
                                    'status' => 'CANCELLED',
                                ]);
                            }
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error("Porego iptal bildirimi hatası: " . $e->getMessage());
                        }

                        // Cache temizle
                        \Illuminate\Support\Facades\Cache::forget('orders_tab_counts');
                        \Illuminate\Support\Facades\Cache::forget('orders_pending_count');

                        \Filament\Notifications\Notification::make()
                            ->title('Sipariş iptal edildi ve stoklar güncellendi.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'processing'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('supplierWaybillPrint')
                        ->label('Tedarikçi İrsaliyesi (Yazdır / Önizle)')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->url(fn (Collection $records) => route('admin.orders.supplier-waybill.print', ['ids' => $records->pluck('id')->implode(',')]))
                        ->openUrlInNewTab(),

                    BulkAction::make('supplierWaybill')
                        ->label('Tedarikçi İrsaliyesi (PDF İndir)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('primary')
                        ->url(fn (Collection $records) => route('admin.orders.supplier-waybill.download', ['ids' => $records->pluck('id')->implode(',')]))
                        ->openUrlInNewTab(),

                    BulkAction::make('bulkSendToPorego')
                        ->label('Porego\'ya Gönder')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Seçilen Siparişleri Porego\'ya Gönder')
                        ->modalDescription('Seçtiğiniz siparişler Porego Kargo sistemine ürün detaylarıyla birlikte aktarılacaktır.')
                        ->modalSubmitActionLabel('Gönder')
                        ->action(function (Collection $records) {
                            $successCount = 0;
                            $failCount = 0;
                            $failMessages = [];
                            $service = app(\App\Services\PoregoApiService::class);

                            foreach ($records as $order) {
                                $result = $service->sendOrder($order);
                                if (is_array($result) && ($result['success'] ?? false)) {
                                    $successCount++;
                                } else {
                                    $failCount++;
                                    $msg = is_array($result) ? ($result['message'] ?? 'Bilinmeyen hata') : 'Bilinmeyen hata';
                                    $failMessages[] = "#{$order->order_number}: {$msg}";
                                }
                            }

                            $body = "{$successCount} adet sipariş Porego'ya iletildi. {$failCount} adet sipariş başarısız oldu.";
                            if (!empty($failMessages)) {
                                $body .= "\n\n" . implode("\n", array_slice($failMessages, 0, 5));
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Toplu Porego Gönderimi Tamamlandı')
                                ->body($body)
                                ->{$failCount > 0 ? 'warning' : 'success'}()
                                ->send();
                        }),

                    BulkAction::make('bulkCreateGibInvoice')
                        ->label('GİB Faturası Kes')
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

                            $delivereds = $records->filter(fn ($r) => !$r->is_invoiced && $r->status === 'delivered');
                            if ($delivereds->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('İşlem Yapılmadı')
                                    ->body('Seçili siparişlerde faturası kesilmemiş ve "Teslim Edildi" durumunda olan bir sipariş bulunmuyor.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            foreach ($delivereds as $order) {
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
                        ->label('Fatura Kaydını Sıfırla')
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
                        ->label('Fatura Durumu Eşitle')
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
                        ->label('Faturayı İmzala (SMS)')
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
                                ->placeholder('Gelen şifreyi girin')
                                ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                                ->dehydrateStateUsing(fn ($state) => strtoupper(trim((string) $state))),
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
                                $smsCode = strtoupper(trim((string) $data['sms_code']));
                                $result = $service->completeSmsVerification($smsCode, $data['operation_id'], $uuids);
                                
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

                                        // Başarıyla imzalanan faturayı müşteriye e-posta olarak gönder
                                        if (!empty($record->customer_email)) {
                                            try {
                                                $service->sendInvoiceMail($record);
                                            } catch (\Exception $mailEx) {
                                                Log::warning("Fatura e-postası gönderilemedi: " . $mailEx->getMessage());
                                            }
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
                        ->label('Google Ads CSV İndir')
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

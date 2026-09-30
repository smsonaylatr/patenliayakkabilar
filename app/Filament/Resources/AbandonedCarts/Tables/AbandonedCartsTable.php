<?php

namespace App\Filament\Resources\AbandonedCarts\Tables;

use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AbandonedCartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where(function($q) {
                    $q->whereNotNull('user_id')
                      ->orWhereNotNull('guest_email')
                      ->orWhereNotNull('guest_phone');
                })->has('items')->with(['user', 'items.product']);
            })
            ->columns([
                TextColumn::make('user_or_guest_name')
                    ->label('Müşteri Adı')
                    ->getStateUsing(fn ($record) => $record->user_id ? $record->user?->name : ($record->guest_name ?? '-'))
                    ->searchable(['guest_name'])
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user_or_guest_email')
                    ->label('E-posta')
                    ->getStateUsing(fn ($record) => $record->user_id ? $record->user?->email : ($record->guest_email ?? '-'))
                    ->searchable(['guest_email']),
                TextColumn::make('user_or_guest_phone')
                    ->label('Telefon')
                    ->getStateUsing(fn ($record) => $record->user_id ? $record->user?->phone : ($record->guest_phone ?? '-'))
                    ->searchable(['guest_phone']),
                TextColumn::make('items_count')
                    ->label('Ürün')
                    ->counts('items')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('total')
                    ->label('Tutar')
                    ->getStateUsing(function ($record) {
                        return number_format($record->items->sum(function ($item) {
                            $price = $item->product?->discount_price ?? $item->product?->price ?? 0;
                            return $price * $item->quantity;
                        }), 2) . ' ₺';
                    })
                    ->badge()
                    ->color('success'),
                TextColumn::make('updated_at')
                    ->label('Son İşlem')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('mail_status')
                    ->label('Durum')
                    ->getStateUsing(function ($record) {
                        if ($record->coupon_mail_sent_at) {
                            return '✅ Kuponlu';
                        }
                        if ($record->reminder_mail_sent_at) {
                            return '📧 Hatırlatma';
                        }
                        return '⏳ Bekliyor';
                    })
                    ->badge()
                    ->color(fn ($record) => match(true) {
                        !empty($record->coupon_mail_sent_at) => 'success',
                        !empty($record->reminder_mail_sent_at) => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('durum')
                    ->label('Gönderim Durumu')
                    ->options([
                        'bekliyor' => '⏳ Henüz gönderilmedi',
                        'hatirlatma' => '📧 Hatırlatma gitti',
                        'kuponlu' => '✅ Kuponlu gitti',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value'] ?? null) {
                            'bekliyor' => $query->whereNull('reminder_mail_sent_at')->whereNull('coupon_mail_sent_at'),
                            'hatirlatma' => $query->whereNotNull('reminder_mail_sent_at')->whereNull('coupon_mail_sent_at'),
                            'kuponlu' => $query->whereNotNull('coupon_mail_sent_at'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    // ─── 1. Kuponsuz Hatırlatma Maili (OTOMATİK de gider, manuel de) ─────
                    Action::make('remind')
                        ->label(fn ($record) => $record->reminder_mail_sent_at
                            ? '✅ Hatırlatma Gitti (' . $record->reminder_mail_sent_at->format('d.m H:i') . ')'
                            : 'Mail Hatırlat')
                        ->icon('heroicon-o-envelope')
                        ->color(fn ($record) => $record->reminder_mail_sent_at ? 'gray' : 'warning')
                        ->requiresConfirmation()
                        ->modalHeading('Kuponsuz Hatırlatma Gönder')
                        ->modalDescription(fn ($record) => $record->reminder_mail_sent_at
                            ? '⚠️ Bu müşteriye daha önce ' . $record->reminder_mail_sent_at->format('d.m.Y H:i') . ' tarihinde hatırlatma gönderilmiş. Tekrar göndermek istediğinize emin misiniz?'
                            : 'Müşteriye sepetindeki ürünleri hatırlatan kuponsuz bir e-posta gönderilecektir. Onaylıyor musunuz?')
                        ->modalSubmitActionLabel('Evet, Gönder')
                        ->visible(fn ($record) => !empty($record->user?->email) || !empty($record->guest_email))
                        ->action(function ($record) {
                            $email = $record->user?->email ?? $record->guest_email;
                            if ($email) {
                                try {
                                    \Illuminate\Support\Facades\Log::info('Kuponsuz hatırlatma maili gönderimi başlatıldı: ' . $email);
                                    \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\AbandonedCartReminderMail($record));

                                    $record->update(['reminder_mail_sent_at' => now()]);

                                    \Filament\Notifications\Notification::make()
                                        ->title('Hatırlatma Maili Gönderildi')
                                        ->body("Kuponsuz hatırlatma → {$email}")
                                        ->success()
                                        ->send();
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error('Hatırlatma maili gönderilemedi: ' . $e->getMessage());
                                    \Filament\Notifications\Notification::make()
                                        ->title('Mail Gönderilirken Hata Oluştu!')
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Müşterinin e-posta adresi bulunamadı!')
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // ─── 2. Kuponsuz SMS Hatırlatma (OTOMATİK de gider, manuel de) ─────
                    Action::make('sms_remind_no_coupon')
                        ->label('SMS Hatırlat')
                        ->icon('heroicon-o-chat-bubble-left')
                        ->color('warning')
                        ->modalHeading('📱 Kuponsuz SMS Hatırlatma')
                        ->modalDescription(fn ($record) => '📞 Telefon: ' . ($record->user?->phone ?? $record->guest_phone ?? 'Yok'))
                        ->modalSubmitActionLabel('📩 Gönder')
                        ->modalCancelActionLabel('İptal')
                        ->modalWidth('lg')
                        ->visible(fn ($record) => !empty($record->user?->phone) || !empty($record->guest_phone))
                        ->form([
                            \Filament\Forms\Components\Textarea::make('sms_message')
                                ->label('SMS Taslağı')
                                ->rows(4)
                                ->helperText('Mesajı düzenleyebilirsiniz. Kuponsuz hatırlatma mesajıdır.')
                                ->required(),
                        ])
                        ->mountUsing(function (\Filament\Schemas\Schema $form, $record) {
                            $name = $record->user?->name ?? $record->guest_name ?? '';
                            $greeting = $name ? "Sayin {$name}, sepetinizdeki" : "Merhaba, sepetinizdeki";

                            $message = "{$greeting} urunler sizi bekliyor! "
                                     . "Alisverisi tamamlamak icin: https://patenliayakkabilar.com/checkout";

                            $form->fill([
                                'sms_message' => $message,
                            ]);
                        })
                        ->action(function ($record, array $data) {
                            $phone = $record->user?->phone ?? $record->guest_phone;
                            if (!$phone) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Telefon numarası bulunamadı!')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            try {
                                $vatanService = app(\App\Services\VatanSmsService::class);
                                $result = $vatanService->send($phone, $data['sms_message'], 'turkce', 'bilgi');

                                if ($result) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS Hatırlatma Gönderildi!')
                                        ->body("Kuponsuz hatırlatma → {$phone}")
                                        ->success()
                                        ->send();
                                } else {
                                    $errorDetail = $vatanService->getLastError() ?? 'Bilinmeyen hata';
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS Gönderilemedi')
                                        ->body("Hata: {$errorDetail}")
                                        ->danger()
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Hata!')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // ─── 3. %10 Kuponlu Mail + SMS (SADECE MANUEL) ─────────────────
                    Action::make('coupon_mail_sms')
                        ->label(fn ($record) => $record->coupon_mail_sent_at
                            ? '✅ Kupon Gitti (' . $record->coupon_mail_sent_at->format('d.m H:i') . ')'
                            : '🎁 Mail + SMS + %10 Kupon')
                        ->icon('heroicon-o-gift')
                        ->color(fn ($record) => $record->coupon_mail_sent_at ? 'gray' : 'danger')
                        ->requiresConfirmation()
                        ->modalHeading('%10 Kuponlu Mail + SMS Gönder')
                        ->modalDescription(function ($record) {
                            $email = $record->user?->email ?? $record->guest_email ?? null;
                            $phone = $record->user?->phone ?? $record->guest_phone ?? null;
                            $channels = [];
                            if ($email) $channels[] = "📧 Mail: {$email}";
                            if ($phone) $channels[] = "📱 SMS: {$phone}";
                            $channelInfo = implode("\n", $channels) ?: '❌ İletişim bilgisi yok';

                            $msg = "{$channelInfo}\n\n";

                            if ($record->coupon_mail_sent_at) {
                                $msg .= "⚠️ Bu müşteriye daha önce {$record->coupon_mail_sent_at->format('d.m.Y H:i')} tarihinde kuponlu gönderim yapılmış. Yeni kupon oluşturulup tekrar gönderilecektir.";
                            } else {
                                $msg .= "Müşteriye %10 indirim kuponu oluşturulup e-posta ve SMS ile gönderilecektir. Kupon 48 saat geçerli olacaktır.";
                            }
                            return $msg;
                        })
                        ->modalSubmitActionLabel('Kupon Oluştur ve Gönder')
                        ->visible(fn ($record) => !empty($record->user?->email) || !empty($record->guest_email) || !empty($record->user?->phone) || !empty($record->guest_phone))
                        ->action(function ($record) {
                            $email = $record->user?->email ?? $record->guest_email;
                            $phone = $record->user?->phone ?? $record->guest_phone;

                            if (!$email && !$phone) {
                                \Filament\Notifications\Notification::make()
                                    ->title('İletişim bilgisi bulunamadı!')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            try {
                                // Kişiye özel %10 kupon oluştur
                                do {
                                    $couponCode = 'PATEN10-' . random_int(1000, 9999);
                                } while (\App\Models\Coupon::where('code', $couponCode)->exists());

                                $expiresAt = now()->addHours(48);

                                \App\Models\Coupon::create([
                                    'code' => $couponCode,
                                    'type' => 'percentage',
                                    'value' => 10,
                                    'min_cart_total' => null,
                                    'usage_limit' => 1,
                                    'used_count' => 0,
                                    'expires_at' => $expiresAt,
                                    'status' => true,
                                ]);

                                $results = [];

                                // ─── Mail Gönderimi ────────────────────────
                                if ($email) {
                                    try {
                                        \Illuminate\Support\Facades\Mail::to($email)->send(
                                            new \App\Mail\AbandonedCartCouponMail($record, $couponCode, $expiresAt->format('d.m.Y H:i'))
                                        );
                                        $results[] = "📧 Mail → {$email} ✅";
                                    } catch (\Exception $e) {
                                        \Illuminate\Support\Facades\Log::error("Kuponlu mail gönderilemedi: {$email} - " . $e->getMessage());
                                        $results[] = "📧 Mail → {$email} ❌ " . $e->getMessage();
                                    }
                                }

                                // ─── SMS Gönderimi ─────────────────────────
                                if ($phone) {
                                    try {
                                        $name = $record->user?->name ?? $record->guest_name ?? '';
                                        $greeting = $name ? "Sayin {$name}, sepetinizdeki" : "Merhaba, sepetinizdeki";

                                        $smsMessage = "{$greeting} urunler sizi bekliyor! "
                                                    . "Size ozel %10 indirim kodunuz: {$couponCode} "
                                                    . "(48 saat gecerli, tek kullanimlik). "
                                                    . "Alisverisi tamamlamak icin: https://patenliayakkabilar.com/checkout";

                                        $vatanService = app(\App\Services\VatanSmsService::class);
                                        $result = $vatanService->send($phone, $smsMessage, 'turkce', 'bilgi');

                                        if ($result) {
                                            $record->update(['abandoned_sms_sent_at' => now()]);
                                            $results[] = "📱 SMS → {$phone} ✅";
                                        } else {
                                            $errorDetail = $vatanService->getLastError() ?? 'Bilinmeyen hata';
                                            $results[] = "📱 SMS → {$phone} ❌ {$errorDetail}";
                                        }
                                    } catch (\Exception $e) {
                                        $results[] = "📱 SMS → {$phone} ❌ " . $e->getMessage();
                                    }
                                }

                                $record->update(['coupon_mail_sent_at' => now()]);

                                \Filament\Notifications\Notification::make()
                                    ->title('%10 Kuponlu Gönderim Tamamlandı!')
                                    ->body("Kupon: {$couponCode} (48 saat geçerli)\n" . implode("\n", $results))
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Kuponlu Gönderim Hatası!')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
                ->label('İşlemler')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary')
                ->button(),
            ])
            ->bulkActions([
                // ─── Toplu: Kuponsuz Hatırlatma Maili ────────────────────
                BulkAction::make('bulk_remind_mail')
                    ->label('📧 Toplu Hatırlatma Maili')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Toplu Kuponsuz Hatırlatma Maili')
                    ->modalDescription('Seçili müşterilere kuponsuz hatırlatma e-postası gönderilecektir.')
                    ->modalSubmitActionLabel('Hepsine Gönder')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        $sent = 0;
                        $failed = 0;

                        foreach ($records as $record) {
                            $email = $record->user?->email ?? $record->guest_email;
                            if (!$email) {
                                $failed++;
                                continue;
                            }
                            try {
                                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\AbandonedCartReminderMail($record));
                                $record->update(['reminder_mail_sent_at' => now()]);
                                $sent++;
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Toplu hatırlatma mail hatası: {$email} - " . $e->getMessage());
                                $failed++;
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Toplu Hatırlatma Maili Tamamlandı')
                            ->body("✅ Gönderildi: {$sent} | ❌ Hata: {$failed}")
                            ->success()
                            ->send();
                    }),

                // ─── Toplu: %10 Kuponlu Mail + SMS (MANUEL) ─────────────
                BulkAction::make('bulk_coupon_mail_sms')
                    ->label('🎁 Toplu %10 Kupon (Mail + SMS)')
                    ->icon('heroicon-o-gift')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Toplu %10 Kuponlu Mail + SMS Gönder')
                    ->modalDescription('Seçili müşterilere kişiye özel %10 indirim kuponu oluşturulup mail ve SMS ile gönderilecektir. Her müşteri farklı bir kupon kodu alacaktır.')
                    ->modalSubmitActionLabel('Kuponları Oluştur ve Gönder')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        $sent = 0;
                        $failed = 0;

                        foreach ($records as $record) {
                            $email = $record->user?->email ?? $record->guest_email;
                            $phone = $record->user?->phone ?? $record->guest_phone;

                            if (!$email && !$phone) {
                                $failed++;
                                continue;
                            }

                            try {
                                // Kişiye özel kupon oluştur
                                do {
                                    $couponCode = 'PATEN10-' . random_int(1000, 9999);
                                } while (\App\Models\Coupon::where('code', $couponCode)->exists());

                                $expiresAt = now()->addHours(48);

                                \App\Models\Coupon::create([
                                    'code' => $couponCode,
                                    'type' => 'percentage',
                                    'value' => 10,
                                    'min_cart_total' => null,
                                    'usage_limit' => 1,
                                    'used_count' => 0,
                                    'expires_at' => $expiresAt,
                                    'status' => true,
                                ]);

                                // Mail gönder
                                if ($email) {
                                    try {
                                        \Illuminate\Support\Facades\Mail::to($email)->send(
                                            new \App\Mail\AbandonedCartCouponMail($record, $couponCode, $expiresAt->format('d.m.Y H:i'))
                                        );
                                    } catch (\Exception $e) {
                                        \Illuminate\Support\Facades\Log::error("Toplu kuponlu mail hatası: {$email} - " . $e->getMessage());
                                    }
                                }

                                // SMS gönder
                                if ($phone) {
                                    try {
                                        $name = $record->user?->name ?? $record->guest_name ?? '';
                                        $greeting = $name ? "Sayin {$name}, sepetinizdeki" : "Merhaba, sepetinizdeki";

                                        $smsMessage = "{$greeting} urunler sizi bekliyor! "
                                                    . "Size ozel %10 indirim kodunuz: {$couponCode} "
                                                    . "(48 saat gecerli, tek kullanimlik). "
                                                    . "Alisverisi tamamlamak icin: https://patenliayakkabilar.com/checkout";

                                        $vatanService = app(\App\Services\VatanSmsService::class);
                                        $vatanService->send($phone, $smsMessage, 'turkce', 'bilgi');
                                        $record->update(['abandoned_sms_sent_at' => now()]);
                                    } catch (\Exception $e) {
                                        \Illuminate\Support\Facades\Log::error("Toplu kuponlu SMS hatası: {$phone} - " . $e->getMessage());
                                    }
                                }

                                $record->update(['coupon_mail_sent_at' => now()]);
                                $sent++;
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Toplu kupon hatası (Sepet #{$record->id}): " . $e->getMessage());
                                $failed++;
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Toplu Kuponlu Gönderim Tamamlandı')
                            ->body("✅ Gönderildi: {$sent} | ❌ Hata: {$failed}")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}

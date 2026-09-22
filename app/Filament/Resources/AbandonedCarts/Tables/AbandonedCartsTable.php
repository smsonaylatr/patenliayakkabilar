<?php

namespace App\Filament\Resources\AbandonedCarts\Tables;

use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->label('Sepetteki Ürün')
                    ->counts('items')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('total')
                    ->label('Sepet Tutarı')
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
                    ->label('Mail Durumu')
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
                //
            ])
            ->actions([
                // ─── 1. Kuponsuz Hatırlatma Maili ─────────────────────────
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

                                \Illuminate\Support\Facades\Log::info('Kuponsuz hatırlatma maili başarıyla gönderildi: ' . $email);

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

                // ─── 2. %10 Kuponlu Mail Gönderimi ────────────────────────
                Action::make('coupon_mail')
                    ->label(fn ($record) => $record->coupon_mail_sent_at
                        ? '✅ Kupon Gitti (' . $record->coupon_mail_sent_at->format('d.m H:i') . ')'
                        : 'Mail + %10 Kupon')
                    ->icon('heroicon-o-gift')
                    ->color(fn ($record) => $record->coupon_mail_sent_at ? 'gray' : 'danger')
                    ->requiresConfirmation()
                    ->modalHeading('%10 Kuponlu Mail Gönder')
                    ->modalDescription(function ($record) {
                        $email = $record->user?->email ?? $record->guest_email ?? 'bulunamadı';
                        $msg = "📧 E-posta: {$email}\n\n";

                        if ($record->coupon_mail_sent_at) {
                            $msg .= "⚠️ Bu müşteriye daha önce {$record->coupon_mail_sent_at->format('d.m.Y H:i')} tarihinde kuponlu mail gönderilmiş. Yeni kupon oluşturulup tekrar gönderilecektir.";
                        } else {
                            $msg .= "Müşteriye %10 indirim kuponu oluşturulup e-posta ile gönderilecektir. Kupon 48 saat geçerli olacaktır.";
                        }
                        return $msg;
                    })
                    ->modalSubmitActionLabel('Kupon Oluştur ve Gönder')
                    ->visible(fn ($record) => !empty($record->user?->email) || !empty($record->guest_email))
                    ->action(function ($record) {
                        $email = $record->user?->email ?? $record->guest_email;
                        if (!$email) {
                            \Filament\Notifications\Notification::make()
                                ->title('E-posta adresi bulunamadı!')
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

                            // Kuponlu maili gönder
                            \Illuminate\Support\Facades\Mail::to($email)->send(
                                new \App\Mail\AbandonedCartCouponMail($record, $couponCode, $expiresAt->format('d.m.Y H:i'))
                            );

                            $record->update(['coupon_mail_sent_at' => now()]);

                            \Illuminate\Support\Facades\Log::info("Manuel kuponlu mail gönderildi: {$email}, Kupon: {$couponCode}");

                            \Filament\Notifications\Notification::make()
                                ->title('%10 Kuponlu Mail Gönderildi!')
                                ->body("Kupon: {$couponCode} (48 saat geçerli)\nGönderildi: {$email}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Kuponlu mail gönderilemedi: {$email} - " . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Kuponlu Mail Gönderilemedi!')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // ─── 3. SMS + %10 Kupon ───────────────────────────────────
                Action::make('sms_remind')
                    ->label('SMS + %10 Kupon')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('success')
                    ->modalHeading('📱 SMS Taslağı — Kuponlu Hatırlatma')
                    ->modalDescription(fn ($record) => '📞 Telefon: ' . ($record->user?->phone ?? $record->guest_phone ?? 'Yok'))
                    ->modalSubmitActionLabel('📩 Gönder')
                    ->modalCancelActionLabel('⏩ Atla')
                    ->modalWidth('lg')
                    ->visible(fn ($record) => !empty($record->user?->phone) || !empty($record->guest_phone))
                    ->form([
                        \Filament\Forms\Components\Textarea::make('sms_message')
                            ->label('SMS Taslağı')
                            ->rows(5)
                            ->helperText('Mesajı düzenleyebilirsiniz. Kupon kodu gönderim sırasında otomatik oluşturulup mesaja eklenecektir.')
                            ->required(),
                    ])
                    ->mountUsing(function (\Filament\Schemas\Schema $form, $record) {
                        $name = $record->user?->name ?? $record->guest_name ?? '';
                        $greeting = $name ? "Sayin {$name}, sepetinizdeki" : "Merhaba, sepetinizdeki";

                        $message = "{$greeting} urunler sizi bekliyor! "
                                 . "Size ozel %10 indirim kodunuz: {KUPON_KODU} "
                                 . "(3 gun gecerli, tek kullanimlik). "
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
                            // Kişiye özel %10 kupon oluştur
                            do {
                                $couponCode = 'PATEN10-' . random_int(1000, 9999);
                            } while (\App\Models\Coupon::where('code', $couponCode)->exists());

                            \App\Models\Coupon::create([
                                'code' => $couponCode,
                                'type' => 'percentage',
                                'value' => 10,
                                'min_cart_total' => null,
                                'usage_limit' => 1,
                                'used_count' => 0,
                                'expires_at' => now()->addDays(3),
                                'status' => true,
                            ]);

                            // Mesajdaki placeholder'ı gerçek kupon koduyla değiştir
                            $message = str_replace('{KUPON_KODU}', $couponCode, $data['sms_message']);

                            $vatanService = app(\App\Services\VatanSmsService::class);
                            $result = $vatanService->send($phone, $message, 'turkce', 'bilgi');

                            if ($result) {
                                $record->update(['abandoned_sms_sent_at' => now()]);

                                \Filament\Notifications\Notification::make()
                                    ->title('SMS + Kupon Gönderildi!')
                                    ->body("Kupon: {$couponCode} (%10, 3 gün geçerli)")
                                    ->success()
                                    ->send();
                            } else {
                                $errorDetail = $vatanService->getLastError() ?? 'Bilinmeyen hata';
                                \Filament\Notifications\Notification::make()
                                    ->title('SMS Gönderilemedi')
                                    ->body("Hata: {$errorDetail}. Kupon oluşturuldu: {$couponCode}")
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
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}

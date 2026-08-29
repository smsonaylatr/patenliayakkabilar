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
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('remind')
                    ->label('Mail Hatırlat')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Hatırlatma Gönder')
                    ->modalDescription('Müşteriye sepetindeki ürünleri hatırlatan bir e-posta gönderilecektir. Onaylıyor musunuz?')
                    ->modalSubmitActionLabel('Evet, Gönder')
                    ->visible(fn ($record) => !empty($record->user?->email) || !empty($record->guest_email))
                    ->action(function ($record) {
                        $email = $record->user?->email ?? $record->guest_email;
                        if ($email) {
                            try {
                                \Illuminate\Support\Facades\Log::info('Sepeti terk etme maili gönderimi başlatıldı: ' . $email);
                                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\AbandonedCartReminderMail($record));
                                \Illuminate\Support\Facades\Log::info('Sepeti terk etme maili başarıyla gönderildi: ' . $email);

                                \Filament\Notifications\Notification::make()
                                    ->title('Hatırlatma Başarıyla Gönderildi')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Sepeti terk etme maili gönderilirken hata oluştu: ' . $e->getMessage());
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
                Action::make('sms_remind')
                    ->label('SMS Hatırlat')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('SMS Hatırlatma Gönder')
                    ->modalDescription('Müşteriye sepetindeki ürünleri hatırlatan bir SMS gönderilecektir. Onaylıyor musunuz?')
                    ->modalSubmitActionLabel('Evet, SMS Gönder')
                    ->visible(fn ($record) => !empty($record->user?->phone) || !empty($record->guest_phone))
                    ->action(function ($record) {
                        $phone = $record->user?->phone ?? $record->guest_phone;
                        if ($phone) {
                            try {
                                \Illuminate\Support\Facades\Log::info('Sepeti terk etme SMS gönderimi başlatıldı: ' . $phone);
                                
                                $name = $record->user?->name ?? $record->guest_name ?? 'Müşterimiz';
                                $messageTemplate = \App\Models\Setting::where('key', 'vatansms_abandoned_cart_message')->value('value');
                                
                                if (empty($messageTemplate)) {
                                    $message = "Merhaba {$name}, sepetinizde ürünleriniz sizi bekliyor! Alışverişinizi tamamlamak için https://patenliayakkabilar.com adresini ziyaret edin.";
                                } else {
                                    $message = str_replace(['{isim}', '{siparis_no}', '{tutar}'], [$name, '', ''], $messageTemplate);
                                }

                                $vatanService = app(\App\Services\VatanSmsService::class);
                                $result = $vatanService->send($phone, $message, 'turkce', 'ticari');

                                if ($result) {
                                    \Illuminate\Support\Facades\Log::info('Sepeti terk etme SMS başarıyla gönderildi: ' . $phone);
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS Hatırlatma Başarıyla Gönderildi')
                                        ->success()
                                        ->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS Gönderilemedi')
                                        ->body('VatanSMS API üzerinden gönderim başarısız oldu. API ayarlarınızı kontrol edin.')
                                        ->danger()
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Sepeti terk etme SMS gönderilirken hata oluştu: ' . $e->getMessage());
                                \Filament\Notifications\Notification::make()
                                    ->title('SMS Gönderilirken Hata Oluştu!')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Müşterinin telefon numarası bulunamadı!')
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

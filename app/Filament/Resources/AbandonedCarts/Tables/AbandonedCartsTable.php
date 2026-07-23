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
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('user_id')->has('items')->with(['user', 'items.product']))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Müşteri Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user.email')
                    ->label('E-posta')
                    ->searchable(),
                TextColumn::make('user.phone')
                    ->label('Telefon')
                    ->searchable(),
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
                    ->visible(fn ($record) => empty($record->user->phone) && !empty($record->user->email))
                    ->action(function ($record) {
                        if ($record->user && $record->user->email) {
                            try {
                                \Illuminate\Support\Facades\Log::info('Sepeti terk etme maili gönderimi başlatıldı: ' . $record->user->email);
                                \Illuminate\Support\Facades\Mail::to($record->user->email)->send(new \App\Mail\AbandonedCartReminderMail($record));
                                \Illuminate\Support\Facades\Log::info('Sepeti terk etme maili başarıyla gönderildi: ' . $record->user->email);

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
                    ->visible(fn ($record) => !empty($record->user->phone))
                    ->action(function ($record) {
                        if ($record->user && $record->user->phone) {
                            try {
                                \Illuminate\Support\Facades\Log::info('Sepeti terk etme SMS gönderimi başlatıldı: ' . $record->user->phone);
                                
                                $message = "Merhaba {$record->user->name}, sepetinizde ürünleriniz sizi bekliyor! Alışverişinizi tamamlamak için sitemizi ziyaret edin.";
                                $poregoService = app(\App\Services\PoregoApiService::class);
                                $result = $poregoService->sendSms($record->user->phone, $message);

                                if ($result['success']) {
                                    \Illuminate\Support\Facades\Log::info('Sepeti terk etme SMS başarıyla gönderildi: ' . $record->user->phone);
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS Hatırlatma Başarıyla Gönderildi')
                                        ->success()
                                        ->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS Gönderilemedi')
                                        ->body($result['message'])
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

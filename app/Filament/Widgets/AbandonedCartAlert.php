<?php

namespace App\Filament\Widgets;

use App\Models\Cart;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AbandonedCartAlert extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Terk Edilen Sepetler (Son 24 Saat)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Cart::query()
                    ->whereNotNull('user_id')
                    ->where('updated_at', '<=', now()->subHours(2))
                    ->where('updated_at', '>=', now()->subDays(1))
                    ->whereHas('items')
                    ->latest('updated_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Müşteri')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('E-posta'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Ürün Sayısı')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tutar')
                    ->money('try')
                    ->state(function (Cart $record) {
                        return $record->items->sum(fn ($item) => $item->quantity * $item->unit_price);
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terk Edilme Zamanı')
                    ->since(),
            ])
            ->actions([
                \Filament\Actions\Action::make('sendReminder')
                    ->label('Hatırlat')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->action(function (Cart $record) {
                        $email = $record->user?->email;
                        if (!empty($email)) {
                            try {
                                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\AbandonedCartReminderMail($record));
                                $record->update(['reminder_mail_sent_at' => now()]);
                                \Filament\Notifications\Notification::make()
                                    ->title('Sepet Hatırlatma E-Postası İletildi ✅')
                                    ->body("{$email} adresine sepet hatırlatma maili başarıyla gönderildi.")
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('E-Posta Gönderilemedi ❌')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('E-Posta Bulunamadı ⚠️')
                                ->body('Sepet sahibi kullanıcının kayıtlı bir e-posta adresi bulunmuyor.')
                                ->warning()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Terk Edilen Sepet Hatırlatması Gönder')
                    ->modalDescription(fn (Cart $record) => ($record->user?->email ?: 'E-posta yok') . ' adresine sepetindeki ürünleri hatırlatan kurumsal e-posta gönderilecek. Onaylıyor musunuz?')
                    ->modalSubmitActionLabel('Evet, Gönder'),
            ]);
    }
}

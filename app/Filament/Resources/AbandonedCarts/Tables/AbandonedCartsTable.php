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
                    ->label('Hatırlat')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Hatırlatma Gönder')
                    ->modalDescription('Müşteriye sepetindeki ürünleri hatırlatan bir e-posta gönderilecektir. Onaylıyor musunuz?')
                    ->modalSubmitActionLabel('Evet, Gönder')
                    ->action(function ($record) {
                        if ($record->user && $record->user->email) {
                            \Illuminate\Support\Facades\Mail::to($record->user->email)->send(new \App\Mail\AbandonedCartReminderMail($record));
                            \Filament\Notifications\Notification::make()
                                ->title('Hatırlatma Başarıyla Gönderildi')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Müşterinin e-posta adresi bulunamadı!')
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

<?php

namespace App\Filament\Resources\GiveawayEntries\Tables;

use App\Models\GiveawayEntry;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GiveawayEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_code')
                    ->label('Kura No')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('instagram_username')
                    ->label('Instagram')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary')
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),

                TextColumn::make('shoe_size')
                    ->label('Beden')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('city')
                    ->label('İl / İlçe')
                    ->formatStateUsing(fn ($record) => "{$record->city} / {$record->district}")
                    ->searchable(['city', 'district']),

                IconColumn::make('is_winner')
                    ->label('Kazanan')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Katılım Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('draw_winner')
                    ->label('🎲 Rastgele Talihli Çek')
                    ->color('success')
                    ->icon('heroicon-o-gift')
                    ->requiresConfirmation()
                    ->modalHeading('Rastgele Çekiliş Kurası')
                    ->modalDescription('Sistem katılan tüm adaylar arasından rastgele bir talihli belirleyecektir. Onaylıyor musunuz?')
                    ->action(function () {
                        $candidate = GiveawayEntry::where('is_winner', false)->inRandomOrder()->first();

                        if (!$candidate) {
                            Notification::make()
                                ->title('Çekilişe katılan aday bulunamadı!')
                                ->warning()
                                ->send();
                            return;
                        }

                        $candidate->update([
                            'is_winner' => true,
                            'won_prize' => 'Patenli Ayakkabı Hediye',
                        ]);

                        Notification::make()
                            ->title('🎉 ÇEKİLİŞ KAZANANI BELİRLENDİ!')
                            ->body("Kazanan: {$candidate->name} ({$candidate->instagram_username})\nKura No: {$candidate->ticket_code}\nTelefon: {$candidate->phone}")
                            ->success()
                            ->persistent()
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

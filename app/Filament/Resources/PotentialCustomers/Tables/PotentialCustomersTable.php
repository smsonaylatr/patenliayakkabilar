<?php

namespace App\Filament\Resources\PotentialCustomers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class PotentialCustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('product.name')
                    ->label('İlgilendiği Ürün')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->copyable(),
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\SelectColumn::make('status')
                    ->label('Durum')
                    ->options([
                        'new' => 'Yeni',
                        'contacted' => 'İletişime Geçildi',
                        'closed' => 'Satışa Döndü / Kapatıldı',
                    ])
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'new' => 'Yeni',
                        'contacted' => 'İletişime Geçildi',
                        'closed' => 'Satışa Döndü / Kapatıldı',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (\App\Models\PotentialCustomer $record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->phone) . '?text=' . urlencode("Merhaba, ilgilendiğiniz {$record->product->name} ürünü hakkında bilgi vermek için ulaşıyoruz. Ürünü incelemek için: " . route('products.show', $record->product->slug)))
                    ->openUrlInNewTab(),
                \Filament\Actions\Action::make('sendSms')
                    ->label('SMS')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('SMS Gönder')
                    ->modalDescription('Müşteriye ürün linki içeren bir SMS gönderilecektir.')
                    ->modalSubmitActionLabel('Gönder')
                    ->action(function (\App\Models\PotentialCustomer $record) {
                        // TODO: NetGSM Service Integration
                        $record->update(['status' => 'contacted']);
                        \Filament\Notifications\Notification::make()
                            ->title('SMS Gönderildi (Simülasyon)')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\Action::make('call')
                    ->label('Ara (NetSantral)')
                    ->icon('heroicon-o-phone')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('NetSantral ile Ara')
                    ->modalDescription('NetSantral API üzerinden müşteri aranacaktır.')
                    ->modalSubmitActionLabel('Aramayı Başlat')
                    ->action(function (\App\Models\PotentialCustomer $record) {
                        // TODO: NetSantral API Integration
                        $record->update(['status' => 'contacted']);
                        \Filament\Notifications\Notification::make()
                            ->title('Arama Başlatıldı (Simülasyon)')
                            ->success()
                            ->send();
                    }),
                EditAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

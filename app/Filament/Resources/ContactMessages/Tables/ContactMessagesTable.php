<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Gönderen')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('subject')
                    ->label('Konu')
                    ->searchable(),
                \Filament\Tables\Columns\IconColumn::make('is_read')
                    ->label('Okundu mu?')
                    ->boolean()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Okunma Durumu')
                    ->placeholder('Tümü')
                    ->trueLabel('Okunanlar')
                    ->falseLabel('Okunmayanlar'),
            ])
            ->recordActions([
                ViewAction::make(),
                \Filament\Actions\Action::make('markAsRead')
                    ->label('Okundu İşaretle')
                    ->icon('heroicon-o-check')
                    ->action(fn (ContactMessage $record) => $record->update(['is_read' => true]))
                    ->visible(fn (ContactMessage $record) => !$record->is_read),
                \Filament\Actions\Action::make('markAsUnread')
                    ->label('Okunmadı İşaretle')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->is_read)
                    ->action(fn ($record) => $record->update(['is_read' => false])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

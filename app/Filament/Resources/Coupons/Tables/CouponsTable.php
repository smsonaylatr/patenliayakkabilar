<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kupon Kodu')
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'percentage' => 'Yüzde',
                        'fixed' => 'Sabit',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('value')
                    ->label('Değer')
                    ->getStateUsing(fn ($record) => $record->type === 'percentage' ? "%{$record->value}" : number_format($record->value, 2) . ' ₺')
                    ->sortable(),
                TextColumn::make('min_cart_total')
                    ->label('Min. Sepet')
                    ->getStateUsing(fn ($record) => $record->min_cart_total ? number_format($record->min_cart_total, 2) . ' ₺' : 'Limit Yok')
                    ->sortable(),
                TextColumn::make('usage_limit')
                    ->label('Limit')
                    ->getStateUsing(fn ($record) => $record->usage_limit ? "{$record->used_count} / {$record->usage_limit}" : "{$record->used_count} / Sınırsız")
                    ->sortable(['used_count', 'usage_limit']),
                TextColumn::make('expires_at')
                    ->label('Bitiş Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('Aktiflik Durumu')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

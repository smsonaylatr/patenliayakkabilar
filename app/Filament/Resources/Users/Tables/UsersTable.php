<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('score'))
            ->columns([
                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                TextColumn::make('score.overall_score')
                    ->label('Skor')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        !$record->score => 'gray',
                        $record->score->overall_score >= 70 => 'success',
                        $record->score->overall_score >= 40 => 'warning',
                        default => 'danger',
                    })
                    ->default('-')
                    ->sortable(query: fn ($query, string $direction) =>
                        $query->leftJoin('customer_scores', 'users.id', '=', 'customer_scores.user_id')
                            ->orderBy('customer_scores.activity_score', $direction)
                            ->select('users.*')
                    ),
                TextColumn::make('score.tier')
                    ->label('Segment')
                    ->badge()
                    ->color(fn ($record) => $record->score?->tier_color ?? 'gray')
                    ->default('Yeni'),
                TextColumn::make('score.lifetime_value')
                    ->label('LTV')
                    ->getStateUsing(fn ($record) => $record->score
                        ? number_format($record->score->lifetime_value, 2) . ' ₺'
                        : '-')
                    ->sortable(query: fn ($query, string $direction) =>
                        $query->leftJoin('customer_scores', 'users.id', '=', 'customer_scores.user_id')
                            ->orderBy('customer_scores.lifetime_value', $direction)
                            ->select('users.*')
                    ),
                TextColumn::make('score.risk_score')
                    ->label('Risk')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        !$record->score => 'gray',
                        $record->score->risk_score >= 70 => 'danger',
                        $record->score->risk_score >= 40 => 'warning',
                        default => 'success',
                    })
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state) => $state === 'admin' ? 'danger' : 'info')
                    ->formatStateUsing(fn (string $state) => $state === 'admin' ? 'Admin' : 'Müşteri'),
                TextColumn::make('orders_count')
                    ->label('Sipariş')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Müşteri',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

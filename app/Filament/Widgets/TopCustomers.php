<?php

namespace App\Filament\Widgets;

use App\Models\CustomerScore;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopCustomers extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'En Değerli 5 Müşteri (LTV)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CustomerScore::query()
                    ->with('user')
                    ->orderByDesc('lifetime_value')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Müşteri')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_orders')
                    ->label('Sipariş')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('lifetime_value')
                    ->label('Yaşam Boyu Değeri (LTV)')
                    ->money('try')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('loyalty_score')
                    ->label('Sadakat')
                    ->numeric()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}

<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\Pages\ListAccountingEntries;
use App\Models\AccountingEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class AccountingEntryResource extends Resource
{
    protected static ?string $model = AccountingEntry::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Satışlar';
    protected static ?string $modelLabel = 'Muhasebe Kaydı';
    protected static ?string $pluralModelLabel = 'Muhasebe';
    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('TARİH')
                    ->sortable()
                    ->dateTime('d.m.Y H:i'),

                TextColumn::make('type')
                    ->label('TİP')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'sale' => 'success',
                        'refund' => 'danger',
                        'shipping' => 'info',
                        'discount' => 'warning',
                        'adjustment' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => AccountingEntry::TYPES[$state] ?? $state),

                TextColumn::make('reference')
                    ->label('SİPARİŞ NO')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('description')
                    ->label('AÇIKLAMA')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('TUTAR')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => ($state >= 0 ? '+' : '') . number_format($state, 2, ',', '.') . ' ₺'),

                TextColumn::make('return_reason')
                    ->label('İADE NEDENİ')
                    ->formatStateUsing(fn (?string $state) => $state ? (AccountingEntry::RETURN_REASONS[$state] ?? $state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('note')
                    ->label('NOT')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('İŞLEMİ YAPAN')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('İşlem Tipi')
                    ->options(AccountingEntry::TYPES)
                    ->native(false),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('Başlangıç')
                            ->native(false),
                        DatePicker::make('until')
                            ->label('Bitiş')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Başlangıç: ' . \Carbon\Carbon::parse($data['from'])->format('d.m.Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Bitiş: ' . \Carbon\Carbon::parse($data['until'])->format('d.m.Y');
                        }
                        return $indicators;
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountingEntries::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

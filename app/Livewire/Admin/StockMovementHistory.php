<?php

namespace App\Livewire\Admin;

use App\Models\StockMovement;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class StockMovementHistory extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(StockMovement::query()->with(['product', 'variant', 'user']))
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Ürün'),
                Tables\Columns\TextColumn::make('variant.size')
                    ->label('Varyant'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sale' => 'Satış',
                        'cancel' => 'İptal',
                        'restock' => 'Stok Yenileme',
                        'adjustment' => 'Manuel Düzeltme',
                        'return' => 'İade',
                        'sync' => 'Senkronizasyon',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'danger',
                        'cancel' => 'warning',
                        'restock' => 'success',
                        'adjustment' => 'info',
                        'return' => 'primary',
                        'sync' => 'gray',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('old_stock')
                    ->label('Eski Stok'),
                Tables\Columns\TextColumn::make('new_stock')
                    ->label('Yeni Stok'),
                Tables\Columns\TextColumn::make('delta')
                    ->label('Değişim')
                    ->getStateUsing(fn ($record) => $record->new_stock - $record->old_stock)
                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}" : (string) $state)
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    ->badge(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Referans')
                    ->searchable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Not')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('İşlemi Yapan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->native(false),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Hareket Tipi')
                    ->options(StockMovement::TYPES)
                    ->native(false),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç Tarihi'),
                        \Filament\Forms\Components\DatePicker::make('to')
                            ->label('Bitiş Tarihi'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('variant.size')
                    ->label('Beden')
                    ->options(array_combine(range(28, 45), range(28, 45)))
                    ->attribute('variant.size')
                    ->multiple()
                    ->native(false),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export')
                    ->label('Dışa Aktar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        $movements = \App\Models\StockMovement::with(['product', 'variant', 'user'])
                            ->orderByDesc('created_at')
                            ->limit(5000)
                            ->get();

                        $csv = "Tarih;Ürün;Beden;Tip;Eski Stok;Yeni Stok;Değişim;Referans;Not;İşlemi Yapan\n";
                        foreach ($movements as $m) {
                            $delta = $m->new_stock - $m->old_stock;
                            $csv .= implode(';', [
                                $m->created_at->format('d.m.Y H:i'),
                                $m->product?->name ?? '-',
                                $m->variant?->size ?? '-',
                                \App\Models\StockMovement::TYPES[$m->type] ?? $m->type,
                                $m->old_stock,
                                $m->new_stock,
                                ($delta > 0 ? "+{$delta}" : $delta),
                                $m->reference ?? '-',
                                str_replace(';', ',', $m->note ?? '-'),
                                $m->user?->name ?? 'Sistem',
                            ]) . "\n";
                        }

                        return response()->streamDownload(function () use ($csv) {
                            echo "\xEF\xBB\xBF" . $csv; // UTF-8 BOM for Excel
                        }, 'stok-hareketleri-' . now()->format('Y-m-d') . '.csv');
                    })
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10);
    }

    public function render()
    {
        return view('livewire.admin.stock-movement-history');
    }
}

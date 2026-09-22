<?php

namespace App\Filament\Resources\MailLogs\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MailLog;

class MailLogsTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->query(MailLog::query())
            ->columns([
                TextColumn::make('to_email')
                    ->label('Alıcı')
                    ->searchable(),
                
                TextColumn::make('subject')
                    ->label('Konu')
                    ->limit(50)
                    ->searchable(),
                
                TextColumn::make('mailable_class')
                    ->label('Mail Tipi')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'queued' => 'warning',
                        'sending' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sent' => 'Gönderildi',
                        'failed' => 'Başarısız',
                        'queued' => 'Kuyrukta',
                        'sending' => 'Gönderiliyor',
                        default => $state,
                    }),
                
                TextColumn::make('sent_at')
                    ->label('Gönderim Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                
                TextColumn::make('error_message')
                    ->label('Hata Mesajı')
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'sent' => 'Gönderildi',
                        'failed' => 'Başarısız',
                        'queued' => 'Kuyrukta',
                        'sending' => 'Gönderiliyor',
                    ]),
                
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Başlangıç Tarihi'),
                        DatePicker::make('created_until')->label('Bitiş Tarihi'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('clearOldLogs')
                    ->label('Tümünü Temizle (30+ Gün)')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function () {
                        // 30 günden eski logları silme işlemi
                        MailLog::where('created_at', '<', now()->subDays(30))->delete();
                    }),
            ]);
    }
}

<?php

namespace App\Filament\Resources\CannedVoiceMessages\Tables;

use App\Models\CannedVoiceMessage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CannedVoiceMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Şablon & Anons Başlığı')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (CannedVoiceMessage $record) => \Illuminate\Support\Str::limit($record->message, 65)),

                TextColumn::make('audio_preview')
                    ->label('Ses Kaydı Durumu')
                    ->getStateUsing(function (CannedVoiceMessage $record) {
                        $audioUrl = $record->audio_url;
                        if ($audioUrl) {
                            return new HtmlString('
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 800; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                        🎵 Yüklü
                                    </span>
                                    <audio controls preload="none" src="' . e($audioUrl) . '" style="height: 32px; width: 190px; outline: none;"></audio>
                                </div>
                            ');
                        }

                        return new HtmlString('
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25);">
                                ⚠️ Ses Kaydı Yok (Tarayıcı Konuşur)
                            </span>
                        ');
                    }),

                TextColumn::make('coupon_code')
                    ->label('Kupon Kodu')
                    ->badge()
                    ->color('warning')
                    ->default('—')
                    ->copyable(),

                TextColumn::make('action_button')
                    ->label('Buton')
                    ->getStateUsing(fn (CannedVoiceMessage $record) => $record->action_button ? ($record->action_button . ' (' . $record->action_url . ')') : '—')
                    ->limit(25),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Aktiflik Durumu')
                    ->trueLabel('Sadece Aktifler')
                    ->falseLabel('Pasifler'),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle / Ses Değiştir'),
                DeleteAction::make()->label('Sil'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}

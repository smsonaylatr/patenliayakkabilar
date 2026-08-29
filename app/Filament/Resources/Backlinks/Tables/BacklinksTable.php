<?php

namespace App\Filament\Resources\Backlinks\Tables;

use App\Models\Backlink;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BacklinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Kaynak / Site')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Backlink $record) => $record->domain),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Backlink::CATEGORIES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'directory' => 'gray',
                        'social_profile' => 'info',
                        'parenting_blog' => 'warning',
                        'sports_lifestyle' => 'success',
                        'forum_community' => 'primary',
                        'digital_pr' => 'danger',
                        'gift_guide' => 'secondary',
                        default => 'gray',
                    }),

                TextColumn::make('anchor_text')
                    ->label('Anchor Metni')
                    ->placeholder('-')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('domain_authority')
                    ->label('DA Puanı')
                    ->badge()
                    ->sortable()
                    ->color(fn (?int $state) => match (true) {
                        $state >= 70 => 'success',
                        $state >= 40 => 'warning',
                        $state > 0 => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?int $state) => $state ? "DA {$state}" : '-'),

                TextColumn::make('link_type')
                    ->label('Link Tipi')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => strtoupper($state))
                    ->color(fn (string $state) => match ($state) {
                        'dofollow' => 'success',
                        'nofollow' => 'gray',
                        'ugc' => 'info',
                        'sponsored' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Backlink::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'gray',
                        'contacted' => 'warning',
                        'published' => 'info',
                        'active_verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('backlink_url')
                    ->label('Canlı Link')
                    ->placeholder('Henüz Yok')
                    ->limit(25)
                    ->url(fn ($record) => $record->backlink_url, true),

                TextColumn::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Backlink::CATEGORIES)
                    ->native(false),

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(Backlink::STATUSES)
                    ->native(false),

                SelectFilter::make('link_type')
                    ->label('Link Tipi')
                    ->options(Backlink::LINK_TYPES)
                    ->native(false),
            ])
            ->recordActions([
                Action::make('open_site')
                    ->label('Siteye Git')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Backlink $record) => 'https://' . ltrim(str_replace(['https://', 'http://'], '', $record->domain), '/'))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('domain_authority', 'desc');
    }
}

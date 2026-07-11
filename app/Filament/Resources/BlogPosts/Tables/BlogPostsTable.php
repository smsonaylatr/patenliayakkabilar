<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Kapak')
                    ->disk('public')
                    ->square()
                    ->size(64)
                    ->extraImgAttributes(['class' => 'rounded-md shadow-sm']),
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->tooltip(fn ($record) => $record->title),
                TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Kopyalandı')
                    ->prefix('/blog/')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('status')
                    ->label('Durum')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Yayında')
                    ->falseLabel('Taslak'),
                TernaryFilter::make('seo_durumu')
                    ->label('SEO Durumu')
                    ->placeholder('Tümü')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('meta_title')->where('meta_title', '!=', ''),
                        false: fn ($query) => $query->where(fn ($q) => $q->whereNull('meta_title')->orWhere('meta_title', '')),
                    )
                    ->trueLabel('SEO Tam')
                    ->falseLabel('SEO Eksik'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz blog yazısı yok')
            ->emptyStateDescription('İlk blog yazınızı ekleyin ve SEO ile organik trafik kazanın.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}

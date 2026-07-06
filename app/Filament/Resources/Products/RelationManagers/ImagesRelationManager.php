<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $title = 'Ürün Görselleri';
    protected static ?string $modelLabel = 'Görsel';
    protected static ?string $pluralModelLabel = 'Görseller';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')
                    ->label('Görsel')
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->maxSize(20480)
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/pjpeg',
                        'image/png',
                        'image/webp',
                        'image/gif',
                        'image/bmp',
                        'image/avif',
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Görsel')
                    ->disk('public')
                    ->square()
                    ->size(80),
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Eklenme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Görsel Ekle')
                    ->mutateFormDataUsing(function (array $data): array {
                        $maxSort = $this->getOwnerRecord()->images()->max('sort_order') ?? -1;
                        $data['sort_order'] = $maxSort + 1;
                        return $data;
                    }),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

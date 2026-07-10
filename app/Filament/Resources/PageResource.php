<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $modelLabel = 'Kurumsal Sayfa';
    protected static ?string $pluralModelLabel = 'Kurumsal Sayfalar';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getModelLabel(): string
    {
        return 'Sayfa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kurumsal Sayfalar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'İçerik Yönetimi';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Tabs::make('Sayfa')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('İçerik')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Sayfa Başlığı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Adresi (Slug)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Page::class, 'slug', ignoreRecord: true),
                                    
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Yayında')
                                    ->default(true),
                                    
                                Forms\Components\RichEditor::make('content')
                                    ->label('Sayfa İçeriği')
                                    ->columnSpanFull()
                                    ->fileAttachmentsDirectory('pages')
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                            ])->columns(2),

                        \Filament\Schemas\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('SEO Başlık')
                                    ->maxLength(70)
                                    ->helperText(fn ($state) => 'Karakter: ' . mb_strlen($state ?? '') . '/70')
                                    ->hint('Boş bırakılırsa sayfa başlığı kullanılır')
                                    ->live(onBlur: true),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('SEO Açıklama')
                                    ->maxLength(160)
                                    ->helperText(fn ($state) => 'Karakter: ' . mb_strlen($state ?? '') . '/160')
                                    ->hint('Boş bırakılırsa içerikten türetilir')
                                    ->live(onBlur: true)
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('og_image')
                                    ->label('Paylaşım Görseli (OG Image)')
                                    ->image()
                                    ->directory('pages/og'),
                                Forms\Components\Toggle::make('is_indexable')
                                    ->label('Arama motorlarında indekslensin')
                                    ->default(true),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Kopyalandı')
                    ->prefix('/sayfa/')
                    ->limit(30),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('meta_title')
                    ->label('SEO')
                    ->limit(30)
                    ->placeholder('⚠️ Eksik')
                    ->color(fn ($state) => $state ? 'gray' : 'warning')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Yayında')
                    ->falseLabel('Taslak'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz kurumsal sayfa yok')
            ->emptyStateDescription('Hakkımızda, KVKK, İade Politikası gibi sayfaları buradan ekleyin.');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}

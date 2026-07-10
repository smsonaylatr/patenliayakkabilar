<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Kategori')
                    ->tabs([
                        Tab::make('Genel')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Kategori Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->label('URL (Slug)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Textarea::make('description')
                                    ->label('Açıklama')
                                    ->default(null)
                                    ->columnSpanFull(),
                                Select::make('parent_id')
                                    ->label('Üst Kategori')
                                    ->relationship('parent', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->default(null),
                                TextInput::make('sort_order')
                                    ->label('Sıralama')
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('status')
                                    ->label('Aktif')
                                    ->default(true),
                                FileUpload::make('image')
                                    ->label('Kategori Görseli')
                                    ->image()
                                    ->disk('public')
                                    ->directory('categories')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('SEO Başlık')
                                    ->maxLength(70)
                                    ->helperText(fn ($state) => 'Karakter: ' . mb_strlen($state ?? '') . '/70')
                                    ->hint('Boş bırakılırsa otomatik üretilir')
                                    ->live(onBlur: true),
                                Textarea::make('meta_description')
                                    ->label('SEO Açıklama')
                                    ->maxLength(160)
                                    ->helperText(fn ($state) => 'Karakter: ' . mb_strlen($state ?? '') . '/160')
                                    ->hint('Boş bırakılırsa otomatik üretilir')
                                    ->live(onBlur: true)
                                    ->columnSpanFull(),
                                TextInput::make('seo_h1')
                                    ->label('Özel H1 Başlık')
                                    ->hint('Boş bırakılırsa kategori adı kullanılır')
                                    ->maxLength(255),
                                FileUpload::make('og_image')
                                    ->label('Paylaşım Görseli (OG Image)')
                                    ->image()
                                    ->disk('public')
                                    ->directory('categories/og'),
                                RichEditor::make('seo_content')
                                    ->label('Kategori SEO Metni')
                                    ->hint('Sayfanın altında görünür, SEO için önemlidir')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold', 'italic', 'link',
                                        'h2', 'h3', 'bulletList', 'orderedList',
                                    ]),
                                Toggle::make('is_indexable')
                                    ->label('Arama motorlarında indekslensin')
                                    ->default(true),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }
}

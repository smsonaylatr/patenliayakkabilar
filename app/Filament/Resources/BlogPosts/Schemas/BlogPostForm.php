<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Blog Yazısı')
                    ->tabs([
                        Tab::make('İçerik')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Başlık')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->label('URL (Slug)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Textarea::make('excerpt')
                                    ->label('Özet')
                                    ->hint('Kısa özet - listede görünür')
                                    ->maxLength(500)
                                    ->default(null)
                                    ->columnSpanFull(),
                                RichEditor::make('content')
                                    ->label('İçerik')
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline', 'link',
                                        'h2', 'h3', 'bulletList', 'orderedList',
                                        'blockquote', 'codeBlock',
                                    ]),
                                FileUpload::make('image_path')
                                    ->label('Kapak Görseli')
                                    ->image()
                                    ->disk('public')
                                    ->directory('blog'),
                                TextInput::make('image_alt')
                                    ->label('Kapak Görseli Alt Metni')
                                    ->hint('Boş bırakılırsa başlık otomatik eklenir')
                                    ->maxLength(255),
                                TextInput::make('author_name')
                                    ->label('Yazar Adı')
                                    ->default('Patenli Ayakkabılar Editör Ekibi')
                                    ->maxLength(255),
                                DateTimePicker::make('published_at')
                                    ->label('Yayın Tarihi')
                                    ->default(now())
                                    ->native(false),
                                Toggle::make('status')
                                    ->label('Yayında')
                                    ->default(true),
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
                                FileUpload::make('og_image')
                                    ->label('Paylaşım Görseli (OG Image)')
                                    ->hint('Boş bırakılırsa ana kapak görseli kullanılır')
                                    ->image()
                                    ->disk('public')
                                    ->directory('blog/og'),
                                Toggle::make('is_indexable')
                                    ->label('Arama motorlarında indekslensin')
                                    ->default(true),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }
}

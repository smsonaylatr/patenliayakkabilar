<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Ürün Bilgileri')
                    ->tabs([
                        Tab::make('Genel Bilgiler')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Kategori seçiniz')
                                    ->default(null),

                                TextInput::make('name')
                                    ->label('Ürün Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                        if ($operation === 'edit') {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('URL Yolu')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                                    ->dehydrated(),

                                Textarea::make('short_description')
                                    ->label('Kısa Açıklama')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->default(null)
                                    ->columnSpanFull(),

                                RichEditor::make('description')
                                    ->label('Açıklama')
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ])
                                    ->default(null)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Fiyat & Stok')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextInput::make('price')
                                    ->label('Fiyat')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₺')
                                    ->minValue(0)
                                    ->step(0.01),

                                TextInput::make('discount_price')
                                    ->label('İndirimli Fiyat')
                                    ->numeric()
                                    ->prefix('₺')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->default(null)
                                    ->lt('price'),

                                TextInput::make('stock')
                                    ->label('Stok Miktarı')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),

                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->default(null),
                            ])
                            ->columns(2),

                        Tab::make('Özellikler')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Marka')
                                    ->maxLength(255)
                                    ->default(null),

                                Select::make('gender')
                                    ->label('Cinsiyet')
                                    ->options([
                                        'erkek'   => 'Erkek',
                                        'kadin'   => 'Kadın',
                                        'unisex'  => 'Unisex',
                                        'cocuk'   => 'Çocuk',
                                    ])
                                    ->placeholder('Cinsiyet seçiniz')
                                    ->default(null),

                                Select::make('age_group')
                                    ->label('Yaş Grubu')
                                    ->options([
                                        'yetiskin' => 'Yetişkin',
                                        'cocuk'    => 'Çocuk',
                                        'genc'     => 'Genç',
                                    ])
                                    ->placeholder('Yaş grubu seçiniz')
                                    ->default(null),

                                Toggle::make('status')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->inline(false),

                                Toggle::make('featured')
                                    ->label('Öne Çıkan')
                                    ->default(false)
                                    ->inline(false),

                                Toggle::make('best_seller')
                                    ->label('Çok Satan')
                                    ->default(false)
                                    ->inline(false),
                            ])
                            ->columns(2),

                        Tab::make('SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Başlık')
                                    ->maxLength(70)
                                    ->hint(fn (?string $state): string => ($state ? strlen($state) : 0) . '/70 karakter')
                                    ->live(onBlur: true)
                                    ->default(null)
                                    ->columnSpanFull(),

                                Textarea::make('meta_description')
                                    ->label('Meta Açıklama')
                                    ->maxLength(160)
                                    ->rows(3)
                                    ->hint(fn (?string $state): string => ($state ? strlen($state) : 0) . '/160 karakter')
                                    ->live(onBlur: true)
                                    ->default(null)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Varyantlar')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Repeater::make('variants')
                                    ->relationship()
                                    ->label('')
                                    ->schema([
                                        Select::make('color')
                                            ->label('Renk')
                                            ->options([
                                                'Beyaz' => 'Beyaz',
                                                'Siyah' => 'Siyah',
                                                'Kırmızı' => 'Kırmızı',
                                                'Mavi' => 'Mavi',
                                                'Pembe' => 'Pembe',
                                                'Yeşil' => 'Yeşil',
                                                'Mor' => 'Mor',
                                                'Turuncu' => 'Turuncu',
                                                'Gri' => 'Gri',
                                                'Lacivert' => 'Lacivert',
                                            ])
                                            ->searchable()
                                            ->required(),
                                        Select::make('size')
                                            ->label('Numara')
                                            ->options(
                                                collect(range(28, 45))->mapWithKeys(fn ($size) => [(string) $size => (string) $size])->toArray()
                                            )
                                            ->searchable()
                                            ->required(),
                                        Select::make('wheel_type')
                                            ->label('Teker Tipi')
                                            ->options([
                                                'single' => 'Tek Teker',
                                                'double' => 'Çift Teker',
                                                'quad' => 'Dört Teker',
                                                'led' => 'LED Tekerlekli',
                                            ])
                                            ->searchable(),
                                        TextInput::make('stock')
                                            ->label('Stok')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0),
                                        TextInput::make('price_extra')
                                            ->label('Fiyat Farkı (₺)')
                                            ->numeric()
                                            ->default(0)
                                            ->step(0.01),
                                        TextInput::make('sku')
                                            ->label('SKU'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Varyant Ekle')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => 
                                        ($state['color'] ?? '') . ' - ' . ($state['size'] ?? '') . ' (Stok: ' . ($state['stock'] ?? 0) . ')'
                                    )
                                    ->columnSpanFull(),
                            ]),


                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}

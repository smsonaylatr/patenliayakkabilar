<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\ProductVariant;
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
                                    ->afterStateUpdated(function (?string $state, Set $set) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('URL Yolu')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
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
                                        'erkek'       => 'Erkek',
                                        'kadin'       => 'Kadın',
                                        'erkek_cocuk' => 'Erkek Çocuk',
                                        'kiz_cocuk'   => 'Kız Çocuk',
                                        'unisex'      => 'Unisex',
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


                                \Filament\Schemas\Components\Section::make('Ürün Özellikleri')
                                    ->icon('heroicon-o-sparkles')
                                    ->description('Ürünün sahip olduğu özellikleri seçin. Boş bırakırsanız kayıt sırasında ürün adına göre otomatik tahmin edilir.')
                                    ->schema([
                                        \Filament\Schemas\Components\Actions::make([
                                            \Filament\Actions\Action::make('auto_fill_features')
                                                ->label('✅ Otomatik Doldur')
                                                ->icon('heroicon-o-check-circle')
                                                ->color('success')
                                                ->size('sm')
                                                ->action(function (Set $set) {
                                                    $set('feature_keys', [
                                                        'led_light',
                                                        'anti_slip',
                                                        'breathable',
                                                        'orthopedic',
                                                        'hidden_wheel',
                                                        'double_wheel',
                                                        'easy_remove',
                                                        'lightweight',
                                                        'rubber_sole',
                                                    ]);
                                                }),
                                        ])->columnSpanFull(),

                                        \Filament\Forms\Components\CheckboxList::make('feature_keys')
                                            ->label('')
                                            ->options(\App\Models\ProductFeature::getOptionsForSelect())
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull()
                                    ->collapsible(),
                            ])
                            ->columns(2),

                        Tab::make('SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                \Filament\Schemas\Components\Actions::make([
                                    \Filament\Actions\Action::make('generate_seo')
                                        ->label('🤖 SEO Otomatik Üret')
                                        ->icon('heroicon-o-sparkles')
                                        ->color('success')
                                        ->size('sm')
                                        ->requiresConfirmation()
                                        ->modalHeading('SEO Verileri Üretilsin mi?')
                                        ->modalDescription('Ürün bilgilerine göre meta başlık ve açıklama otomatik oluşturulacak. Mevcut değerler değiştirilecek.')
                                        ->action(function (Set $set, \Filament\Schemas\Components\Utilities\Get $get) {
                                            $name = $get('name');
                                            $categoryId = $get('category_id');
                                            $brand = $get('brand');
                                            $gender = $get('gender');
                                            $ageGroup = $get('age_group');
                                            $price = $get('discount_price') ?: $get('price');
                                            $shortDesc = $get('short_description');

                                            $category = $categoryId ? \App\Models\Category::find($categoryId)?->name : null;

                                            $genderLabel = match ($gender) {
                                                'erkek'       => 'Erkek',
                                                'kadin'       => 'Kadın',
                                                'erkek_cocuk' => 'Erkek Çocuk',
                                                'kiz_cocuk'   => 'Kız Çocuk',
                                                'unisex'      => 'Unisex',
                                                default       => null,
                                            };

                                            $ageLabel = match ($ageGroup) {
                                                'cocuk'    => 'Çocuk',
                                                'genc'     => 'Genç',
                                                'yetiskin' => 'Yetişkin',
                                                default    => null,
                                            };

                                            // Meta Title
                                            $suffix = '- Patenli Ayakkabılar';
                                            $middle = '';
                                            if ($category) {
                                                $middle = '| ' . $category . ' ';
                                            } elseif ($genderLabel) {
                                                $middle = '| ' . $genderLabel . ' Patenli Ayakkabı ';
                                            }
                                            $title = trim($name . ' ' . $middle . $suffix);
                                            if (mb_strlen($title) > 70) {
                                                $title = trim($name . ' ' . $suffix);
                                            }
                                            $set('meta_title', mb_substr($title, 0, 70));

                                            // Meta Description
                                            $parts = [];
                                            $intro = $name;
                                            if ($genderLabel) $intro .= ' ' . $genderLabel;
                                            if ($ageLabel && $ageLabel !== $genderLabel) $intro .= ' ' . $ageLabel;
                                            if ($brand) $intro .= ' ' . $brand;
                                            $intro .= ' patenli ayakkabı.';
                                            $parts[] = $intro;

                                            if ($price && $price > 0) {
                                                $parts[] = 'Fiyat: ' . number_format((float) $price, 0) . ' ₺.';
                                            }

                                            if ($shortDesc) {
                                                $firstSentence = Str::before($shortDesc, '.');
                                                if (mb_strlen($firstSentence) > 10 && mb_strlen($firstSentence) < 80) {
                                                    $parts[] = trim($firstSentence) . '.';
                                                }
                                            }

                                            $parts[] = '✅ Ücretsiz kargo, hızlı teslimat.';

                                            $desc = '';
                                            foreach ($parts as $part) {
                                                $candidate = $desc ? $desc . ' ' . $part : $part;
                                                if (mb_strlen($candidate) <= 160) {
                                                    $desc = $candidate;
                                                } else {
                                                    break;
                                                }
                                            }
                                            $set('meta_description', $desc);
                                        }),
                                ])->columnSpanFull(),



                                TextInput::make('meta_title')
                                    ->label('Meta Başlık')
                                    ->maxLength(70)
                                    ->hint(fn (?string $state): string => ($state ? mb_strlen($state) : 0) . '/70 karakter')
                                    ->hintColor(fn (?string $state): string => ($state && mb_strlen($state) > 60) ? 'warning' : 'gray')
                                    ->live(onBlur: true)
                                    ->placeholder('Boş bırakırsan kayıt sırasında otomatik üretilir')
                                    ->helperText('İdeal: 50-60 karakter. Anahtar kelime başta olmalı.')
                                    ->default(null)
                                    ->columnSpanFull(),

                                Textarea::make('meta_description')
                                    ->label('Meta Açıklama')
                                    ->maxLength(160)
                                    ->rows(3)
                                    ->hint(fn (?string $state): string => ($state ? mb_strlen($state) : 0) . '/160 karakter')
                                    ->hintColor(fn (?string $state): string => ($state && mb_strlen($state) > 150) ? 'warning' : 'gray')
                                    ->live(onBlur: true)
                                    ->placeholder('Boş bırakırsan kayıt sırasında otomatik üretilir')
                                    ->helperText('İdeal: 120-155 karakter. Fiyat, özellik ve CTA içermeli.')
                                    ->default(null)
                                    ->columnSpanFull(),

                                TextInput::make('canonical_url')
                                    ->label('Canonical URL')
                                    ->placeholder('Varsayılan: /urun/{slug}')
                                    ->helperText('Farklı bir URL\'de canonical göstermek istiyorsanız doldurun.')
                                    ->url()
                                    ->columnSpanFull(),

                                FileUpload::make('og_image')
                                    ->label('Paylaşım Görseli (OG Image)')
                                    ->image()
                                    ->directory('products/og')
                                    ->helperText('Sosyal medya paylaşımında kullanılır. Boşsa ilk ürün görseli kullanılır.'),

                                Toggle::make('is_indexable')
                                    ->label('Arama motorlarında indekslensin')
                                    ->default(true)
                                    ->helperText('Kapatırsanız bu ürün Google\'da görünmez.'),
                            ])
                            ->columns(2),

                        Tab::make('Varyantlar')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                \Filament\Schemas\Components\Actions::make([
                                    \Filament\Actions\Action::make('generate_series')
                                        ->label('📦 Seri Oluştur')
                                        ->icon('heroicon-o-squares-plus')
                                        ->color('success')
                                        ->size('lg')
                                        ->form([
                                            Select::make('series_color')
                                                ->label('Renk')
                                                ->options(ProductVariant::COLOR_OPTIONS)
                                                ->multiple()
                                                ->searchable()
                                                ->native(false)
                                                ->required()
                                                ->helperText('Birden fazla renk seçebilirsiniz'),
                                            Select::make('series_start')
                                                ->label('Başlangıç Numara')
                                                ->options(
                                                    collect(range(26, 37))->mapWithKeys(fn ($s) => [(string) $s => (string) $s])->toArray()
                                                )
                                                ->default('26')
                                                ->required(),
                                            Select::make('series_end')
                                                ->label('Bitiş Numara')
                                                ->options(
                                                    collect(range(26, 37))->mapWithKeys(fn ($s) => [(string) $s => (string) $s])->toArray()
                                                )
                                                ->default('34')
                                                ->required(),
                                            Select::make('series_wheel')
                                                ->label('Teker Tipi')
                                                ->options([
                                                    'single' => 'Tek Teker',
                                                    'double' => 'Çift Teker',
                                                    'quad' => 'Dört Teker',
                                                    'led' => 'LED Tekerlekli',
                                                ]),
                                            TextInput::make('series_price')
                                                ->label('Fiyat (₺)')
                                                ->numeric()
                                                ->required()
                                                ->prefix('₺')
                                                ->default(0),
                                            TextInput::make('series_discount')
                                                ->label('İndirimli Fiyat (₺)')
                                                ->numeric()
                                                ->prefix('₺')
                                                ->default(null),
                                            TextInput::make('series_koli')
                                                ->label('Koli Adedi')
                                                ->numeric()
                                                ->default(1)
                                                ->required()
                                                ->helperText('Her numara için stok = koli adedi. Örn: 2 koli = her numaradan 2 adet.')
                                                ->minValue(1),
                                        ])
                                        ->modalHeading('📦 Varyant Serisi Oluştur')
                                        ->modalDescription('Seçtiğiniz numara aralığında tüm varyantları otomatik oluşturur. SKU otomatik üretilir.')
                                        ->modalSubmitActionLabel('Seriyi Oluştur')
                                        ->action(function (array $data, Set $set, \Filament\Schemas\Components\Utilities\Get $get) {
                                            $existing = $get('variants') ?? [];
                                            $slug = \Illuminate\Support\Str::slug($get('name') ?: 'URUN');

                                            // Çoklu renk desteği
                                            $colors = $data['series_color'] ?? [];
                                            if (is_string($colors)) {
                                                $colors = [$colors];
                                            }
                                            $colorCode = collect($colors)
                                                ->map(fn ($c) => mb_strtoupper(mb_substr($c, 0, 2)))
                                                ->implode('-') ?: 'XX';

                                            $start = (int) $data['series_start'];
                                            $end = (int) $data['series_end'];

                                            if ($start > $end) {
                                                [$start, $end] = [$end, $start];
                                            }

                                            $newVariants = [];
                                            for ($size = $start; $size <= $end; $size++) {
                                                $sku = strtoupper($slug) . '-' . $colorCode . '-' . $size;

                                                $newVariants[] = [
                                                    'color'          => $colors,
                                                    'size'           => (string) $size,
                                                    'wheel_type'     => $data['series_wheel'] ?? null,
                                                    'price'          => $data['series_price'],
                                                    'discount_price' => $data['series_discount'] ?: null,
                                                    'stock'          => (int) ($data['series_koli'] ?? 1),
                                                    'sku'            => $sku,
                                                ];
                                            }

                                            $set('variants', array_merge($existing, $newVariants));
                                        }),
                                ])->columnSpanFull(),

                                Repeater::make('variants')
                                    ->relationship()
                                    ->label('')
                                    ->schema([
                                        Select::make('color')
                                            ->label('Renk')
                                            ->options(ProductVariant::COLOR_OPTIONS)
                                            ->multiple()
                                            ->searchable()
                                            ->native(false)
                                            ->required()
                                            ->helperText('Birden fazla renk seçebilirsiniz'),
                                        Select::make('size')
                                            ->label('Numara')
                                            ->options(
                                                collect(range(26, 37))->mapWithKeys(fn ($size) => [(string) $size => (string) $size])->toArray()
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
                                        TextInput::make('price')
                                            ->label('Fiyat')
                                            ->numeric()
                                            ->required()
                                            ->prefix('₺')
                                            ->minValue(0)
                                            ->step(0.01),
                                        TextInput::make('discount_price')
                                            ->label('İndirimli Fiyat')
                                            ->numeric()
                                            ->prefix('₺')
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->default(null),
                                        TextInput::make('stock')
                                            ->label('Stok')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0),
                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->disabled()
                                            ->dehydrated()
                                            ->helperText('Otomatik üretilir'),
                                    ])
                                    ->columns(4)
                                    ->defaultItems(0)
                                    ->addActionLabel('Tek Varyant Ekle')
                                    ->cloneable()
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => 
                                        (is_array($state['color'] ?? null) ? implode(' / ', $state['color']) : ($state['color'] ?? '')) . 
                                        ' - ' . ($state['size'] ?? '') . 
                                        ' | ' . number_format((float) ($state['price'] ?? 0), 0) . ' ₺' .
                                        ' (Stok: ' . ($state['stock'] ?? 0) . ')' .
                                        (($state['sku'] ?? null) ? ' [' . $state['sku'] . ']' : '')
                                    )
                                    ->columnSpanFull(),
                            ]),


                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}

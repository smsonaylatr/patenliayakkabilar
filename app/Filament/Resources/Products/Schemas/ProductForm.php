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
                                \Filament\Forms\Components\Actions::make([
                                    \Filament\Forms\Components\Actions\Action::make('generate_seo')
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
                                                'erkek'  => 'Erkek',
                                                'kadin'  => 'Kadın',
                                                'cocuk'  => 'Çocuk',
                                                'unisex' => 'Unisex',
                                                default  => null,
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

                                \Filament\Forms\Components\Placeholder::make('seo_preview')
                                    ->label('🔍 Google Önizleme')
                                    ->content(function (\Filament\Schemas\Components\Utilities\Get $get) {
                                        $title = $get('meta_title') ?: $get('name') . ' - Patenli Ayakkabılar';
                                        $desc = $get('meta_description') ?: 'Meta açıklama girilmedi...';
                                        $slug = $get('slug') ?: 'urun-adi';

                                        return new \Illuminate\Support\HtmlString(
                                            '<div style="font-family: Arial, sans-serif; max-width: 600px; padding: 16px; background: #1a1a2e; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">' .
                                            '<div style="font-size: 11px; color: #bdc1c6; margin-bottom: 4px;">patenliayakkabilar.com › urun › ' . e($slug) . '</div>' .
                                            '<div style="font-size: 18px; color: #8ab4f8; margin-bottom: 6px; line-height: 1.3;">' . e(mb_substr($title, 0, 70)) . '</div>' .
                                            '<div style="font-size: 13px; color: #bdc1c6; line-height: 1.5;">' . e(mb_substr($desc, 0, 160)) . '</div>' .
                                            '</div>'
                                        );
                                    })
                                    ->columnSpanFull(),

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

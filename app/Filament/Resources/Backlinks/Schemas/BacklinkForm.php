<?php

namespace App\Filament\Resources\Backlinks\Schemas;

use App\Models\Backlink;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BacklinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Backlink & Kaynak Bilgileri')
                    ->description('Bağlantı alınacak veya alınan kaynak web sitesi bilgileri.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label('Kaynak / Site Adı')
                                ->required()
                                ->placeholder('Örn: Anneysen Blog & Forum')
                                ->maxLength(255),
                            TextInput::make('domain')
                                ->label('Domain')
                                ->required()
                                ->placeholder('anneysen.com')
                                ->prefix('https://')
                                ->maxLength(255),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('category')
                                ->label('Kategori')
                                ->options(Backlink::CATEGORIES)
                                ->default('directory')
                                ->native(false)
                                ->required(),
                            Select::make('link_type')
                                ->label('Link Tipi')
                                ->options(Backlink::LINK_TYPES)
                                ->default('dofollow')
                                ->native(false)
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('target_url')
                                ->label('Sitemizdeki Hedef URL')
                                ->default('https://patenliayakkabilar.com')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('anchor_text')
                                ->label('Bağlantı Metni (Anchor Text)')
                                ->placeholder('Örn: Patenli Ayakkabılar veya patenli ayakkabı')
                                ->maxLength(255),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('domain_authority')
                                ->label('Domain Authority (DA Puanı)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(100)
                                ->placeholder('1 - 100')
                                ->suffix('/ 100'),
                            Select::make('status')
                                ->label('Durum')
                                ->options(Backlink::STATUSES)
                                ->default('pending')
                                ->native(false)
                                ->required(),
                        ]),
                    ]),

                Section::make('Yayın ve Doğrulama Bilgileri')
                    ->description('Yayınlanan canlı backlink adresi ve iletişim detayları.')
                    ->schema([
                        TextInput::make('backlink_url')
                            ->label('Canlı Backlink URL\'si')
                            ->url()
                            ->placeholder('https://anneysen.com/blog/cocuk-paten-tavsiyesi')
                            ->helperText('Yayınlanan sayfanın tam web adresi.'),
                        Grid::make(2)->schema([
                            TextInput::make('contact_name')
                                ->label('İletişim Kurulan Kişi / Editör')
                                ->placeholder('Ahmet Yılmaz'),
                            TextInput::make('contact_email')
                                ->label('İletişim E-Posta')
                                ->email()
                                ->placeholder('editor@domain.com'),
                        ]),
                        DateTimePicker::make('published_at')
                            ->label('Yayın Tarihi')
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Notlar & Outreach Stratejisi')
                            ->rows(3)
                            ->placeholder('Konuk yazarlık veya ürün inceleme detayı, tohumlama notları...'),
                    ]),
            ]);
    }
}

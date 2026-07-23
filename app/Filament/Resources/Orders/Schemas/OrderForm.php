<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sipariş Bilgileri')
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Sipariş Numarası')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated()
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Sipariş Durumu')
                            ->options([
                                'pending' => 'Beklemede',
                                'processing' => 'Hazırlanıyor',
                                'shipped' => 'Kargoda',
                                'delivered' => 'Teslim Edildi',
                                'cancelled' => 'İptal',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),

                        Select::make('payment_status')
                            ->label('Ödeme Durumu')
                            ->options([
                                'pending' => 'Beklemede',
                                'paid' => 'Ödendi',
                                'refunded' => 'İade Edildi',
                                'failed' => 'Başarısız',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),

                        Select::make('payment_method')
                            ->label('Ödeme Yöntemi')
                            ->options([
                                'credit_card' => 'Kredi Kartı',
                                'wire_transfer' => 'Havale/EFT',
                                'cash_on_delivery' => 'Kapıda Ödeme',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('ip_address')
                            ->label('IP Adresi')
                            ->disabled()
                            ->columnSpan('full'),
                    ]),

                Section::make('Kargo Bilgileri')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        Select::make('cargo_company')
                            ->label('Kargo Firması')
                            ->options([
                                'yurtici' => 'Yurtiçi Kargo',
                                'aras' => 'Aras Kargo',
                                'mng' => 'MNG Kargo',
                                'ptt' => 'PTT Kargo',
                                'ups' => 'UPS',
                                'other' => 'Diğer',
                            ])
                            ->native(false),

                        TextInput::make('cargo_tracking_code')
                            ->label('Kargo Takip Kodu')
                            ->maxLength(255),
                    ]),

                Section::make('Müşteri Bilgileri')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Kayıtlı Kullanıcı')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->default(null),

                        TextInput::make('customer_name')
                            ->label('Müşteri Adı')
                            ->maxLength(255),

                        TextInput::make('customer_phone')
                            ->label('Telefon')
                            ->tel()
                            ->telRegex('/^[0-9\s\+\-\(\)]+$/')
                            ->maxLength(255),

                        TextInput::make('customer_email')
                            ->label('E-posta')
                            ->email()
                            ->maxLength(255),
                    ]),

                Section::make('Teslimat Adresi')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        TextInput::make('shipping_city')
                            ->label('İl')
                            ->maxLength(255),

                        TextInput::make('shipping_district')
                            ->label('İlçe')
                            ->maxLength(255),

                        Textarea::make('shipping_address')
                            ->label('Adres')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Fatura Adresi')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('billing_city')
                            ->label('İl')
                            ->maxLength(255),

                        TextInput::make('billing_district')
                            ->label('İlçe')
                            ->maxLength(255),

                        Textarea::make('billing_address')
                            ->label('Adres')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Tutar Bilgileri')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Ara Toplam')
                            ->numeric()
                            ->required()
                            ->prefix('₺')
                            ->inputMode('decimal'),

                        TextInput::make('shipping_price')
                            ->label('Kargo Ücreti')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('₺')
                            ->inputMode('decimal'),

                        TextInput::make('discount_total')
                            ->label('İndirim Toplamı')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('₺')
                            ->inputMode('decimal'),

                        TextInput::make('grand_total')
                            ->label('Genel Toplam')
                            ->numeric()
                            ->required()
                            ->prefix('₺')
                            ->inputMode('decimal'),
                    ]),

                Section::make('Notlar')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Textarea::make('customer_note')
                            ->label('Müşteri Notu')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kupon Bilgileri')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kupon Kodu')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                        Select::make('type')
                            ->label('İndirim Tipi')
                            ->options([
                                'percentage' => 'Yüzde (%)',
                                'fixed' => 'Sabit Tutar (₺)'
                            ])
                            ->default('percentage')
                            ->native(false)
                            ->required(),
                        TextInput::make('value')
                            ->label('İndirim Değeri')
                            ->numeric()
                            ->required()
                            ->suffixIcon('heroicon-m-banknotes'),
                        TextInput::make('min_cart_total')
                            ->label('Minimum Sepet Tutarı')
                            ->numeric()
                            ->default(null)
                            ->suffixIcon('heroicon-m-shopping-cart'),
                        TextInput::make('usage_limit')
                            ->label('Kullanım Limiti')
                            ->numeric()
                            ->default(null)
                            ->hint('Boş bırakılırsa sınırsız kullanılır'),
                        TextInput::make('used_count')
                            ->label('Kullanım Sayısı')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ]),
                
                Section::make('Durum & Geçerlilik')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('expires_at')
                            ->label('Son Kullanma Tarihi')
                            ->native(false),
                        Toggle::make('status')
                            ->label('Kupon Aktif mi?')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}

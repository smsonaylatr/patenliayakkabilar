<?php

namespace App\Filament\Resources\PotentialCustomers\Schemas;

use Filament\Schemas\Schema;

class PotentialCustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('İlgilendiği Ürün')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->required(),
                \Filament\Forms\Components\TextInput::make('email')
                    ->label('E-posta')
                    ->email(),
                \Filament\Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options([
                        'new' => 'Yeni',
                        'contacted' => 'İletişime Geçildi',
                        'closed' => 'Satışa Döndü / Kapatıldı',
                    ])
                    ->required()
                    ->default('new'),
                \Filament\Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->columnSpanFull(),
            ]);
    }
}

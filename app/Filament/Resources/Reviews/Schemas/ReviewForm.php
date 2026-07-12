<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Ürün')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->label('Müşteri Adı')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('rating')
                    ->label('Puan')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
                Toggle::make('status')
                    ->label('Yayında')
                    ->required(),
                Textarea::make('comment')
                    ->label('Yorum')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}

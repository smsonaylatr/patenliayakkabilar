<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\TextInput::make('name')
                    ->label('Gönderen')
                    ->required()
                    ->maxLength(255),
                \Filament\Schemas\Components\TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->required()
                    ->maxLength(255),
                \Filament\Schemas\Components\TextInput::make('subject')
                    ->label('Konu')
                    ->maxLength(255),
                \Filament\Schemas\Components\Textarea::make('message')
                    ->label('Mesaj')
                    ->required()
                    ->columnSpanFull(),
                \Filament\Schemas\Components\Toggle::make('is_read')
                    ->label('Okundu mu?')
                    ->required(),
            ]);
    }
}

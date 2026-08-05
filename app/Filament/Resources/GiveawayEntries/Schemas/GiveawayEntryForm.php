<?php

namespace App\Filament\Resources\GiveawayEntries\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\Toggle;

class GiveawayEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ticket_code')
                    ->label('Kura Numarası')
                    ->disabled()
                    ->required(),
                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required(),
                TextInput::make('instagram_username')
                    ->label('Instagram Kullanıcı Adı')
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->required(),
                TextInput::make('shoe_size')
                    ->label('Numara/Beden')
                    ->required(),
                TextInput::make('city')
                    ->label('İl')
                    ->required(),
                TextInput::make('district')
                    ->label('İlçe')
                    ->required(),
                Textarea::make('address')
                    ->label('Açık Adres')
                    ->columnSpanFull(),
                Toggle::make('is_winner')
                    ->label('Kazanan (Talihli) Mı?'),
                TextInput::make('won_prize')
                    ->label('Kazanılan Ödül'),
                Toggle::make('kvkk_consent')
                    ->label('KVKK Onayı'),
                Toggle::make('sms_consent')
                    ->label('SMS İzni'),
            ]);
    }
}

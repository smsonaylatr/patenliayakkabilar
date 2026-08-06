<?php

namespace App\Filament\Resources\StockNotifications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StockNotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->native(false),

                Select::make('product_variant_id')
                    ->relationship('variant', 'size')
                    ->nullable()
                    ->native(false),

                TextInput::make('email')
                    ->email()
                    ->required(),

                TextInput::make('phone')
                    ->tel()
                    ->nullable(),

                Toggle::make('is_notified')
                    ->label('Bildirim Gönderildi mi?'),

                DateTimePicker::make('notified_at')
                    ->label('Bildirilme Tarihi'),
            ]);
    }
}

<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\Section::make('Mesaj Detayları')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('name')
                            ->label('Gönderen'),
                        \Filament\Infolists\Components\TextEntry::make('email')
                            ->label('E-posta'),
                        \Filament\Infolists\Components\TextEntry::make('subject')
                            ->label('Konu'),
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label('Tarih')
                            ->dateTime('d M Y, H:i'),
                        \Filament\Infolists\Components\TextEntry::make('message')
                            ->label('Mesaj İçeriği')
                            ->columnSpanFull(),
                        \Filament\Infolists\Components\IconEntry::make('is_read')
                            ->label('Okundu mu?')
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }
}

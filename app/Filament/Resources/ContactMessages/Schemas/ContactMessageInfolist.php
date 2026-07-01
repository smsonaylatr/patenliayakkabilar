<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mesaj Detayları')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Gönderen'),
                        TextEntry::make('email')
                            ->label('E-posta'),
                        TextEntry::make('subject')
                            ->label('Konu'),
                        TextEntry::make('created_at')
                            ->label('Tarih')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('message')
                            ->label('Mesaj İçeriği')
                            ->columnSpanFull(),
                        IconEntry::make('is_read')
                            ->label('Okundu mu?')
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }
}

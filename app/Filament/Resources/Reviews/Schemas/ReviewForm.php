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
                TextInput::make('product_id')
                    ->required()
                    ,
                TextInput::make('user_id')
                    
                    ->default(null),
                TextInput::make('rating')
                    ->required()
                    ,
                Textarea::make('comment')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->required(),
            ]);
    }
}

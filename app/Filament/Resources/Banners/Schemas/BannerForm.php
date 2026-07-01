<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->default(null),
                TextInput::make('subtitle')
                    ->default(null),
                FileUpload::make('image_path')
                    ->image()
                    ->required(),
                TextInput::make('button_text')
                    ->default(null),
                TextInput::make('link_url')
                    ->url()
                    ->default(null),
                TextInput::make('position')
                    ->required()
                    ->default('hero'),
                Toggle::make('status')
                    ->required(),
            ]);
    }
}

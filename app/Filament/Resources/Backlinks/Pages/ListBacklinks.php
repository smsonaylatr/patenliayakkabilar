<?php

namespace App\Filament\Resources\Backlinks\Pages;

use App\Filament\Resources\Backlinks\BacklinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBacklinks extends ListRecords
{
    protected static string $resource = BacklinkResource::class;

    public function mount(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('backlinks')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\BacklinkSeeder',
                    '--force' => true,
                ]);
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni Backlink Kaynağı Ekle'),
        ];
    }
}

<?php

namespace App\Filament\Resources\MailLogs;

use App\Models\MailLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class MailLogResource extends Resource
{
    protected static ?string $model = MailLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'E-Posta Logları';
    protected static ?string $navigationGroup = 'Site Yönetimi';
    protected static ?string $modelLabel = 'E-Posta Logu';
    protected static ?string $pluralModelLabel = 'E-Posta Logları';
    protected static ?int $navigationSort = 99;
    
    // Loglar sadece okunabilir olmalıdır, oluşturma kapatıldı
    public static function canCreate(): bool 
    { 
        return false; 
    }
    
    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return Tables\MailLogsTable::make($table);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMailLogs::route('/'),
        ];
    }
}

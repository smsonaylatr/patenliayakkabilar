<?php

namespace App\Filament\Exports;

use App\Models\StockMovement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StockMovementExporter extends Exporter
{
    protected static ?string $model = StockMovement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')->label('Tarih'),
            ExportColumn::make('product.name')->label('Ürün'),
            ExportColumn::make('variant.size')->label('Varyant'),
            ExportColumn::make('type')->label('Tip'),
            ExportColumn::make('old_stock')->label('Eski Stok'),
            ExportColumn::make('new_stock')->label('Yeni Stok'),
            ExportColumn::make('reference')->label('Referans'),
            ExportColumn::make('note')->label('Not'),
            ExportColumn::make('user.name')->label('İşlemi Yapan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Dışa aktarma işlemi tamamlandı ve ' . number_format($export->successful_rows) . ' kayıt aktarıldı.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' kayıt dışa aktarılamadı.';
        }

        return $body;
    }
}

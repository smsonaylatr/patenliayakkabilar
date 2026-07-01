<?php

namespace App\Filament\Widgets;

use App\Models\CustomerSegment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerSegmentOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $segments = CustomerSegment::where('is_active', true)->get();
        $stats = [];

        foreach ($segments as $segment) {
            $stats[] = Stat::make($segment->name, $segment->customer_count)
                ->description('Aktif müşteri segmenti')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary');
        }

        if (empty($stats)) {
            $stats[] = Stat::make('Segmentler', '0')
                ->description('Henüz segment oluşturulmadı')
                ->color('gray');
        }

        return $stats;
    }
}

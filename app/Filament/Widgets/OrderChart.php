<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OrderChart extends ChartWidget
{
    protected ?string $heading = 'Son 30 Gün - Sipariş & Ciro';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $days = collect(range(29, 0));

        $labels = $days->map(fn ($daysAgo) =>
            Carbon::today()->subDays($daysAgo)->format('d.m')
        )->toArray();

        $revenues = $days->map(fn ($daysAgo) =>
            (float) Order::whereDate('created_at', Carbon::today()->subDays($daysAgo))
                ->sum('grand_total')
        )->toArray();

        $orderCounts = $days->map(fn ($daysAgo) =>
            Order::whereDate('created_at', Carbon::today()->subDays($daysAgo))->count()
        )->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Ciro (₺)',
                    'data' => $revenues,
                    'backgroundColor' => 'rgba(255, 78, 0, 0.1)',
                    'borderColor' => '#ff4e00',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Sipariş Sayısı',
                    'data' => $orderCounts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'ticks' => [
                        'callback' => '(value) => value.toLocaleString("tr-TR") + " ₺"',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}

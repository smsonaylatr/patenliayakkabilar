<?php

namespace App\Filament\Widgets;

use App\Models\CustomerEvent;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueBySource extends ChartWidget
{
    protected ?string $heading = 'Gelir Kaynakları (UTM Source)';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $sources = CustomerEvent::select('utm_source', DB::raw('count(*) as total'))
            ->where('event_type', 'purchase')
            ->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $labels = $sources->pluck('utm_source')->toArray();
        $data = $sources->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Sipariş Sayısı',
                    'data' => empty($data) ? [0] : $data,
                    'backgroundColor' => ['#3b82f6', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316'],
                ],
            ],
            'labels' => empty($labels) ? ['Veri Yok'] : $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueBySource extends ChartWidget
{
    protected ?string $heading = 'Sipariş Kaynakları Dağılımı';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $sources = Order::select(
                DB::raw("COALESCE(NULLIF(traffic_source, ''), 'Doğrudan') as source"),
                DB::raw('count(*) as total')
            )
            ->groupBy('source')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $labels = $sources->pluck('source')->toArray();
        $data = $sources->pluck('total')->map(fn ($val) => (int) $val)->toArray();

        $colorMap = [
            'Google Ads' => '#3b82f6',
            'Google Organik' => '#10b981',
            'Instagram' => '#ec4899',
            'Instagram Ads' => '#f43f5e',
            'Facebook' => '#6366f1',
            'Meta Ads' => '#4f46e5',
            'TikTok' => '#334155',
            'TikTok Ads' => '#0f172a',
            'WhatsApp' => '#22c55e',
            'Admin Paneli' => '#a855f7',
            'Doğrudan' => '#94a3b8',
        ];

        $bgColors = [];
        foreach ($labels as $label) {
            $bgColors[] = $colorMap[$label] ?? '#64748b';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sipariş Sayısı',
                    'data' => empty($data) ? [0] : $data,
                    'backgroundColor' => empty($bgColors) ? ['#94a3b8'] : $bgColors,
                ],
            ],
            'labels' => empty($labels) ? ['Veri Yok'] : $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

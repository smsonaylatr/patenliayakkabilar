<?php

namespace App\Filament\Widgets;

use App\Models\CustomerScore;
use Filament\Widgets\ChartWidget;

class CustomerScoreDistribution extends ChartWidget
{
    protected ?string $heading = 'Müşteri Skor Dağılımı';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $critical = CustomerScore::where('risk_score', '>=', 80)->count();
        $high = CustomerScore::whereBetween('risk_score', [50, 79])->count();
        $medium = CustomerScore::whereBetween('risk_score', [20, 49])->count();
        $low = CustomerScore::where('risk_score', '<', 20)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Müşteri Sayısı',
                    'data' => [$critical, $high, $medium, $low],
                    'backgroundColor' => ['#ef4444', '#f97316', '#eab308', '#22c55e'],
                ],
            ],
            'labels' => ['Kritik Risk', 'Yüksek Risk', 'Orta Risk', 'Düşük Risk'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\CustomerScore;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerIntelligence extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = CustomerScore::where('days_since_last_activity', '<=', 30)->count();
        $atRiskCustomers = CustomerScore::where('risk_score', '>=', 60)->count();
        $vipCustomers = CustomerScore::where('lifetime_value', '>=', 500)->where('risk_score', '<', 30)->count();

        $avgLTV = CustomerScore::avg('lifetime_value') ?? 0;
        $avgChurn = CustomerScore::avg('predicted_churn_probability') ?? 0;

        return [
            Stat::make('Aktif Müşteriler', $activeCustomers . '/' . $totalCustomers)
                ->description('Son 30 günde aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Risk Altındaki Müşteriler', $atRiskCustomers)
                ->description('Kayıp riski yüksek')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($atRiskCustomers > 5 ? 'danger' : 'warning'),

            Stat::make('VIP Müşteriler', $vipCustomers)
                ->description('Yüksek değerli & aktif')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),

            Stat::make('Ort. Müşteri Değeri', number_format($avgLTV, 2) . ' ₺')
                ->description('Yaşam boyu değer ortalaması')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}

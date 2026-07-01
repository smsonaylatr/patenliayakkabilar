<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        // Son 7 günlük gerçek ciro verileri
        $dailyRevenues = collect(range(6, 0))->map(function ($daysAgo) {
            return (float) Order::whereDate('created_at', Carbon::today()->subDays($daysAgo))
                ->sum('grand_total');
        })->toArray();

        $todayRevenue = end($dailyRevenues);
        $yesterdayRevenue = $dailyRevenues[5] ?? 0;
        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100)
            : 0;

        // Son 7 günlük sipariş sayıları
        $dailyOrders = collect(range(6, 0))->map(function ($daysAgo) {
            return Order::whereDate('created_at', Carbon::today()->subDays($daysAgo))->count();
        })->toArray();

        // Aylık ciro
        $monthlyRevenue = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('grand_total');

        // Son 7 günlük müşteri kayıtları
        $dailyUsers = collect(range(6, 0))->map(function ($daysAgo) {
            return User::whereDate('created_at', Carbon::today()->subDays($daysAgo))->count();
        })->toArray();

        // Bekleyen siparişler
        $pendingOrders = Order::where('status', 'pending')->count();

        // Ortalama sipariş tutarı
        $avgOrderValue = Order::avg('grand_total') ?? 0;

        return [
            Stat::make('Bugünkü Ciro', number_format($todayRevenue, 2) . ' ₺')
                ->description($revenueChange >= 0 ? "Düne göre %{$revenueChange} artış" : "Düne göre %{$revenueChange} düşüş")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($dailyRevenues)
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Aylık Ciro', number_format($monthlyRevenue, 2) . ' ₺')
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Yeni Siparişler', end($dailyOrders))
                ->description('Bugün gelen siparişler')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart($dailyOrders)
                ->color('warning'),

            Stat::make('Bekleyen Siparişler', $pendingOrders)
                ->description('İşlem bekleyen')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 5 ? 'danger' : 'warning'),

            Stat::make('Toplam Müşteri', User::count())
                ->description('Kayıtlı toplam üye')
                ->descriptionIcon('heroicon-m-users')
                ->chart($dailyUsers)
                ->color('info'),

            Stat::make('Ort. Sipariş Tutarı', number_format($avgOrderValue, 2) . ' ₺')
                ->description('Ortalama sepet değeri')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray'),
        ];
    }
}

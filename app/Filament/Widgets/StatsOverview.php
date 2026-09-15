<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        // Dashboard istatistiklerini 120 saniye cache'le — 14+ sorgu her sayfa yüklemesinde çalışmasın
        $data = Cache::remember('dashboard_stats_overview', 120, function () {
            // Sadece ödenen ve iptal edilmemiş siparişler
            $activeOrders = fn () => Order::where('status', '!=', 'cancelled')->where('payment_status', 'paid');

            // Son 7 günlük gerçek ciro verileri
            $dailyRevenues = collect(range(6, 0))->map(function ($daysAgo) use ($activeOrders) {
                return (float) $activeOrders()->whereDate('created_at', Carbon::today()->subDays($daysAgo))
                    ->sum('grand_total');
            })->toArray();

            // Son 7 günlük sipariş sayıları (iptal hariç)
            $dailyOrders = collect(range(6, 0))->map(function ($daysAgo) use ($activeOrders) {
                return $activeOrders()->whereDate('created_at', Carbon::today()->subDays($daysAgo))->count();
            })->toArray();

            // Son 7 günlük müşteri kayıtları
            $dailyUsers = collect(range(6, 0))->map(function ($daysAgo) {
                return User::whereDate('created_at', Carbon::today()->subDays($daysAgo))->count();
            })->toArray();

            return [
                'dailyRevenues' => $dailyRevenues,
                'dailyOrders' => $dailyOrders,
                'dailyUsers' => $dailyUsers,
                'monthlyRevenue' => $activeOrders()->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('grand_total'),
                'pendingOrders' => Order::where('status', 'pending')->count(),
                'avgOrderValue' => $activeOrders()->avg('grand_total') ?? 0,
                'totalUsers' => User::count(),
            ];
        });

        $todayRevenue = end($data['dailyRevenues']);
        $yesterdayRevenue = $data['dailyRevenues'][5] ?? 0;
        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100)
            : 0;

        return [
            Stat::make('Bugünkü Ciro', number_format($todayRevenue, 2) . ' ₺')
                ->description($revenueChange >= 0 ? "Düne göre %{$revenueChange} artış" : "Düne göre %{$revenueChange} düşüş")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($data['dailyRevenues'])
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Aylık Ciro', number_format($data['monthlyRevenue'], 2) . ' ₺')
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Yeni Siparişler', end($data['dailyOrders']))
                ->description('Bugün gelen siparişler')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart($data['dailyOrders'])
                ->color('warning'),

            Stat::make('Bekleyen Siparişler', $data['pendingOrders'])
                ->description('İşlem bekleyen')
                ->descriptionIcon('heroicon-m-clock')
                ->color($data['pendingOrders'] > 5 ? 'danger' : 'warning'),

            Stat::make('Toplam Müşteri', $data['totalUsers'])
                ->description('Kayıtlı toplam üye')
                ->descriptionIcon('heroicon-m-users')
                ->chart($data['dailyUsers'])
                ->color('info'),

            Stat::make('Ort. Sipariş Tutarı', number_format($data['avgOrderValue'], 2) . ' ₺')
                ->description('Ortalama sepet değeri')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray'),
        ];
    }
}


<?php

namespace App\Filament\Resources\Accounting\Pages;

use App\Models\AccountingEntry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountingSummaryWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalSales = AccountingEntry::where('type', AccountingEntry::TYPE_SALE)->sum('amount');
        $totalRefunds = AccountingEntry::where('type', AccountingEntry::TYPE_REFUND)->sum('amount');
        $net = $totalSales + $totalRefunds; // refund negatif olduğu için toplama yapıyoruz

        // Bu ayın verileri
        $monthlySales = AccountingEntry::where('type', AccountingEntry::TYPE_SALE)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $monthlyRefunds = AccountingEntry::where('type', AccountingEntry::TYPE_REFUND)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $refundCount = AccountingEntry::where('type', AccountingEntry::TYPE_REFUND)->count();

        return [
            Stat::make('Toplam Satış', number_format($totalSales, 2, ',', '.') . ' ₺')
                ->description('Tüm zamanlar')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Toplam İade', number_format(abs($totalRefunds), 2, ',', '.') . ' ₺')
                ->description($refundCount . ' adet iade')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color('danger'),

            Stat::make('Net Gelir', number_format($net, 2, ',', '.') . ' ₺')
                ->description('Satış - İade')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color($net >= 0 ? 'success' : 'danger'),

            Stat::make('Bu Ay Satış', number_format($monthlySales, 2, ',', '.') . ' ₺')
                ->description('İade: ' . number_format(abs($monthlyRefunds), 2, ',', '.') . ' ₺')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
        ];
    }
}

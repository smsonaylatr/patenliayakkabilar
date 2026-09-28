<?php

namespace App\Filament\Resources\Segments\Widgets;

use App\Models\ActiveVisitor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerSegmentStatsWidget extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        try {
            $baseQuery = ActiveVisitor::query()->where('is_blocked', false)->where('visit_count', '>=', 2);

            $totalRepeat = (clone $baseQuery)->count();
            $threeStars = (clone $baseQuery)->where('visit_count', '>=', 3)->count();
            $twoStars = (clone $baseQuery)->where('visit_count', 2)->count();

            $cartVisitors = (clone $baseQuery)->where('cart_items_count', '>', 0);
            $cartTotal = (float) $cartVisitors->sum('cart_total');
            $cartCount = $cartVisitors->count();

            $highIntentCount = (clone $baseQuery)->where('intent_score', '>=', 60)->count();

            $reachableCount = (clone $baseQuery)->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('guest_phone')->where('guest_phone', '!=', '');
                })->orWhereHas('user', function ($uq) {
                    $uq->whereNotNull('phone')->where('phone', '!=', '');
                });
            })->count();

            return [
                Stat::make('⭐ 2-3 Yıldızlı Müşteriler', number_format($totalRepeat))
                    ->description('Siteye 2+ kez gelen sadık kitle')
                    ->descriptionIcon('heroicon-m-sparkles')
                    ->color('warning'),

                Stat::make('⭐⭐⭐ 3 Yıldız (Müdavim)', number_format($threeStars))
                    ->description('3 veya daha fazla ziyaret edenler')
                    ->descriptionIcon('heroicon-m-star')
                    ->color('success'),

                Stat::make('⭐⭐ 2 Yıldız (İkinci Geliş)', number_format($twoStars))
                    ->description('Geri dönen potansiyel alıcılar')
                    ->descriptionIcon('heroicon-m-arrow-path')
                    ->color('primary'),

                Stat::make('🛒 Sepette Bekleyen Tutar', number_format($cartTotal, 2) . ' ₺')
                    ->description($cartCount . ' sadık ziyaretçinin sepetinde')
                    ->descriptionIcon('heroicon-m-shopping-cart')
                    ->color('danger'),

                Stat::make('🔥 Sıcak Adaylar', number_format($highIntentCount))
                    ->description('Niyet skoru %60 ve üzeri')
                    ->descriptionIcon('heroicon-m-fire')
                    ->color('primary'),

                Stat::make('💬 WhatsApp İletişim', number_format($reachableCount) . ' Müşteri')
                    ->description('Telefonu kayıtlı, hemen mesaj atılabilir')
                    ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                    ->color('success'),
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }
}

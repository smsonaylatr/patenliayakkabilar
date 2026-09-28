<?php

namespace App\Filament\Resources\Segments\Pages;

use App\Filament\Resources\Segments\SegmentResource;
use App\Filament\Resources\Segments\Widgets\CustomerSegmentStatsWidget;
use App\Models\ActiveVisitor;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSegments extends ListRecords
{
    protected static string $resource = SegmentResource::class;

    public function getHeading(): string
    {
        return 'Müşteri Segmentleri (2-3 Yıldızlı Sadık Ziyaretçiler)';
    }

    public function getSubheading(): ?string
    {
        return 'Siteyi birden fazla kez ziyaret eden, yüksek ilgi ve satın alma eğilimi gösteren 2 ve 3 yıldızlı potansiyel alıcılar.';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerSegmentStatsWidget::class,
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all_stars';
    }

    public function getTabs(): array
    {
        try {
            $baseQuery = ActiveVisitor::query()->where('is_blocked', false)->where('visit_count', '>=', 2);

            $allCount = (clone $baseQuery)->count();
            $threeCount = (clone $baseQuery)->where('visit_count', '>=', 3)->count();
            $twoCount = (clone $baseQuery)->where('visit_count', 2)->count();
            $cartCount = (clone $baseQuery)->where('cart_items_count', '>', 0)->count();
            $intentCount = (clone $baseQuery)->where('intent_score', '>=', 60)->count();
            $memberCount = (clone $baseQuery)->whereNotNull('user_id')->count();

            return [
                'all_stars' => Tab::make('⭐ Tüm 2-3 Yıldızlılar')
                    ->icon('heroicon-m-sparkles')
                    ->badge($allCount)
                    ->badgeColor('warning')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('visit_count', '>=', 2)),

                'three_stars' => Tab::make('⭐⭐⭐ 3 Yıldız (Müdavimler)')
                    ->icon('heroicon-m-star')
                    ->badge($threeCount)
                    ->badgeColor('success')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('visit_count', '>=', 3)),

                'two_stars' => Tab::make('⭐⭐ 2 Yıldız (İkinci Geliş)')
                    ->icon('heroicon-m-arrow-path')
                    ->badge($twoCount)
                    ->badgeColor('primary')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('visit_count', 2)),

                'with_cart' => Tab::make('🛒 Sepeti Dolu Olanlar')
                    ->icon('heroicon-m-shopping-cart')
                    ->badge($cartCount)
                    ->badgeColor('danger')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('visit_count', '>=', 2)->where('cart_items_count', '>', 0)),

                'high_intent' => Tab::make('🔥 Sıcak Adaylar (%60+)')
                    ->icon('heroicon-m-fire')
                    ->badge($intentCount)
                    ->badgeColor('primary')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('visit_count', '>=', 2)->where('intent_score', '>=', 60)),

                'members' => Tab::make('👑 Kayıtlı Üyeler')
                    ->icon('heroicon-m-user')
                    ->badge($memberCount)
                    ->badgeColor('info')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('visit_count', '>=', 2)->whereNotNull('user_id')),
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }
}

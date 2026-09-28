<?php

namespace App\Filament\Resources\Segments\Pages;

use App\Filament\Resources\Segments\SegmentResource;
use App\Filament\Resources\Segments\Widgets\CustomerSegmentStatsWidget;
use App\Models\ActiveVisitor;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp_campaign')
                ->label('🟢 WhatsApp Pazarlama Kampanyası')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->modalHeading('🟢 Segment Odaklı WhatsApp Pazarlama Kampanyası')
                ->modalDescription('2 ve 3 yıldızlı müşterilere özel hazırlanmış WhatsApp mesajlarını tek tıkla gönderin veya toplu numaraları dışa aktarın.')
                ->modalWidth(Width::FourExtraLarge)
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Kapat')
                ->modalContent(function () {
                    $visitors = ActiveVisitor::with(['user'])
                        ->where('is_blocked', false)
                        ->where('visit_count', '>=', 2)
                        ->where(function ($q) {
                            $q->where(function ($sq) {
                                $sq->whereNotNull('guest_phone')->where('guest_phone', '!=', '');
                            })->orWhereHas('user', function ($uq) {
                                $uq->whereNotNull('phone')->where('phone', '!=', '');
                            });
                        })
                        ->orderByDesc('cart_items_count')
                        ->orderByDesc('visit_count')
                        ->get();

                    return view('filament.resources.segments.whatsapp-campaign-modal', [
                        'visitors' => $visitors,
                    ]);
                }),
        ];
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

            $phoneCount = (clone $baseQuery)->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('guest_phone')->where('guest_phone', '!=', '');
                })->orWhereHas('user', function ($uq) {
                    $uq->whereNotNull('phone')->where('phone', '!=', '');
                });
            })->count();

            return [
                'all_stars' => Tab::make('⭐ Tüm 2-3 Yıldızlılar')
                    ->icon('heroicon-m-sparkles')
                    ->badge($allCount)
                    ->badgeColor('warning')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('visit_count', '>=', 2)),

                'whatsapp_ready' => Tab::make('💬 WhatsApp İletişim')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->badge($phoneCount)
                    ->badgeColor('success')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where(function ($q) {
                        $q->where(function ($sq) {
                            $sq->whereNotNull('guest_phone')->where('guest_phone', '!=', '');
                        })->orWhereHas('user', function ($uq) {
                            $uq->whereNotNull('phone')->where('phone', '!=', '');
                        });
                    })),

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

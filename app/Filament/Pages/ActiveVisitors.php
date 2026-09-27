<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CannedVoiceMessages\CannedVoiceMessageResource;
use App\Models\ActiveVisitor;
use App\Models\CannedVoiceMessage;
use App\Models\Cart;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ActiveVisitors extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'canli-ziyaretciler';

    protected static ?int $navigationSort = 1;

    public string $trafficPeriod = 'daily';

    public string $activeCardFilter = 'all';

    public string $viewMode = 'list';

    public string $activeTab = 'live';

    public function mount(): void
    {
        $saved = request()->cookie('av_view_mode', session('av_view_mode', 'list'));
        $this->viewMode = in_array($saved, ['list', 'grid']) ? $saved : 'list';

        $tab = request()->query('tab');
        if (in_array($tab, ['live', 'recent'])) {
            $this->activeTab = $tab;
        }
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['live', 'recent'])) {
            $this->activeTab = $tab;
            $this->activeCardFilter = 'all';
            $this->resetTable();
        }
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['list', 'grid'])) {
            $this->viewMode = $mode;
            session(['av_view_mode' => $mode]);
            \Illuminate\Support\Facades\Cookie::queue('av_view_mode', $mode, 60 * 24 * 365);
        }
    }

    public function setTrafficPeriod(string $period): void
    {
        if (in_array($period, ['daily', 'weekly', 'monthly'])) {
            $this->trafficPeriod = $period;
        }
    }

    public function setCardFilter(string $filter): void
    {
        $this->activeCardFilter = ($this->activeCardFilter === $filter) ? 'all' : $filter;
    }

    public function getView(): string
    {
        return 'filament.pages.active-visitors';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-signal';
    }

    public static function getNavigationLabel(): string
    {
        return 'Canlı Ziyaretçiler';
    }

    public function getTitle(): string
    {
        return 'Canlı Ziyaretçi & Satış Dönüşüm Merkezi';
    }

    public static function canAccess(): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('active_visitors');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Müşteriler';
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('active_visitors')) {
                return null;
            }
            $count = ActiveVisitor::online()->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public function getViewData(): array
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('active_visitors')) {
                return [
                    'onlineCount' => 0,
                    'todayVisitorsCount' => 0,
                    'highIntentCount' => 0,
                    'liveHighIntentCount' => 0,
                    'cartCount' => 0,
                    'cartTotal' => 0,
                    'liveCartCount' => 0,
                    'hesitatingCount' => 0,
                    'liveHesitatingCount' => 0,
                    'membersCount' => 0,
                    'guestsCount' => 0,
                    'loginRate' => 0,
                    'totalActive' => 0,
                    'blockedCount' => 0,
                    'activeCardFilter' => 'all',
                ];
            }

            // 1. Canlı ve Dönemlik Ziyaretçi Sorguları
            $onlineQuery = ActiveVisitor::online()->where('is_blocked', false);
            $onlineCount = (clone $onlineQuery)->count();

            $todayRadarQuery = ActiveVisitor::query()
                ->where('last_heartbeat_at', '>=', now()->startOfDay())
                ->where('is_blocked', false);

            $recentRadarQuery = ActiveVisitor::query()
                ->where('last_heartbeat_at', '>=', now()->subHours(24))
                ->where('is_blocked', false);

            $recentVisitorsCount = (clone $recentRadarQuery)->count();
            $todayVisitorsCount = (clone $todayRadarQuery)->count();

            // 2. Bekleyen Sepetler (Canlı & Bugünkü/24s & DB Carts)
            $liveCartQuery = (clone $onlineQuery)->where('cart_items_count', '>', 0);
            $liveCartCount = (clone $liveCartQuery)->count();
            $liveCartTotal = (float) (clone $liveCartQuery)->sum('cart_total');

            $recentCartQuery = (clone $recentRadarQuery)->where('cart_items_count', '>', 0);
            $recentCartCount = (clone $recentCartQuery)->count();
            $recentCartTotal = (float) (clone $recentCartQuery)->sum('cart_total');

            $dbPendingCartsCount = 0;
            $dbPendingCartsTotal = 0.0;
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('carts')) {
                    $dbPendingCartsQuery = \App\Models\Cart::whereHas('items')->where('updated_at', '>=', now()->subDays(7));
                    $dbPendingCartsCount = (clone $dbPendingCartsQuery)->count();
                    $dbPendingCartsTotal = (float) (clone $dbPendingCartsQuery)->with('items')->get()->sum(fn($c) => $c->items->sum(fn($i) => ($i->price ?? 0) * ($i->quantity ?? 1)));
                }
            } catch (\Throwable $e) {}

            $cartCount = $liveCartCount > 0 ? $liveCartCount : ($recentCartCount > 0 ? $recentCartCount : $dbPendingCartsCount);
            $cartTotal = $liveCartCount > 0 ? $liveCartTotal : ($recentCartTotal > 0 ? $recentCartTotal : $dbPendingCartsTotal);

            // 3. Sıcak Adaylar (%60 ve üzeri niyet)
            $liveHighIntentCount = (clone $onlineQuery)->where('intent_score', '>=', 60)->count();
            $recentHighIntentCount = (clone $recentRadarQuery)->where('intent_score', '>=', 60)->count();
            $highIntentCount = $liveHighIntentCount > 0 ? $liveHighIntentCount : $recentHighIntentCount;

            // 4. Tereddütte Olanlar (Beden/kargo bariyeri)
            $liveHesitatingCount = (clone $onlineQuery)->where('intent_level', 'hesitating')->count();
            $recentHesitatingCount = (clone $recentRadarQuery)->where('intent_level', 'hesitating')->count();
            $hesitatingCount = $liveHesitatingCount > 0 ? $liveHesitatingCount : $recentHesitatingCount;

            // 5. Kullanıcı Segmenti (Üye / Misafir Dağılımı)
            $liveMembersCount = (clone $onlineQuery)->where(function ($q) {
                $q->whereNotNull('user_id')->orWhere('is_identified', true);
            })->count();
            $liveGuestsCount = max(0, $onlineCount - $liveMembersCount);

            $recentMembersCount = (clone $recentRadarQuery)->where(function ($q) {
                $q->whereNotNull('user_id')->orWhere('is_identified', true);
            })->count();
            $recentGuestsCount = max(0, $recentVisitorsCount - $recentMembersCount);

            $membersCount = $onlineCount > 0 ? $liveMembersCount : $recentMembersCount;
            $guestsCount = $onlineCount > 0 ? $liveGuestsCount : $recentGuestsCount;
            $totalActive = $membersCount + $guestsCount;
            $loginRate = $totalActive > 0 ? round(($membersCount / $totalActive) * 100) : 0;

            $blockedCount = ActiveVisitor::where('is_blocked', true)->count();
            $recentLeftCount = ActiveVisitor::where('last_heartbeat_at', '<', now()->subSeconds(75))
                ->where('last_heartbeat_at', '>=', now()->subHours(24))
                ->where('is_blocked', false)
                ->count();

            $trafficService = app(\App\Services\TrafficAnalyticsService::class);
            $dailyTraffic = $trafficService->getMetricsForPeriod('daily');
            $weeklyTraffic = $trafficService->getMetricsForPeriod('weekly');
            $monthlyTraffic = $trafficService->getMetricsForPeriod('monthly');
            $currentTraffic = match ($this->trafficPeriod) {
                'weekly' => $weeklyTraffic,
                'monthly' => $monthlyTraffic,
                default => $dailyTraffic,
            };

            return [
                'onlineCount' => $onlineCount,
                'recentCount' => $recentLeftCount,
                'recentLeftCount' => $recentLeftCount,
                'activeTab' => $this->activeTab,
                'todayVisitorsCount' => max($todayVisitorsCount, $dailyTraffic['unique_visitors'] ?? 0),
                'highIntentCount' => $highIntentCount,
                'liveHighIntentCount' => $liveHighIntentCount,
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
                'liveCartCount' => $liveCartCount,
                'recentCartCount' => $recentCartCount,
                'dbPendingCartsCount' => $dbPendingCartsCount,
                'hesitatingCount' => $hesitatingCount,
                'liveHesitatingCount' => $liveHesitatingCount,
                'membersCount' => $membersCount,
                'guestsCount' => $guestsCount,
                'loginRate' => $loginRate,
                'totalActive' => $totalActive,
                'blockedCount' => $blockedCount,
                'activeCardFilter' => $this->activeCardFilter,
                'trafficPeriod' => $this->trafficPeriod,
                'dailyTraffic' => $dailyTraffic,
                'weeklyTraffic' => $weeklyTraffic,
                'monthlyTraffic' => $monthlyTraffic,
                'currentTraffic' => $currentTraffic,
            ];
        } catch (\Throwable $e) {
            $emptyTraffic = [
                'period' => 'daily',
                'period_label' => 'Günlük',
                'unique_visitors' => 0,
                'page_views' => 0,
                'sessions' => 0,
                'cart_additions' => 0,
                'orders_count' => 0,
                'orders_revenue' => 0,
                'cart_rate' => 0,
                'conversion_rate' => 0,
                'avg_duration_formatted' => '0 sn',
                'device_breakdown' => ['mobile' => 85, 'desktop' => 12, 'tablet' => 3],
                'source_breakdown' => [],
                'analysis' => 'Veriler toplanıyor...',
            ];

            return [
                'onlineCount' => 0,
                'recentCount' => 0,
                'recentLeftCount' => 0,
                'activeTab' => $this->activeTab,
                'highIntentCount' => 0,
                'cartCount' => 0,
                'cartTotal' => 0,
                'hesitatingCount' => 0,
                'membersCount' => 0,
                'guestsCount' => 0,
                'totalActive' => 0,
                'blockedCount' => 0,
                'trafficPeriod' => 'daily',
                'dailyTraffic' => $emptyTraffic,
                'weeklyTraffic' => $emptyTraffic,
                'monthlyTraffic' => $emptyTraffic,
                'currentTraffic' => $emptyTraffic,
            ];
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->query(fn (): Builder =>
                ActiveVisitor::query()
                    ->with(['user', 'cart.items.product.images'])
                    ->when($this->activeTab === 'recent', function (Builder $query) {
                        $query->where('is_blocked', false)
                            ->where(function (Builder $sq) {
                                $sq->where('last_heartbeat_at', '<', now()->subSeconds(75))
                                    ->where('last_heartbeat_at', '>=', now()->subDays(7));
                            });
                    }, function (Builder $query) {
                        // SADECE CANLI OLANLAR VEYA ENGELLENMİŞ OLANLAR (Yönetim & Engel Kaldırma için)
                        $query->where(function (Builder $sq) {
                            $sq->online()->orWhere('is_blocked', true);
                        });
                    })
                    ->when($this->activeCardFilter === 'cart', fn($q) => $q->where('cart_items_count', '>', 0))
                    ->when($this->activeCardFilter === 'high_intent', fn($q) => $q->where('intent_score', '>=', 60))
                    ->when($this->activeCardFilter === 'hesitating', fn($q) => $q->where('intent_level', 'hesitating'))
                    ->when($this->activeCardFilter === 'members', fn($q) => $q->where(fn($sq) => $sq->whereNotNull('user_id')->orWhere('is_identified', true)))
                    ->latest('last_heartbeat_at')
            )
            ->columns([
                // 1. Ziyaretçi Kimliği & Canlı Sinyal
                TextColumn::make('visitor_identity')
                    ->label('Ziyaretçi & Sinyal')
                    ->searchable(['ip_address', 'guest_name', 'guest_id', 'visitor_token', 'guest_email', 'guest_phone', 'referrer_host', 'utm_source', 'utm_campaign', 'user.name', 'user.email'])
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $isOnline = $record->is_currently_online;
                        $diff = $record->last_heartbeat_at ? $record->last_heartbeat_at->diffForHumans(null, true) : 'şimdi';
                        $deviceIcon = match ($record->device_type) {
                            'mobile' => '📱',
                            'tablet' => '📟',
                            default => '💻',
                        };

                        $name = $record->user_id && $record->user ? e($record->user->name) : e($record->display_name);
                        $isMember = ($record->user_id && $record->user) || $record->is_identified;
                        $hasCustomName = !empty($record->guest_name);
                        $initial = mb_substr($name, 0, 1);
                        $duration = $record->duration_formatted;
                        $pageCount = $record->page_views_count ?: 1;
                        $starsHtml = $record->stars_html;
                        $guestId = e($record->guest_id);

                        $phone = $record->user?->phone ?? $record->guest_phone;
                        $email = $record->user?->email ?? $record->guest_email;

                        $onlineBadge = $isOnline
                            ? '<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:800;background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.4);"><span class="live-radar-dot" style="width:7px;height:7px;"></span> CANLI</span>'
                            : '<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:700;background:rgba(245,158,11,0.15);color:#f59e0b;border:1px solid rgba(245,158,11,0.3);">AYRILDI (' . $diff . ')</span>';

                        $badgeHtml = '';
                        if ($isMember) {
                            $badgeHtml = '<span style="background:rgba(16,185,129,0.25);color:#10b981;border:1px solid rgba(16,185,129,0.4);padding:1px 6px;border-radius:4px;font-size:9px;font-weight:800;">MÜŞTERİ</span>';
                        } elseif ($hasCustomName) {
                            $badgeHtml = '<span style="background:rgba(56,189,248,0.2);color:#38bdf8;border:1px solid rgba(56,189,248,0.4);padding:1px 6px;border-radius:4px;font-size:9px;font-weight:800;">MİSAFİR</span>';
                        }

                        $idBadge = '';
                        if ($hasCustomName) {
                            $idBadge = '<span title="Kalıcı Misafir ID: #' . $guestId . '" style="background:rgba(255,255,255,0.08);color:#94a3b8;border:1px solid rgba(255,255,255,0.15);padding:1px 5px;border-radius:4px;font-size:9.5px;font-family:monospace;font-weight:700;">#' . $guestId . '</span>';
                        }

                        $contactHtml = '';
                        if ($phone || $email) {
                            $contactHtml = '<div style="font-size:11px;color:#38bdf8;font-weight:700;display:flex;align-items:center;gap:5px;margin-top:2px;">'
                                . ($phone ? '<span>📱 ' . e($phone) . '</span>' : '')
                                . ($phone && $email ? '<span>•</span>' : '')
                                . ($email ? '<span style="color:#94a3b8;">✉️ ' . e($email) . '</span>' : '')
                                . '</div>';
                        }

                        $sourceInfo = $record->source_info;
                        $sourceHtml = '<div style="margin-top:5px;display:flex;align-items:center;gap:5px;flex-wrap:wrap;">'
                            . '<span title="' . e($sourceInfo['detail']) . '" style="display:inline-flex;align-items:center;gap:4px;padding:2px 7px;border-radius:6px;font-size:10px;font-weight:800;background:' . $sourceInfo['bg_color'] . ';color:' . $sourceInfo['color'] . ';border:1px solid ' . $sourceInfo['border_color'] . ';">'
                            . '<span>' . $sourceInfo['icon'] . '</span>'
                            . '<span>' . e($sourceInfo['name']) . '</span>'
                            . '</span>'
                            . (!empty($record->utm_campaign) ? '<span title="Kampanya: ' . e($record->utm_campaign) . '" style="display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:5px;font-size:9.5px;font-weight:700;background:rgba(255,255,255,0.06);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);">🎯 ' . e($record->utm_campaign) . '</span>' : '')
                            . '</div>';

                        $freqSignal = app(\App\Services\TrafficAnalyticsService::class)->getVisitorFrequencySignal($record);
                        $freqHtml = '<div style="margin-top:4px;">'
                            . '<span title="' . e($freqSignal['label']) . '" style="display:inline-flex;align-items:center;gap:4px;padding:2px 7px;border-radius:6px;font-size:9.5px;font-weight:800;background:' . $freqSignal['bg'] . ';color:' . $freqSignal['color'] . ';border:1px solid ' . $freqSignal['border'] . ';">'
                            . '<span>' . $freqSignal['icon'] . '</span>'
                            . '<span>' . $freqSignal['badge'] . '</span>'
                            . '<span style="opacity:0.85;font-weight:600;">(' . e($freqSignal['label']) . ')</span>'
                            . '</span>'
                            . '</div>';

                        return new HtmlString('
                            <div style="display:flex;align-items:flex-start;gap:12px;min-width:220px;">
                                <div style="position:relative;width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#ff4e00,#b45309);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:15px;flex-shrink:0;box-shadow:0 0 12px rgba(255,78,0,0.35);">
                                    ' . $initial . '
                                </div>
                                <div style="flex:1;">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;flex-wrap:wrap;">
                                        <span style="font-weight:800;color:#f8fafc;font-size:13px;">' . $name . '</span>
                                        ' . $starsHtml . '
                                        ' . $badgeHtml . '
                                        ' . $idBadge . '
                                    </div>
                                    <div style="margin-bottom:4px;">
                                        ' . $onlineBadge . '
                                    </div>
                                    ' . $contactHtml . '
                                    <div style="font-size:11px;color:#94a3b8;display:flex;align-items:center;gap:5px;margin-top:2px;flex-wrap:wrap;">
                                        <span>' . $deviceIcon . ' ' . e($record->browser ?? 'Tarayıcı') . '</span>
                                        <span>•</span>
                                        <span style="font-family:monospace;color:#64748b;">' . e($record->ip_address) . '</span>
                                        <span>•</span>
                                        <span title="Kalıcı Misafir ID" style="font-family:monospace;color:#38bdf8;font-size:10px;background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.25);padding:0.5px 5px;border-radius:4px;">ID: #' . $guestId . '</span>
                                    </div>
                                    <div style="font-size:10px;color:#64748b;margin-top:2px;">
                                        ⏱️ ' . $duration . ' (' . $pageCount . '. sayfa)
                                    </div>
                                    ' . $sourceHtml . '
                                    ' . $freqHtml . '
                                </div>
                            </div>
                        ');
                    }),

                // 2. Bulunduğu Sayfa & Model
                TextColumn::make('current_path')
                    ->label('Bulunduğu Sayfa & Model')
                    ->searchable(['current_title', 'current_path'])
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $pageInfo = $record->page_info;
                        $product = $record->current_product;
                        $productImage = $record->current_product_image;
                        $url = $record->current_url ?: $record->current_path;

                        // Beden seçimi veya son hareket
                        $recentDetail = null;
                        if (!empty($record->journey_trail)) {
                            foreach (array_reverse($record->journey_trail) as $step) {
                                if (!empty($step['detail'])) {
                                    $recentDetail = $step['detail'];
                                    break;
                                }
                            }
                        }

                        // Durum 1: Ürün Sayfasında
                        if ($product) {
                            $price = $product->discount_price ?: $product->price;
                            $imgHtml = $productImage
                                ? '<img src="' . e($productImage) . '" style="width:48px;height:48px;border-radius:12px;object-fit:cover;border:1px solid rgba(255,255,255,0.15);flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,0.35);" />'
                                : '<div style="width:48px;height:48px;border-radius:12px;background:rgba(255,78,0,0.15);border:1px solid rgba(255,78,0,0.35);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">👟</div>';

                            return new HtmlString('
                                <div style="display:flex;align-items:center;gap:12px;max-width:290px;">
                                    ' . $imgHtml . '
                                    <div style="overflow:hidden;flex:1;">
                                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
                                            <span style="display:inline-flex;align-items:center;gap:3px;padding:1px 6px;border-radius:4px;font-size:9px;font-weight:800;letter-spacing:0.04em;text-transform:uppercase;background:rgba(255,78,0,0.15);color:#ff7849;border:1px solid rgba(255,78,0,0.35);">
                                                👟 ' . e($pageInfo['badge']) . '
                                            </span>
                                        </div>
                                        <a href="' . e($url) . '" target="_blank" style="font-weight:800;color:#f8fafc;font-size:12.5px;line-height:1.35;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            ' . e($pageInfo['title']) . ' ↗
                                        </a>
                                        <div style="display:flex;align-items:center;gap:6px;margin-top:3px;">
                                            <span style="font-weight:800;color:#10b981;font-size:12px;">' . number_format($price, 2) . ' ₺</span>
                                            <span style="font-size:10px;color:#94a3b8;background:rgba(255,255,255,0.06);padding:1px 5px;border-radius:4px;">İnceliyor</span>
                                        </div>
                                        ' . ($recentDetail ? '<div style="font-size:10px;color:#fbbf24;font-weight:700;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">🎯 ' . e($recentDetail) . '</div>' : '') . '
                                    </div>
                                </div>
                            ');
                        }

                        // Durum 2: Ödeme Sayfası (Checkout)
                        if ($pageInfo['type'] === 'checkout') {
                            return new HtmlString('
                                <div style="display:flex;align-items:center;gap:12px;max-width:290px;">
                                    <div style="width:48px;height:48px;border-radius:12px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.35);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;box-shadow:0 0 14px rgba(16,185,129,0.2);">
                                        🛒
                                    </div>
                                    <div style="overflow:hidden;flex:1;">
                                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
                                            <span style="display:inline-flex;align-items:center;gap:3px;padding:1px 6px;border-radius:4px;font-size:9px;font-weight:800;letter-spacing:0.04em;text-transform:uppercase;background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.35);">
                                                🛒 ' . e($pageInfo['badge']) . '
                                            </span>
                                        </div>
                                        <a href="' . e($url) . '" target="_blank" style="font-weight:800;color:#38bdf8;font-size:12.5px;line-height:1.35;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            ' . e($pageInfo['title']) . ' ↗
                                        </a>
                                        <div style="font-size:11px;color:#10b981;font-weight:700;margin-top:2px;">
                                            Sepet Tutarı: ' . number_format($record->cart_total, 2) . ' ₺' . ($record->cart_items_count > 0 ? ' (' . $record->cart_items_count . ' ürün)' : '') . '
                                        </div>
                                        ' . ($recentDetail ? '<div style="font-size:10px;color:#fbbf24;font-weight:700;margin-top:3px;">🎯 ' . e($recentDetail) . '</div>' : '') . '
                                    </div>
                                </div>
                            ');
                        }

                        // Durum 3: Diğer Tüm Sayfalar (Ana Sayfa, Kategoriler, Sipariş Takip, Blog, Kurumsal vb.)
                        $color = $pageInfo['color'];
                        $bgColor = $pageInfo['bg_color'];
                        $borderColor = $pageInfo['border_color'];
                        $icon = $pageInfo['icon'];

                        return new HtmlString('
                            <div style="display:flex;align-items:center;gap:12px;max-width:290px;">
                                <div style="width:48px;height:48px;border-radius:12px;background:' . $bgColor . ';border:1px solid ' . $borderColor . ';display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
                                    ' . $icon . '
                                </div>
                                <div style="overflow:hidden;flex:1;">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
                                        <span style="display:inline-flex;align-items:center;gap:3px;padding:1px 6px;border-radius:4px;font-size:9px;font-weight:800;letter-spacing:0.04em;text-transform:uppercase;background:' . $bgColor . ';color:' . $color . ';border:1px solid ' . $borderColor . ';">
                                            ' . $icon . ' ' . e($pageInfo['badge']) . '
                                        </span>
                                    </div>
                                    <a href="' . e($url) . '" target="_blank" style="font-weight:800;color:#f8fafc;font-size:12.5px;line-height:1.35;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        ' . e($pageInfo['title']) . ' ↗
                                    </a>
                                    <div style="font-size:10px;color:#64748b;margin-top:2px;font-family:monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        ' . e($record->current_path) . '
                                    </div>
                                    ' . ($recentDetail ? '<div style="font-size:10px;color:#fbbf24;font-weight:700;margin-top:3px;">🎯 ' . e($recentDetail) . '</div>' : '') . '
                                </div>
                            </div>
                        ');
                    }),

                // 3. Davranış Teşhisi & Satın Alma Niyeti
                TextColumn::make('behavior_insight')
                    ->label('Davranış Teşhisi & Niyet')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $score = $record->intent_score ?? 15;
                        $insight = $record->behavior_insight ?? 'Sitede genel keşif yapıyor.';

                        $color = match (true) {
                            $score >= 80 => '#10b981',
                            $score >= 60 => '#f59e0b',
                            $score >= 40 => '#38bdf8',
                            default => '#94a3b8',
                        };

                        $gradient = match (true) {
                            $score >= 80 => 'linear-gradient(90deg, #10b981, #059669)',
                            $score >= 60 => 'linear-gradient(90deg, #f59e0b, #d97706)',
                            $score >= 40 => 'linear-gradient(90deg, #38bdf8, #0284c7)',
                            default => 'linear-gradient(90deg, #64748b, #475569)',
                        };

                        $label = match (true) {
                            $score >= 80 => '🔥 ÇOK SICAK (%' . $score . ')',
                            $score >= 60 => '⚡ TEREDDÜTTE (%' . $score . ')',
                            $score >= 40 => '👀 İLGİLİ (%' . $score . ')',
                            default => '🔍 KEŞİF (%' . $score . ')',
                        };

                        return new HtmlString('
                            <div style="min-width:210px;max-width:260px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                                    <span style="font-size:10px;font-weight:900;color:' . $color . ';letter-spacing:0.04em;">
                                        ' . $label . '
                                    </span>
                                </div>
                                <div style="background:rgba(255,255,255,0.08);border-radius:999px;height:5px;width:100%;overflow:hidden;margin-bottom:6px;">
                                    <div style="background:' . $gradient . ';width:' . $score . '%;height:100%;border-radius:999px;"></div>
                                </div>
                                <div style="font-size:11px;color:#cbd5e1;background:#182234;border-left:3px solid ' . $color . ';padding:5px 8px;border-radius:4px;line-height:1.35;">
                                    ' . e($insight) . '
                                </div>
                            </div>
                        ');
                    }),

                // 4. Sepet Durumu
                TextColumn::make('cart_total')
                    ->label('Sepet')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        if ($record->cart_items_count > 0) {
                            return new HtmlString('
                                <div>
                                    <div style="font-weight:800;color:#10b981;font-size:13px;display:flex;align-items:center;gap:4px;">
                                        🛒 ' . number_format($record->cart_total, 2) . ' ₺
                                    </div>
                                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;">
                                        ' . $record->cart_items_count . ' ürün sepette
                                    </div>
                                </div>
                            ');
                        }

                        return new HtmlString('<span style="color:#64748b;font-size:11px;">Sepet Boş</span>');
                    }),
            ])
            ->content(fn (Table $table) => view('filament.pages.partials.active-visitors-table', [
                'table' => $table,
                'viewMode' => $this->viewMode,
                'activeTab' => $this->activeTab,
                'onlineCount' => ActiveVisitor::online()->where('is_blocked', false)->count(),
                'recentCount' => ActiveVisitor::where('last_heartbeat_at', '<', now()->subSeconds(75))->where('last_heartbeat_at', '>=', now()->subHours(24))->where('is_blocked', false)->count(),
            ]))
            ->filters([
                SelectFilter::make('intent_filter')
                    ->label('Filtrele & Durum')
                    ->options([
                        'all' => 'Tüm Ziyaretçiler',
                        'blocked' => '🚫 Engellenenler (Banlılar)',
                        'online' => '🟢 Sadece Canlı Olanlar',
                        'high_intent' => '🔥 Sıcak Adaylar (Niyet >= 60)',
                        'hesitating' => '⚡ Tereddütte Olanlar',
                        'with_cart' => '🛒 Sepetinde Ürün Olanlar',
                        'members' => '👤 Kayıtlı Üyeler / Müşteriler',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === 'blocked') {
                            $query->where('is_blocked', true);
                        } elseif ($val === 'online') {
                            $query->where('last_heartbeat_at', '>=', now()->subSeconds(45))->where('is_blocked', false);
                        } elseif ($val === 'high_intent') {
                            $query->where('intent_score', '>=', 60);
                        } elseif ($val === 'hesitating') {
                            $query->where('intent_level', 'hesitating');
                        } elseif ($val === 'with_cart') {
                            $query->where('cart_items_count', '>', 0);
                        } elseif ($val === 'members') {
                            $query->where(function ($q) {
                                $q->whereNotNull('user_id')->orWhere('is_identified', true);
                            });
                        }
                    }),

                SelectFilter::make('traffic_source')
                    ->label('Geliş Kaynağı')
                    ->options([
                        'google' => '🌐 Google (Arama / Ads)',
                        'instagram' => '📸 Instagram',
                        'facebook' => '📘 Facebook / Meta',
                        'tiktok' => '🎵 TikTok',
                        'direct' => '⚡ Doğrudan (Direkt)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === 'google') {
                            $query->where(function ($q) {
                                $q->where('referrer_host', 'like', '%google%')
                                  ->orWhere('utm_source', 'like', '%google%')
                                  ->orWhere('utm_source', 'like', '%cpc%');
                            });
                        } elseif ($val === 'instagram') {
                            $query->where(function ($q) {
                                $q->where('referrer_host', 'like', '%instagram%')
                                  ->orWhere('utm_source', 'like', '%instagram%');
                            });
                        } elseif ($val === 'facebook') {
                            $query->where(function ($q) {
                                $q->where('referrer_host', 'like', '%facebook%')
                                  ->orWhere('referrer_host', 'like', '%fb.%')
                                  ->orWhere('utm_source', 'like', '%facebook%')
                                  ->orWhere('utm_source', 'like', '%meta%');
                            });
                        } elseif ($val === 'tiktok') {
                            $query->where(function ($q) {
                                $q->where('referrer_host', 'like', '%tiktok%')
                                  ->orWhere('utm_source', 'like', '%tiktok%');
                            });
                        } elseif ($val === 'direct') {
                            $query->where(function ($q) {
                                $q->whereNull('referrer')->orWhere('referrer', '');
                            })->where(function ($q) {
                                $q->whereNull('utm_source')->orWhere('utm_source', '');
                            });
                        }
                    }),
            ])
            ->recordActions([
                // ─── 1. Önerilen Stratejiyi 1-Tıkla Uygula ───
                Action::make('apply_strategy')
                    ->label('⚡ Strateji Uygula')
                    ->button()
                    ->size('sm')
                    ->color('success')
                    ->icon('heroicon-o-bolt')
                    ->modalWidth(Width::TwoExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::End)
                    ->modalHeading(fn (ActiveVisitor $record) => '⚡ Satış Stratejisini Uygula: ' . ($record->recommended_strategy['title'] ?? 'Özel Teklif'))
                    ->modalDescription(fn (ActiveVisitor $record) => 'Davranış Teşhisi: ' . ($record->behavior_insight ?? ''))
                    ->modalSubmitActionLabel('🚀 Ziyaretçiye Hemen Gönder')
                    ->form(function (ActiveVisitor $record) {
                        $strategy = $record->recommended_strategy ?? [];
                        $isRedirect = ($strategy['action_type'] ?? '') === 'redirect';

                        return [
                            Select::make('strategy_type')
                                ->label('Aksiyon Türü')
                                ->native(false)
                                ->options([
                                    'offer' => '🎁 Fırsat / Kupon / WhatsApp Popup',
                                    'redirect' => '🚀 Doğrudan Sayfaya Yönlendir',
                                ])
                                ->default($isRedirect ? 'redirect' : 'offer')
                                ->live(),

                            TextInput::make('offer_title')
                                ->label('Bildirim Başlığı')
                                ->default($strategy['popup_title'] ?? $strategy['title'] ?? 'Size Özel Fırsat!')
                                ->visible(fn ($get) => $get('strategy_type') === 'offer')
                                ->required(),

                            Textarea::make('offer_message')
                                ->label('Mesaj Metni')
                                ->rows(3)
                                ->default($strategy['suggested_message'] ?? 'Bu fırsatı kaçırmayın!')
                                ->required(),

                            TextInput::make('coupon_code')
                                ->label('Kupon Kodu (Opsiyonel)')
                                ->default($strategy['suggested_coupon'] ?? null)
                                ->visible(fn ($get) => $get('strategy_type') === 'offer'),

                            TextInput::make('action_button')
                                ->label('Buton Metni (Opsiyonel)')
                                ->default($strategy['action_button'] ?? null)
                                ->visible(fn ($get) => $get('strategy_type') === 'offer'),

                            TextInput::make('action_url')
                                ->label('Buton Hedef Linki (Opsiyonel)')
                                ->default($strategy['action_url'] ?? null)
                                ->visible(fn ($get) => $get('strategy_type') === 'offer'),

                            TextInput::make('redirect_url')
                                ->label('Yönlendirilecek Hedef URL (Site İçi veya Site Dışı)')
                                ->default($strategy['target_url'] ?? '/checkout')
                                ->placeholder('Örn: /checkout veya https://instagram.com/... veya https://wa.me/...')
                                ->helperText('Site içi sayfa (/checkout gibi) veya harici web adresi (https://... gibi) yazabilirsiniz.')
                                ->visible(fn ($get) => $get('strategy_type') === 'redirect')
                                ->required(),

                            Select::make('countdown')
                                ->label('Yönlendirme Geri Sayımı')
                                ->native(false)
                                ->options([
                                    '0' => 'Anında Yönlendir (0 sn)',
                                    '3' => '3 Saniye Geri Sayımla',
                                    '5' => '5 Saniye Geri Sayımla',
                                ])
                                ->default('3')
                                ->visible(fn ($get) => $get('strategy_type') === 'redirect'),
                        ];
                    })
                    ->action(function (ActiveVisitor $record, array $data) {
                        if (($data['strategy_type'] ?? 'offer') === 'redirect') {
                            $target = self::normalizeUrl($data['redirect_url'] ?? '/checkout');
                            $record->queueRedirect(
                                $target,
                                $data['offer_message'] ?? null,
                                (int) ($data['countdown'] ?? 3)
                            );
                        } else {
                            $record->queueOffer(
                                $data['offer_title'] ?? 'Özel Fırsat',
                                $data['offer_message'] ?? '',
                                $data['coupon_code'] ?? null,
                                $data['action_button'] ?? null,
                                $data['action_url'] ?? null
                            );
                        }

                        Notification::make()
                            ->title('Satış Stratejisi İletildi!')
                            ->body('Komut ziyaretçinin ekranına iletildi, birkaç saniye içinde açılacak.')
                            ->success()
                            ->send();
                    }),

                // ─── 2. Hızlı Yönlendir ───
                Action::make('force_redirect')
                    ->label('🚀 Yönlendir')
                    ->button()
                    ->size('sm')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::End)
                    ->modalHeading('🚀 Ziyaretçiyi Sayfaya Yönlendir')
                    ->modalDescription('Ziyaretçiyi istediğiniz adrese aktarın. "Şimdi Yönlendir" ile pencere açık kalır; "Şimdi Yönlendir ve Çık" ile işlem tamamlanıp pencere kapanır.')
                    ->modalSubmitActionLabel('🚀 Şimdi Yönlendir')
                    ->modalSubmitAction(fn (Action $action) => $action->icon('heroicon-o-paper-airplane'))
                    ->extraModalFooterActions(fn (Action $action): array => [
                        $action->makeModalSubmitAction('force_redirect_and_close', ['close' => true])
                            ->label('Şimdi Yönlendir ve Çık')
                            ->color('gray')
                            ->icon('heroicon-o-arrow-right-on-rectangle'),
                    ])
                    ->form(self::getRedirectFormSchema())
                    ->action(function (ActiveVisitor $record, array $data, array $arguments, Action $action) {
                        $target = self::resolveRedirectUrl($data);
                        $isSilent = ($data['redirect_mode'] ?? 'silent') === 'silent';
                        $showNotice = !$isSilent;
                        $shouldClose = (bool) ($arguments['close'] ?? false);

                        $record->queueRedirect(
                            $target,
                            $showNotice ? ($data['redirect_message'] ?? null) : null,
                            $showNotice ? (int) ($data['countdown'] ?? 3) : 0,
                            $showNotice
                        );

                        Notification::make()
                            ->title('Yönlendirme Başlatıldı')
                            ->body('Ziyaretçi ' . ($isSilent ? 'sessizce (bildirimsiz) ' : '') . $target . ' hedefine yönlendiriliyor.' . ($shouldClose ? '' : ' (Pop-up açık tutuldu)'))
                            ->success()
                            ->send();

                        if (! $shouldClose) {
                            $action->arguments([]);
                            $action->halt();
                        }
                    }),

                // ─── 3. Gezinme Yolculuğunu İncele ───
                Action::make('inspect_journey')
                    ->label('🔍 İncele')
                    ->button()
                    ->size('sm')
                    ->color('gray')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (ActiveVisitor $record) => 'Müşteri Yolculuğu & Sepet Özeti - ' . $record->display_name)
                    ->modalWidth(\Filament\Support\Enums\Width::FiveExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::End)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat')
                    ->modalContent(fn (ActiveVisitor $record) => view('filament.pages.partials.visitor-journey-modal', ['record' => $record])),

                // ─── 4. Sesli İleti / Anons Gönder ───
                Action::make('send_voice')
                    ->label('🎙️ Sesli İleti')
                    ->button()
                    ->size('sm')
                    ->color('warning')
                    ->icon('heroicon-o-speaker-wave')
                    ->modalWidth(Width::TwoExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::End)
                    ->modalHeading(fn (ActiveVisitor $record) => '🎙️ Ziyaretçiye Sesli İleti & Anons Gönder (' . $record->display_name . ')')
                    ->modalDescription('Ziyaretçinin ekranında ses kaydınız oynatılır (isteğe bağlı görsel bildirim kartı eklenebilir).')
                    ->modalSubmitActionLabel('🚀 Sesli İletiyi Fırlat')
                    ->form([
                        Select::make('voice_source')
                            ->label('Seslendirme & Şablon Kaynağı')
                            ->native(false)
                            ->options([
                                'canned' => '🎙️ Hazır Ses Kayıtlı Şablondan Seç',
                                'custom_upload' => '📤 Şimdi Yeni Ses Kaydı Yükle (.mp3, .wav, .m4a)',
                                'browser_tts' => '🔔 Sadece Mağaza Zili & Bildirim (Ses Dosyasız)',
                            ])
                            ->default('canned')
                            ->live(),

                        Select::make('canned_message_id')
                            ->label('Hazır Sesli Anons Şablonu')
                            ->native(false)
                            ->options(function () {
                                return CannedVoiceMessage::active()->ordered()->get()->mapWithKeys(function ($item) {
                                    $badge = $item->hasAudio() ? ' [🎵 Ses Kaydı Yüklü]' : ' [⚠️ Ses Kaydı Yok]';
                                    return [$item->id => $item->title . $badge];
                                })->toArray();
                            })
                            ->default(fn () => CannedVoiceMessage::active()->ordered()->value('id'))
                            ->visible(fn ($get) => ($get('voice_source') ?? 'canned') === 'canned')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($item = CannedVoiceMessage::find($state)) {
                                    $set('title', $item->title);
                                    $set('message', $item->message);
                                    $set('coupon_code', $item->coupon_code);
                                    $set('action_button', $item->action_button);
                                    $set('action_url', $item->action_url);
                                }
                            }),

                        FileUpload::make('uploaded_audio')
                            ->label('Ses Kaydı Dosyası (.mp3, .wav, .m4a, .ogg)')
                            ->disk('public')
                            ->directory('voice-announcements')
                            ->acceptedFileTypes([
                                'audio/mpeg',
                                'audio/mp3',
                                'audio/wav',
                                'audio/x-wav',
                                'audio/x-m4a',
                                'audio/mp4',
                                'audio/ogg',
                                'audio/webm',
                                'audio/aac',
                            ])
                            ->maxSize(25600)
                            ->visible(fn ($get) => $get('voice_source') === 'custom_upload')
                            ->helperText('Telefonunuzdan veya mikrofonunuzdan kaydettiğiniz ses dosyasını yükleyin. Ziyaretçilere bu ses dinletilecektir.'),

                        Select::make('sound_type')
                            ->label('Ses & Bildirim Modu')
                            ->native(false)
                            ->options([
                                'speech_only' => '🎵 Sadece Sesi Oynat (Bildirim Kartsız / Otomatik Çal)',
                                'speech_with_card' => '💬 Sesi Oynat + Görsel Bildirim Kartı Göster',
                                'chime_with_card' => '🔔 Mağaza Zili + Sesi Oynat + Görsel Kart Göster',
                            ])
                            ->default('speech_only')
                            ->required()
                            ->live(),

                        TextInput::make('title')
                            ->label('Bildirim Başlığı')
                            ->default('🎙️ Mağazamıza Hoş Geldiniz!')
                            ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card']))
                            ->required(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card'])),

                        Textarea::make('message')
                            ->label('Seslendirilecek ve Gösterilecek Mesaj')
                            ->rows(3)
                            ->default('Patenli Ayakkabılar\'a hoş geldiniz! Beğendiğiniz modellerde bugün geçerli özel fırsatları kaçırmayın, keyifli alışverişler dileriz!')
                            ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card']) || $get('voice_source') === 'browser_tts')
                            ->required(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card']) || $get('voice_source') === 'browser_tts'),

                        TextInput::make('coupon_code')
                            ->label('İndirim Kuponu (Opsiyonel)')
                            ->placeholder('Örn: SESLI10')
                            ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card'])),

                        TextInput::make('action_button')
                            ->label('Buton Metni (Opsiyonel)')
                            ->default('Tüm Modelleri Gör')
                            ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card'])),

                        TextInput::make('action_url')
                            ->label('Buton Linki (Opsiyonel)')
                            ->default('/patenli-ayakkabilar')
                            ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card'])),
                    ])
                    ->action(function (ActiveVisitor $record, array $data) {
                        $audioUrl = null;
                        if (($data['voice_source'] ?? 'canned') === 'custom_upload' && !empty($data['uploaded_audio'])) {
                            $audioUrl = asset('storage/' . ltrim($data['uploaded_audio'], '/'));
                        } elseif (($data['voice_source'] ?? 'canned') === 'canned' && !empty($data['canned_message_id'])) {
                            $canned = CannedVoiceMessage::find($data['canned_message_id']);
                            $audioUrl = $canned?->audio_url;
                        }

                        $soundMode = $data['sound_type'] ?? 'speech_only';
                        $showCard = in_array($soundMode, ['speech_with_card', 'chime_with_card']);
                        $chime = ($soundMode === 'chime_with_card') ? 'chime_and_speech' : 'speech_only';

                        $record->queueVoiceMessage(
                            $data['message'] ?? '',
                            $data['title'] ?? '🎙️ Canlı Mağaza Anonsu',
                            $chime,
                            $showCard ? ($data['coupon_code'] ?? null) : null,
                            $showCard ? ($data['action_button'] ?? null) : null,
                            $showCard ? ($data['action_url'] ?? null) : null,
                            $audioUrl,
                            $showCard
                        );

                        Notification::make()
                            ->title('Sesli İleti İletildi! 🎙️')
                            ->body($record->display_name . ' adlı ziyaretçinin ekranında ' . ($showCard ? 'ses ve görsel kart açılacak.' : 'kart olmadan yalnızca ses otomatik çalacak.'))
                            ->success()
                            ->send();
                    }),

                // ─── 5. Diğer Aksiyonlar ───
                ActionGroup::make([
                    Action::make('remote_reload')
                        ->label('Sayfayı Yenilet')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (ActiveVisitor $record) {
                            $record->queueReload();
                            Notification::make()->title('Yenileme emri gönderildi.')->send();
                        }),

                    Action::make('kick_user')
                        ->label('Oturumu Sonlandır (Kick)')
                        ->icon('heroicon-o-arrow-left-on-rectangle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (ActiveVisitor $record) {
                            $record->queueKick();
                            Notification::make()->title('Ziyaretçi oturumu sonlandırıldı.')->warning()->send();
                        }),

                    Action::make('block_ip')
                        ->label('Ziyaretçiyi Engelle (Ban)')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->visible(fn (ActiveVisitor $record) => ! $record->is_blocked)
                        ->requiresConfirmation()
                        ->modalHeading('Ziyaretçiyi Engelle (Ban)')
                        ->modalDescription('Bu ziyaretçinin siteye erişimi kısıtlanacaktır. Dilediğiniz zaman "Engeli Kaldır" ile erişimini tekrar açabilirsiniz.')
                        ->action(function (ActiveVisitor $record) {
                            $record->blockVisitor();
                            Notification::make()->title('Ziyaretçi erişimi engellendi.')->danger()->send();
                        }),

                    Action::make('unblock_ip')
                        ->label('Engeli Kaldır (Erişimi Aç)')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (ActiveVisitor $record) => (bool) $record->is_blocked)
                        ->requiresConfirmation()
                        ->modalHeading('Erişim Engelini Kaldır')
                        ->modalDescription('Bu kullanıcının engeli kaldırılacak ve siteye normal erişimine izin verilecektir.')
                        ->action(function (ActiveVisitor $record) {
                            $record->unblockVisitor();
                            Notification::make()->title('Ziyaretçinin erişim engeli kaldırıldı!')->success()->send();
                        }),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->tooltip('Diğer İşlemler'),
            ]);
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.partials.active-visitors-header');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Toplu Yönlendirme
            Action::make('bulk_redirect')
                ->label('📢 Tüm Canlıları Toplu Yönlendir')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane')
                ->modalWidth(Width::ThreeExtraLarge)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::End)
                ->modalHeading('📢 Sitedeki Tüm Aktif Ziyaretçileri Toplu Yönlendir')
                ->modalDescription('Sitedeki tüm aktif kullanıcılara tek tıkla yönlendirme emri gönderir. "Şimdi Yönlendir" ile pencere açık kalır; "Şimdi Yönlendir ve Çık" ile işlem tamamlanıp pencere kapanır.')
                ->modalSubmitActionLabel('🚀 Şimdi Yönlendir')
                ->modalSubmitAction(fn (Action $action) => $action->icon('heroicon-o-paper-airplane'))
                ->extraModalFooterActions(fn (Action $action): array => [
                    $action->makeModalSubmitAction('bulk_redirect_and_close', ['close' => true])
                        ->label('Şimdi Yönlendir ve Çık')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-right-on-rectangle'),
                ])
                ->form(self::getRedirectFormSchema())
                ->action(function (array $data, array $arguments, Action $action) {
                    $target = self::resolveRedirectUrl($data);
                    $isSilent = ($data['redirect_mode'] ?? 'silent') === 'silent';
                    $showNotice = !$isSilent;
                    $shouldClose = (bool) ($arguments['close'] ?? false);
                    $visitors = ActiveVisitor::online()->get();

                    foreach ($visitors as $v) {
                        $v->queueRedirect(
                            $target,
                            $showNotice ? ($data['redirect_message'] ?? null) : null,
                            $showNotice ? (int) ($data['countdown'] ?? 3) : 0,
                            $showNotice
                        );
                    }

                    Notification::make()
                        ->title('Toplu Yönlendirme Başlatıldı')
                        ->body($visitors->count() . ' aktif kullanıcı ' . ($isSilent ? 'sessizce (bildirimsiz) ' : '') . $target . ' hedefine yönlendiriliyor.' . ($shouldClose ? '' : ' (Pop-up açık tutuldu)'))
                        ->success()
                        ->send();

                    if (! $shouldClose) {
                        $action->arguments([]);
                        $action->halt();
                    }
                }),

            // Toplu Duyuru / Kupon
            Action::make('broadcast_offer')
                ->label('💬 Herkese Canlı Fırsat / Kupon Gönder')
                ->color('success')
                ->icon('heroicon-o-gift')
                ->modalWidth(Width::TwoExtraLarge)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::End)
                ->modalHeading('💬 Herkese Canlı Fırsat / Kupon Gönder')
                ->form([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->default('🎉 Size Özel Günün Sürpriz Fırsatı!')
                        ->required(),

                    Textarea::make('message')
                        ->label('Mesaj')
                        ->default('Tüm patenli ayakkabılarda anında geçerli %10 indirim kuponunuz:')
                        ->required(),

                    TextInput::make('coupon_code')
                        ->label('Kupon Kodu')
                        ->default('CANLI10'),

                    TextInput::make('action_button')
                        ->label('Buton Metni')
                        ->default('Modelleri İncele'),

                    TextInput::make('action_url')
                        ->label('Buton Linki')
                        ->default('/patenli-ayakkabilar'),
                ])
                ->action(function (array $data) {
                    $visitors = ActiveVisitor::online()->get();
                    foreach ($visitors as $v) {
                        $v->queueOffer(
                            $data['title'],
                            $data['message'],
                            $data['coupon_code'] ?? null,
                            $data['action_button'] ?? null,
                            $data['action_url'] ?? null
                        );
                    }

                    Notification::make()
                        ->title('Fırsat Gönderildi')
                        ->body($visitors->count() . ' aktif kullanıcının ekranına bildirim ulaştırıldı.')
                        ->success()
                        ->send();
                }),

            // Hazır Ses Kayıtları Yönetimi
            Action::make('manage_voice_records')
                ->label('🎙️ Hazır Ses Kayıtları')
                ->color('warning')
                ->icon('heroicon-o-microphone')
                ->url(fn () => CannedVoiceMessageResource::getUrl('index'))
                ->tooltip('Hazır anons şablonlarını yönetin, ses dosyası yükleyin ve dinleyin'),

            // Toplu Sesli Anons (Tüm Canlı Ziyaretçilere)
            Action::make('broadcast_voice')
                ->label('🎙️ Herkese Canlı Sesli Anons')
                ->color('warning')
                ->icon('heroicon-o-speaker-wave')
                ->modalWidth(Width::TwoExtraLarge)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::End)
                ->modalHeading('🎙️ Sitedeki Tüm Aktif Ziyaretçilere Canlı Sesli Anons')
                ->modalDescription('Şu an sitede olan tüm aktif kullanıcılara aynı anda ses kaydınız oynatılır (isteğe bağlı görsel bildirim kartı eklenebilir).')
                ->modalSubmitActionLabel('🚀 Herkese Sesli Anons Fırlat')
                ->form([
                    Select::make('voice_source')
                        ->label('Seslendirme & Şablon Kaynağı')
                        ->native(false)
                        ->options([
                            'canned' => '🎙️ Hazır Ses Kayıtlı Şablondan Seç',
                            'custom_upload' => '📤 Şimdi Yeni Ses Kaydı Yükle (.mp3, .wav, .m4a)',
                            'browser_tts' => '🔔 Sadece Mağaza Zili & Bildirim (Ses Dosyasız)',
                        ])
                        ->default('canned')
                        ->live(),

                    Select::make('canned_message_id')
                        ->label('Hazır Sesli Anons Şablonu')
                        ->native(false)
                        ->options(function () {
                            return CannedVoiceMessage::active()->ordered()->get()->mapWithKeys(function ($item) {
                                $badge = $item->hasAudio() ? ' [🎵 Ses Kaydı Yüklü]' : ' [⚠️ Ses Kaydı Yok]';
                                return [$item->id => $item->title . $badge];
                            })->toArray();
                        })
                        ->default(fn () => CannedVoiceMessage::active()->ordered()->value('id'))
                        ->visible(fn ($get) => ($get('voice_source') ?? 'canned') === 'canned')
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($item = CannedVoiceMessage::find($state)) {
                                $set('title', $item->title);
                                $set('message', $item->message);
                                $set('coupon_code', $item->coupon_code);
                                $set('action_button', $item->action_button);
                                $set('action_url', $item->action_url);
                            }
                        }),

                    FileUpload::make('uploaded_audio')
                        ->label('Ses Kaydı Dosyası (.mp3, .wav, .m4a, .ogg)')
                        ->disk('public')
                        ->directory('voice-announcements')
                        ->acceptedFileTypes([
                            'audio/mpeg',
                            'audio/mp3',
                            'audio/wav',
                            'audio/x-wav',
                            'audio/x-m4a',
                            'audio/mp4',
                            'audio/ogg',
                            'audio/webm',
                            'audio/aac',
                        ])
                        ->maxSize(25600)
                        ->visible(fn ($get) => $get('voice_source') === 'custom_upload')
                        ->helperText('Telefonunuzdan veya mikrofonunuzdan kaydettiğiniz ses dosyasını yükleyin. Ziyaretçilere bu ses dinletilecektir.'),

                    Select::make('sound_type')
                        ->label('Ses & Bildirim Modu')
                        ->native(false)
                        ->options([
                            'speech_only' => '🎵 Sadece Sesi Oynat (Bildirim Kartsız / Otomatik Çal)',
                            'speech_with_card' => '💬 Sesi Oynat + Görsel Bildirim Kartı Göster',
                            'chime_with_card' => '🔔 Mağaza Zili + Sesi Oynat + Görsel Kart Göster',
                        ])
                        ->default('speech_only')
                        ->required()
                        ->live(),

                    TextInput::make('title')
                        ->label('Anons Başlığı')
                        ->default('🎙️ Patenli Ayakkabılar Mağaza Anonsu')
                        ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card']))
                        ->required(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card'])),

                    Textarea::make('message')
                        ->label('Seslendirilecek ve Gösterilecek Mesaj')
                        ->rows(3)
                        ->default('Değerli ziyaretçilerimiz, Patenli Ayakkabılar\'a hoş geldiniz! Beğendiğiniz modellerde bugün geçerli sürpriz fırsatları kaçırmayın, keyifli alışverişler dileriz!')
                        ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card']) || $get('voice_source') === 'browser_tts')
                        ->required(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card']) || $get('voice_source') === 'browser_tts'),

                    TextInput::make('coupon_code')
                        ->label('Kupon Kodu (Opsiyonel)')
                        ->placeholder('Örn: CANLI10')
                        ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card'])),

                    TextInput::make('action_button')
                        ->label('Buton Metni (Opsiyonel)')
                        ->default('Çok Satanları İncele')
                        ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card'])),

                    TextInput::make('action_url')
                        ->label('Buton Linki (Opsiyonel)')
                        ->default('/patenli-ayakkabilar')
                        ->visible(fn ($get) => in_array($get('sound_type'), ['speech_with_card', 'chime_with_card'])),
                ])
                ->action(function (array $data) {
                    $audioUrl = null;
                    if (($data['voice_source'] ?? 'canned') === 'custom_upload' && !empty($data['uploaded_audio'])) {
                        $audioUrl = asset('storage/' . ltrim($data['uploaded_audio'], '/'));
                    } elseif (($data['voice_source'] ?? 'canned') === 'canned' && !empty($data['canned_message_id'])) {
                        $canned = CannedVoiceMessage::find($data['canned_message_id']);
                        $audioUrl = $canned?->audio_url;
                    }

                    $soundMode = $data['sound_type'] ?? 'speech_only';
                    $showCard = in_array($soundMode, ['speech_with_card', 'chime_with_card']);
                    $chime = ($soundMode === 'chime_with_card') ? 'chime_and_speech' : 'speech_only';

                    $visitors = ActiveVisitor::online()->get();
                    foreach ($visitors as $v) {
                        $v->queueVoiceMessage(
                            $data['message'] ?? '',
                            $data['title'] ?? '🎙️ Patenli Ayakkabılar Mağaza Anonsu',
                            $chime,
                            $showCard ? ($data['coupon_code'] ?? null) : null,
                            $showCard ? ($data['action_button'] ?? null) : null,
                            $showCard ? ($data['action_url'] ?? null) : null,
                            $audioUrl,
                            $showCard
                        );
                    }

                    Notification::make()
                        ->title('Toplu Sesli Anons İletildi! 🎙️')
                        ->body($visitors->count() . ' aktif ziyaretçinin ekranında ' . ($showCard ? 'ses ve görsel kart açılacak.' : 'kart olmadan yalnızca ses otomatik çalacak.'))
                        ->success()
                        ->send();
                }),

            // Engellenen Ziyaretçiler (Kara Liste)
            Action::make('manage_blocked')
                ->label(function () {
                    $count = ActiveVisitor::where('is_blocked', true)->count();
                    return $count > 0 ? "🚫 Engellenenler ({$count})" : '🚫 Engellenenler (Kara Liste)';
                })
                ->color(fn () => ActiveVisitor::where('is_blocked', true)->exists() ? 'danger' : 'gray')
                ->icon('heroicon-o-no-symbol')
                ->modalHeading('🚫 Engellenen Ziyaretçiler & Kara Liste')
                ->modalDescription('Burada erişimi engellenmiş tüm IP adreslerini ve ziyaretçileri görebilir, tek tıkla engellerini kaldırabilirsiniz.')
                ->modalWidth('4xl')
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->modalFooterActionsAlignment(\Filament\Support\Enums\Alignment::End)
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Kapat')
                ->modalContent(fn () => view('filament.pages.partials.blocked-visitors-modal', [
                    'blockedVisitors' => ActiveVisitor::where('is_blocked', true)->orderByDesc('updated_at')->get(),
                ])),

            // Eski Kayıtları Temizle
            Action::make('clear_old')
                ->label('🧹 Eski Kayıtları Temizle')
                ->color('gray')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action(function () {
                    $count = ActiveVisitor::where('last_heartbeat_at', '<', now()->subHours(24))
                        ->where('is_blocked', false)
                        ->delete();
                    Notification::make()->title("{$count} eski kayıt temizlendi.")->success()->send();
                }),
        ];
    }

    public function unblockVisitorById(int $id): void
    {
        $visitor = ActiveVisitor::find($id);
        if ($visitor) {
            $ip = $visitor->ip_address;
            $visitor->unblockVisitor();
            Notification::make()
                ->title('Engelleme Kaldırıldı')
                ->body(($ip ? "IP: {$ip} — " : '') . 'Ziyaretçinin erişim engeli başarıyla kaldırıldı.')
                ->success()
                ->send();
        }
    }

    public function unblockIp(string $ip): void
    {
        $visitors = ActiveVisitor::where('ip_address', $ip)->get();
        foreach ($visitors as $v) {
            $v->unblockVisitor();
        }

        Notification::make()
            ->title('IP Engeli Kaldırıldı')
            ->body("{$ip} IP adresine ait tüm engellemeler kaldırıldı.")
            ->success()
            ->send();
    }

    public function blockCustomIp(string $ip): void
    {
        $ip = trim($ip);
        if (empty($ip)) {
            Notification::make()->title('Lütfen geçerli bir IP adresi girin.')->warning()->send();
            return;
        }

        $visitors = ActiveVisitor::where('ip_address', $ip)->get();
        if ($visitors->isNotEmpty()) {
            foreach ($visitors as $v) {
                $v->blockVisitor();
            }
        } else {
            ActiveVisitor::create([
                'visitor_token' => 'banned_ip_' . md5($ip . microtime()),
                'ip_address' => $ip,
                'is_blocked' => true,
                'first_seen_at' => now(),
                'last_heartbeat_at' => now(),
                'pending_command' => [
                    'id' => 'cmd_' . uniqid(),
                    'action' => 'blocked',
                    'message' => 'Web sitemize erişiminiz sınırlandırılmıştır.',
                    'created_at' => now()->toIso8601String(),
                ],
            ]);
        }

        Notification::make()
            ->title('IP Engellendi')
            ->body("{$ip} IP adresi engellendi.")
            ->danger()
            ->send();
    }

    /**
     * URL'yi normalize eder (Site içi veya site dışı linkleri akıllıca düzenler)
     */
    public static function normalizeUrl(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '/';
        }

        // Protokol zaten varsa (https://, http://, whatsapp://, mailto:, tel:)
        if (preg_match('#^(https?://|whatsapp://|tel:|mailto:)#i', $url)) {
            return $url;
        }

        // Domain veya bilinen harici servis desenleri
        if (
            str_starts_with($url, '//') ||
            preg_match('#^(wa\.me|api\.whatsapp\.com|instagram\.com|www\.|t\.me|[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})#i', $url)
        ) {
            return 'https://' . ltrim($url, '/');
        }

        // Site içi göreceli yol: Başında / yoksa ekle
        if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
        }

        return $url;
    }

    /**
     * Sadece kayıtlı sosyal medya ve iletişim kanalları için renkli & ikonlu buton seçenekleri (3x3 Mükemmel Hizalı Izgara)
     */
    public static function getSocialChannelOptions(): array
    {
        return [
            'whatsapp' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(37, 211, 102, 0.15); color: #25D366;">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">WhatsApp</span>
                        <span class="channel-card-subtitle">Destek Sohbeti</span>
                    </div>
                </div>
            '),

            'call' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(2, 132, 199, 0.15); color: #0284c7;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">Telefon Arama</span>
                        <span class="channel-card-subtitle">Hemen Ara (tel:)</span>
                    </div>
                </div>
            '),

            'instagram' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(225, 48, 108, 0.15); color: #e1306c;">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">Instagram</span>
                        <span class="channel-card-subtitle">@patenliayakkabilar</span>
                    </div>
                </div>
            '),

            'tiktok' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(254, 44, 85, 0.15); color: #fe2c55;">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">TikTok</span>
                        <span class="channel-card-subtitle">@patenliayakkabilar</span>
                    </div>
                </div>
            '),

            'telegram' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(34, 158, 217, 0.15); color: #229ed9;">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.196 1.006.128.832.942z"/>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">Telegram</span>
                        <span class="channel-card-subtitle">Kanal & Destek</span>
                    </div>
                </div>
            '),

            'facebook' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(24, 119, 242, 0.15); color: #1877f2;">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">Facebook</span>
                        <span class="channel-card-subtitle">Resmi Sayfamız</span>
                    </div>
                </div>
            '),

            'search' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(234, 88, 12, 0.15); color: #ea580c;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">Sitede Arama</span>
                        <span class="channel-card-subtitle">Kelime / Ürün Ara</span>
                    </div>
                </div>
            '),

            'page' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">Sayfa / Kategori</span>
                        <span class="channel-card-subtitle">Vitrin, Sepet, Model</span>
                    </div>
                </div>
            '),

            'custom' => new HtmlString('
                <div class="channel-card-row">
                    <div class="channel-card-icon" style="background: rgba(129, 140, 248, 0.15); color: #818cf8;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                    </div>
                    <div class="channel-card-info">
                        <span class="channel-card-title">Özel Link</span>
                        <span class="channel-card-subtitle">Manuel URL Adresi</span>
                    </div>
                </div>
            '),
        ];
    }

    /**
     * Doğrulanmış hedef sayfa ve link seçeneklerini döner.
     */
    public static function getTargetUrlOptions(): array
    {
        $options = [
            '⚡ POPÜLER VE HIZLI DÖNÜŞÜM SAYFALARI' => [
                '/' => '🏠 Ana Sayfa (Vitrin)',
                '/patenli-ayakkabilar' => '👟 Tüm Modeller (Katalog & Çok Satanlar)',
                '/checkout' => '🛒 Sepetim & Ödeme Sayfası (Kasa)',
                '/iletisim' => '📞 İletişim & Canlı Destek Sayfası',
            ],
        ];

        try {
            $categories = \App\Models\Category::where('status', true)->orderBy('id')->get();
            if ($categories->isNotEmpty()) {
                $catOptions = [];
                foreach ($categories as $cat) {
                    $icon = match (true) {
                        str_contains($cat->slug, 'kiz') => '👧',
                        str_contains($cat->slug, 'erkek') => '👦',
                        str_contains($cat->slug, 'cocuk') => '🧒',
                        str_contains($cat->slug, 'kadin') => '👩',
                        default => '🏷️',
                    };
                    $catOptions['/kategori/' . $cat->slug] = "{$icon} {$cat->name} Modelleri";
                }
                $options['📂 KATEGORİ SAYFALARI'] = $catOptions;
            }
        } catch (\Throwable $e) {
            // Hata durumunda varsayılan kategori linkleri
            $options['📂 KATEGORİ SAYFALARI'] = [
                '/kategori/erkek-cocuk' => '👦 Erkek Çocuk Modelleri',
                '/kategori/kiz-cocuk' => '👧 Kız Çocuk Modelleri',
            ];
        }

        return $options;
    }

    /**
     * Yönlendirme hedefi için tam URL çözer (WhatsApp, Telefon Arama, Instagram, TikTok, Telegram, Facebook, Arama, Sayfa veya Özel Link)
     */
    public static function resolveRedirectUrl(array $data): string
    {
        if (!empty($data['quick_target'])) {
            return self::normalizeUrl($data['quick_target']);
        }

        if (!empty($data['bulk_target'])) {
            return self::normalizeUrl($data['bulk_target']);
        }

        if (!empty($data['target_url'])) {
            return self::normalizeUrl($data['target_url']);
        }

        $channel = $data['channel'] ?? 'whatsapp';

        switch ($channel) {
            case 'whatsapp':
                $rawNumber = (string) ($data['whatsapp_number'] ?? \App\Models\Setting::where('key', 'footer_whatsapp')->value('value') ?: '905551234567');
                $rawNumber = preg_replace('/[^0-9]/', '', $rawNumber);
                if (strlen($rawNumber) === 10 && str_starts_with($rawNumber, '5')) {
                    $rawNumber = '90' . $rawNumber;
                } elseif (strlen($rawNumber) === 11 && str_starts_with($rawNumber, '0')) {
                    $rawNumber = '90' . substr($rawNumber, 1);
                }
                $url = 'https://wa.me/' . $rawNumber;
                if (!empty($data['whatsapp_message'])) {
                    $url .= '?text=' . urlencode($data['whatsapp_message']);
                }
                return $url;

            case 'call':
                $rawPhone = (string) ($data['phone_number'] ?? \App\Models\Setting::where('key', 'company_phone')->value('value') ?: '08503080000');
                $cleanDigits = preg_replace('/[^\+0-9]/', '', $rawPhone);
                return 'tel:' . $cleanDigits;

            case 'instagram':
                $acc = (string) ($data['instagram_account'] ?? \App\Models\Setting::where('key', 'footer_instagram')->value('value') ?: 'https://www.instagram.com/patenliayakkabilar');
                if (!str_starts_with($acc, 'http')) {
                    $acc = 'https://instagram.com/' . ltrim($acc, '@');
                }
                return $acc;

            case 'tiktok':
                $acc = (string) ($data['tiktok_account'] ?? \App\Models\Setting::where('key', 'footer_tiktok')->value('value') ?: 'https://www.tiktok.com/@patenliayakkabilar');
                if (!str_starts_with($acc, 'http')) {
                    $acc = 'https://tiktok.com/@' . ltrim($acc, '@');
                }
                return $acc;

            case 'facebook':
                $acc = (string) ($data['facebook_page'] ?? \App\Models\Setting::where('key', 'footer_facebook')->value('value') ?: 'https://facebook.com/patenliayakkabilar');
                if (!str_starts_with($acc, 'http')) {
                    $acc = 'https://facebook.com/' . ltrim($acc, '/');
                }
                return $acc;

            case 'telegram':
                $acc = (string) ($data['telegram_account'] ?? 'https://t.me/patenliayakkabilar');
                if (!str_starts_with($acc, 'http')) {
                    $acc = 'https://t.me/' . ltrim($acc, '@');
                }
                return $acc;

            case 'search':
                $q = trim((string) ($data['search_query'] ?? ''));
                return '/patenli-ayakkabilar' . (!empty($q) ? '?search=' . urlencode($q) : '');

            case 'page':
                return self::normalizeUrl($data['site_page'] ?? '/checkout');

            case 'custom':
                return self::normalizeUrl($data['custom_url'] ?? '/');

            default:
                return '/';
        }
    }

    /**
     * Sosyal medya, telefon arama ve sayfa yönlendirme form alanları (Hizalı ve Düzenli Yapı)
     */
    public static function getRedirectFormSchema(): array
    {
        return [
            Radio::make('channel')
                ->label('🚀 Yönlendirilecek Kanal veya Hedefi Seçin')
                ->options(self::getSocialChannelOptions())
                ->extraAttributes(['class' => 'channel-selection-grid'])
                ->columns([
                    'default' => 3,
                    'sm' => 3,
                    'md' => 3,
                    'lg' => 3,
                ])
                ->default('whatsapp')
                ->live(),

            // 1. WhatsApp Alanları
            TextInput::make('whatsapp_number')
                ->label('💬 WhatsApp Numarası veya wa.me Linki')
                ->default(fn () => (string) (\App\Models\Setting::where('key', 'footer_whatsapp')->value('value') ?: '905551234567'))
                ->placeholder('905xxxxxxxxx')
                ->helperText('💡 Ziyaretçinin cihazında doğrudan WhatsApp sohbeti başlatılır.')
                ->visible(fn ($get) => $get('channel') === 'whatsapp')
                ->required(fn ($get) => $get('channel') === 'whatsapp'),

            TextInput::make('whatsapp_message')
                ->label('Hazır Sohbet Başlangıç Mesajı (Opsiyonel)')
                ->placeholder('Örn: Merhaba, patenli ayakkabılar hakkında danışmak istiyorum...')
                ->visible(fn ($get) => $get('channel') === 'whatsapp'),

            // 2. Telefon Arama Alanı
            TextInput::make('phone_number')
                ->label('📞 Doğrudan Aranacak Telefon Numarası')
                ->default(fn () => (string) (\App\Models\Setting::where('key', 'company_phone')->value('value') ?: '08503080000'))
                ->placeholder('+90850xxxxxxx veya 05xxxxxxxxx')
                ->helperText('💡 Ziyaretçi yönlendirildiğinde doğrudan arama ekranı açılır (tel: bağlantısı).')
                ->visible(fn ($get) => $get('channel') === 'call')
                ->required(fn ($get) => $get('channel') === 'call'),

            // 3. Instagram Alanı
            TextInput::make('instagram_account')
                ->label('📸 Instagram Profil Linki veya Kullanıcı Adı')
                ->default(fn () => (string) (\App\Models\Setting::where('key', 'footer_instagram')->value('value') ?: 'https://instagram.com/patenliayakkabilar'))
                ->helperText('💡 Ziyaretçi resmi Instagram hesabınıza yönlendirilir.')
                ->visible(fn ($get) => $get('channel') === 'instagram')
                ->required(fn ($get) => $get('channel') === 'instagram'),

            // 4. TikTok Alanı
            TextInput::make('tiktok_account')
                ->label('🎵 TikTok Profil Linki veya Kullanıcı Adı')
                ->default(fn () => (string) (\App\Models\Setting::where('key', 'footer_tiktok')->value('value') ?: 'https://tiktok.com/@patenliayakkabilar'))
                ->helperText('💡 Ziyaretçi resmi TikTok hesabınıza yönlendirilir.')
                ->visible(fn ($get) => $get('channel') === 'tiktok')
                ->required(fn ($get) => $get('channel') === 'tiktok'),

            // 5. Telegram Alanı
            TextInput::make('telegram_account')
                ->label('✈️ Telegram Kanal veya Destek Linki')
                ->default('https://t.me/patenliayakkabilar')
                ->helperText('💡 Ziyaretçi resmi Telegram kanalınıza yönlendirilir.')
                ->visible(fn ($get) => $get('channel') === 'telegram')
                ->required(fn ($get) => $get('channel') === 'telegram'),

            // 6. Facebook Alanı
            TextInput::make('facebook_page')
                ->label('📘 Facebook Sayfa Linki')
                ->default(fn () => (string) (\App\Models\Setting::where('key', 'footer_facebook')->value('value') ?: 'https://facebook.com/patenliayakkabilar'))
                ->helperText('💡 Ziyaretçi resmi Facebook sayfanıza aktarılır.')
                ->visible(fn ($get) => $get('channel') === 'facebook')
                ->required(fn ($get) => $get('channel') === 'facebook'),

            // 7. Site İçi Arama Alanı
            TextInput::make('search_query')
                ->label('🔍 Sitede Otomatik Aranacak Kelime / Ürün')
                ->placeholder('Örn: ışıklı, 4 tekerlekli, pembe...')
                ->helperText('💡 Ziyaretçi sitede doğrudan bu arama kelimesinin sonuç sayfasına aktarılır.')
                ->visible(fn ($get) => $get('channel') === 'search')
                ->required(fn ($get) => $get('channel') === 'search'),

            // 8. Site İçi Sayfa / Kategori Seçimi
            Select::make('site_page')
                ->label('📄 Yönlendirilecek Sayfa veya Kategori')
                ->native(false)
                ->searchable()
                ->options(self::getTargetUrlOptions())
                ->default('/checkout')
                ->helperText('💡 Ziyaretçi sitedeki seçtiğiniz kategoriye, vitrine veya ödeme sayfasına aktarılır.')
                ->visible(fn ($get) => $get('channel') === 'page')
                ->required(fn ($get) => $get('channel') === 'page'),

            // 9. Manuel Özel URL
            TextInput::make('custom_url')
                ->label('🌐 Manuel Özel Web Linki (URL)')
                ->placeholder('https://... veya /sayfa')
                ->helperText('İstediğiniz herhangi bir tam web bağlantısı veya site içi adres.')
                ->visible(fn ($get) => $get('channel') === 'custom')
                ->required(fn ($get) => $get('channel') === 'custom'),

            // Yönlendirme Şekli ve Bildirim Ayarları
            Select::make('redirect_mode')
                ->label('Yönlendirme Şekli')
                ->native(false)
                ->options([
                    'silent' => '⚡ Bildirim Göstermeden Doğrudan Yönlendir (Sessiz / Anında)',
                    'notify' => '💬 Bilgilendirme Pop-up\'ı Göster (Geri Sayım & Mesaj ile)',
                ])
                ->default('silent')
                ->live()
                ->helperText('Bildirim göstermeden seçeneğinde ziyaretçi ekranında herhangi bir uyarı penceresi gösterilmez; anında ilgili adrese aktarılır.'),

            TextInput::make('redirect_message')
                ->label('Kullanıcıya Gösterilecek Mesaj (Opsiyonel)')
                ->placeholder('Örn: Sizi müşteri destek hattımıza aktarıyoruz...')
                ->visible(fn ($get) => $get('redirect_mode') === 'notify'),

            Select::make('countdown')
                ->label('Geri Sayım Süresi')
                ->native(false)
                ->options([
                    '0' => 'Anında Yönlendir (0 sn)',
                    '3' => '3 Saniye Geri Sayım',
                    '5' => '5 Saniye Geri Sayım',
                ])
                ->default('3')
                ->visible(fn ($get) => $get('redirect_mode') === 'notify'),
        ];
    }
}


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

    public function mount(): void
    {
        $saved = request()->cookie('av_view_mode', session('av_view_mode', 'list'));
        $this->viewMode = in_array($saved, ['list', 'grid']) ? $saved : 'list';
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
            ->query(
                ActiveVisitor::query()
                    ->with(['user', 'cart.items.product.images'])
                    ->where(function (Builder $query) {
                        $query->where('last_heartbeat_at', '>=', now()->subHours(24))
                            ->orWhere('is_blocked', true);
                    })
                    ->when($this->activeCardFilter === 'online', fn($q) => $q->online())
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
                    ->modalHeading('🚀 Ziyaretçiyi Sayfaya Yönlendir')
                    ->modalDescription('Ziyaretçiyi istediğiniz adrese aktarın. "Şimdi Yönlendir" ile pencere açık kalır; "Şimdi Yönlendir ve Çık" ile işlem tamamlanıp pencere kapanır.')
                    ->modalSubmitActionLabel('Şimdi Yönlendir')
                    ->modalSubmitAction(fn (Action $action) => $action->icon('heroicon-o-paper-airplane'))
                    ->extraModalFooterActions(fn (Action $action): array => [
                        $action->makeModalSubmitAction('force_redirect_and_close', ['close' => true])
                            ->label('Şimdi Yönlendir ve Çık')
                            ->color('gray')
                            ->icon('heroicon-o-arrow-right-on-rectangle'),
                    ])
                    ->form([
                        Select::make('quick_target')
                            ->label('Hedef Sayfa veya Site Dışı Link')
                            ->native(false)
                            ->options(self::getTargetUrlOptions())
                            ->default('/checkout')
                            ->live(),

                        TextInput::make('external_url')
                            ->label('🌐 Site Dışı Harici Link / Web Adresi')
                            ->placeholder('https://instagram.com/..., https://wa.me/... veya https://trendyol.com/...')
                            ->helperText('💡 Ziyaretçi doğrudan siteniz dışındaki bu adrese aktarılır (WhatsApp, Instagram, Pazaryeri vb.). https:// yazmasanız da sistem otomatik tamamlar.')
                            ->visible(fn ($get) => $get('quick_target') === 'custom_external')
                            ->required(fn ($get) => $get('quick_target') === 'custom_external'),

                        TextInput::make('custom_url')
                            ->label('🔗 Özel Site İçi Sayfa Linki')
                            ->placeholder('/urun/ornek-paten veya sayfa adresi')
                            ->helperText('Siteniz içerisindeki herhangi bir sayfa yolu.')
                            ->visible(fn ($get) => $get('quick_target') === 'custom')
                            ->required(fn ($get) => $get('quick_target') === 'custom'),

                        Select::make('redirect_mode')
                            ->label('Yönlendirme Şekli')
                            ->native(false)
                            ->options([
                                'silent' => '⚡ Bildirim Göstermeden Doğrudan Yönlendir (Sessiz / Anında)',
                                'notify' => '💬 Bilgilendirme Pop-up\'ı Göster (Geri Sayım & Mesaj ile)',
                            ])
                            ->default('silent')
                            ->live()
                            ->helperText('Bildirim göstermeden seçeneğinde ziyaretçiye herhangi bir uyarı veya pencere gösterilmez; anında hedef sayfaya / harici adrese yönlendirilir.'),

                        TextInput::make('redirect_message')
                            ->label('Kullanıcıya Gösterilecek Mesaj (Opsiyonel)')
                            ->placeholder('Örn: Sizi WhatsApp destek hattımıza aktarıyoruz...')
                            ->visible(fn ($get) => $get('redirect_mode') === 'notify'),

                        Select::make('countdown')
                            ->label('Geri Sayım')
                            ->native(false)
                            ->options([
                                '0' => 'Anında Yönlendir (0 sn)',
                                '3' => '3 Saniye Geri Sayım',
                                '5' => '5 Saniye Geri Sayım',
                            ])
                            ->default('3')
                            ->visible(fn ($get) => $get('redirect_mode') === 'notify'),
                    ])
                    ->action(function (ActiveVisitor $record, array $data, array $arguments, Action $action) {
                        $target = match($data['quick_target'] ?? 'custom') {
                            'custom_external' => self::normalizeUrl($data['external_url'] ?? ''),
                            'custom' => self::normalizeUrl($data['custom_url'] ?? '/'),
                            default => self::normalizeUrl($data['quick_target'] ?? '/checkout'),
                        };
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
                            ->body('Ziyaretçi ' . ($isSilent ? 'sessizce (bildirimsiz) ' : '') . $target . ' adresine yönlendiriliyor.' . ($shouldClose ? '' : ' (Pop-up açık tutuldu)'))
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
                ->modalHeading('📢 Sitedeki Tüm Aktif Ziyaretçileri Toplu Yönlendir')
                ->modalDescription('Sitedeki tüm aktif kullanıcılara tek tıkla yönlendirme emri gönderir. "Şimdi Yönlendir" ile pencere açık kalır; "Şimdi Yönlendir ve Çık" ile işlem tamamlanıp pencere kapanır.')
                ->modalSubmitActionLabel('Şimdi Yönlendir')
                ->modalSubmitAction(fn (Action $action) => $action->icon('heroicon-o-paper-airplane'))
                ->extraModalFooterActions(fn (Action $action): array => [
                    $action->makeModalSubmitAction('bulk_redirect_and_close', ['close' => true])
                        ->label('Şimdi Yönlendir ve Çık')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-right-on-rectangle'),
                ])
                ->form([
                    Select::make('bulk_target')
                        ->label('Hedef Sayfa veya Site Dışı Link')
                        ->native(false)
                        ->options(self::getTargetUrlOptions())
                        ->default('/checkout')
                        ->live(),

                    TextInput::make('external_url')
                        ->label('🌐 Site Dışı Harici Link / Web Adresi')
                        ->placeholder('https://instagram.com/..., https://wa.me/... veya https://trendyol.com/...')
                        ->helperText('💡 Tüm aktif ziyaretçiler doğrudan siteniz dışındaki bu adrese aktarılacaktır. https:// yazmasanız da otomatik eklenir.')
                        ->visible(fn ($get) => $get('bulk_target') === 'custom_external')
                        ->required(fn ($get) => $get('bulk_target') === 'custom_external'),

                    TextInput::make('custom_url')
                        ->label('🔗 Özel Site İçi Sayfa Linki')
                        ->placeholder('/urun/ornek-paten veya /blog')
                        ->helperText('Siteniz içerisindeki herhangi bir sayfa yolu.')
                        ->visible(fn ($get) => $get('bulk_target') === 'custom')
                        ->required(fn ($get) => $get('bulk_target') === 'custom'),

                    Select::make('redirect_mode')
                        ->label('Yönlendirme Şekli')
                        ->native(false)
                        ->options([
                            'silent' => '⚡ Bildirim Göstermeden Doğrudan Yönlendir (Sessiz / Anında)',
                            'notify' => '💬 Bilgilendirme Pop-up\'ı Göster (Geri Sayım & Mesaj ile)',
                        ])
                        ->default('silent')
                        ->live()
                        ->helperText('Bildirim göstermeden seçeneğinde kullanıcılara pop-up gösterilmez, doğrudan hedef adrese yönlendirilirler.'),

                    TextInput::make('bulk_message')
                        ->label('Kullanıcılara Gösterilecek Mesaj (Opsiyonel)')
                        ->placeholder('Örn: Fırsat ürünlerimize aktarılıyorsunuz...')
                        ->visible(fn ($get) => $get('redirect_mode') === 'notify'),
                ])
                ->action(function (array $data, array $arguments, Action $action) {
                    $target = match($data['bulk_target'] ?? 'custom') {
                        'custom_external' => self::normalizeUrl($data['external_url'] ?? ''),
                        'custom' => self::normalizeUrl($data['custom_url'] ?? '/'),
                        default => self::normalizeUrl($data['bulk_target'] ?? '/checkout'),
                    };
                    $isSilent = ($data['redirect_mode'] ?? 'silent') === 'silent';
                    $showNotice = !$isSilent;
                    $shouldClose = (bool) ($arguments['close'] ?? false);
                    $visitors = ActiveVisitor::online()->get();

                    foreach ($visitors as $v) {
                        $v->queueRedirect(
                            $target,
                            $showNotice ? ($data['bulk_message'] ?? null) : null,
                            $showNotice ? 3 : 0,
                            $showNotice
                        );
                    }

                    Notification::make()
                        ->title('Toplu Yönlendirme Başlatıldı')
                        ->body($visitors->count() . ' aktif kullanıcı ' . ($isSilent ? 'sessizce (bildirimsiz) ' : '') . $target . ' adresine yönlendiriliyor.' . ($shouldClose ? '' : ' (Pop-up açık tutuldu)'))
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
     * Yönlendirme ve anonslarda kullanılacak doğrulanmış güncel sayfa, site dışı link ve kategori seçenekleri
     */
    public static function getTargetUrlOptions(): array
    {
        $options = [
            '/' => '🏠 Ana Sayfa',
            '/patenli-ayakkabilar' => '👟 Tüm Modeller (Katalog & Çok Satanlar)',
            '/checkout' => '🛒 Sepetim & Ödeme Sayfası (Kasa)',
        ];

        // Kategoriler
        try {
            $categories = \App\Models\Category::where('status', true)->orderBy('id')->get();
            if ($categories->isEmpty()) {
                $options['/kategori/erkek-cocuk'] = '👦 Erkek Çocuk Modelleri';
                $options['/kategori/kiz-cocuk'] = '👧 Kız Çocuk Modelleri';
            } else {
                foreach ($categories as $cat) {
                    $icon = match (true) {
                        str_contains($cat->slug, 'kiz') => '👧',
                        str_contains($cat->slug, 'erkek') => '👦',
                        str_contains($cat->slug, 'cocuk') => '🧒',
                        str_contains($cat->slug, 'kadin') => '👩',
                        default => '🏷️',
                    };
                    $options['/kategori/' . $cat->slug] = "{$icon} {$cat->name} Modelleri";
                }
            }
        } catch (\Throwable $e) {
            $options['/kategori/erkek-cocuk'] = '👦 Erkek Çocuk Modelleri';
            $options['/kategori/kiz-cocuk'] = '👧 Kız Çocuk Modelleri';
        }

        $options['/iletisim'] = '📞 İletişim & Canlı Destek';
        $options['custom'] = '🔗 Özel Site İçi Sayfa Linki (/sayfa-adi)';
        $options['custom_external'] = '🌐 Site Dışı Link (Harici Web Sitesi, WhatsApp, Instagram vb.)';

        return $options;
    }

    /**
     * Yönlendirme hedefi için tam URL çözer (WhatsApp, Telefon Arama, Instagram, TikTok, Telegram, Facebook, Arama vb.)
     */
    public static function resolveRedirectUrl(array $data): string
    {
        $channel = $data['channel'] ?? 'whatsapp';

        switch ($channel) {
            case 'whatsapp':
                $rawNumber = preg_replace('/[^0-9]/', '', (string) ($data['whatsapp_number'] ?? ''));
                if (empty($rawNumber)) {
                    $rawNumber = preg_replace('/[^0-9]/', '', (string) (\App\Models\Setting::where('key', 'footer_whatsapp')->value('value') ?: '905551234567'));
                }
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
                $rawPhone = trim((string) ($data['phone_number'] ?? ''));
                if (empty($rawPhone)) {
                    $rawPhone = (string) (\App\Models\Setting::where('key', 'company_phone')->value('value') ?: '08503080000');
                }
                $cleanDigits = preg_replace('/[^\+0-9]/', '', $rawPhone);
                return 'tel:' . $cleanDigits;

            case 'instagram':
                $acc = trim((string) ($data['instagram_account'] ?? ''));
                if (empty($acc)) {
                    $acc = (string) (\App\Models\Setting::where('key', 'footer_instagram')->value('value') ?: 'https://www.instagram.com/patenliayakkabilar');
                }
                if (!str_starts_with($acc, 'http')) {
                    $acc = 'https://instagram.com/' . ltrim($acc, '@');
                }
                return $acc;

            case 'tiktok':
                $acc = trim((string) ($data['tiktok_account'] ?? ''));
                if (empty($acc)) {
                    $acc = (string) (\App\Models\Setting::where('key', 'footer_tiktok')->value('value') ?: 'https://www.tiktok.com/@patenliayakkabilar');
                }
                if (!str_starts_with($acc, 'http')) {
                    $acc = 'https://tiktok.com/@' . ltrim($acc, '@');
                }
                return $acc;

            case 'telegram':
                $acc = trim((string) ($data['telegram_account'] ?? ''));
                if (empty($acc)) {
                    $acc = 'https://t.me/patenliayakkabilar';
                }
                if (!str_starts_with($acc, 'http')) {
                    $acc = 'https://t.me/' . ltrim($acc, '@');
                }
                return $acc;

            case 'facebook':
                $acc = trim((string) ($data['facebook_page'] ?? ''));
                if (empty($acc)) {
                    $acc = (string) (\App\Models\Setting::where('key', 'footer_facebook')->value('value') ?: 'https://facebook.com/patenliayakkabilar');
                }
                if (!str_starts_with($acc, 'http')) {
                    $acc = 'https://facebook.com/' . ltrim($acc, '/');
                }
                return $acc;

            case 'search':
                $q = trim((string) ($data['search_query'] ?? ''));
                return '/patenli-ayakkabilar' . (!empty($q) ? '?search=' . urlencode($q) : '');

            case 'checkout':
                return '/checkout';

            case 'page':
                return self::normalizeUrl($data['site_page'] ?? '/patenli-ayakkabilar');

            case 'custom':
            default:
                return self::normalizeUrl($data['custom_url'] ?? '/');
        }
    }

    /**
     * Sosyal medya, telefon arama ve sayfa yönlendirme form alanları
     */
    public static function getRedirectFormSchema(): array
    {
        $defaultPhone = (string) (\App\Models\Setting::where('key', 'company_phone')->value('value') ?: '08503080000');
        $defaultWhatsapp = (string) (\App\Models\Setting::where('key', 'footer_whatsapp')->value('value') ?: '905551234567');
        $defaultInstagram = (string) (\App\Models\Setting::where('key', 'footer_instagram')->value('value') ?: 'https://www.instagram.com/patenliayakkabilar');
        $defaultTiktok = (string) (\App\Models\Setting::where('key', 'footer_tiktok')->value('value') ?: 'https://www.tiktok.com/@patenliayakkabilar');
        $defaultFacebook = (string) (\App\Models\Setting::where('key', 'footer_facebook')->value('value') ?: 'https://facebook.com/patenliayakkabilar');

        return [
            Radio::make('channel')
                ->label('🚀 Yönlendirilecek Kanal veya Sosyal Medya Seçin')
                ->options([
                    'whatsapp' => '💬 WhatsApp Sohbet',
                    'call' => '📞 Telefon Arama (Hemen Ara)',
                    'instagram' => '📸 Instagram Profili',
                    'tiktok' => '🎵 TikTok Sayfası',
                    'telegram' => '✈️ Telegram Kanalı',
                    'facebook' => '📘 Facebook Sayfası',
                    'search' => '🔍 Site İçi Arama',
                    'checkout' => '🛒 Sepetim & Ödeme',
                    'page' => '📄 Diğer Sayfalar',
                    'custom' => '🌐 Manuel Özel Link',
                ])
                ->descriptions([
                    'whatsapp' => 'WhatsApp uygulamasını veya sohbetini açar',
                    'call' => 'Ziyaretçinin telefonunda doğrudan arama ekranını başlatır',
                    'instagram' => 'Resmi Instagram profilinizi açar',
                    'tiktok' => 'TikTok hesabınızı veya videonuzu açar',
                    'telegram' => 'Telegram destek veya duyuru kanalınızı açar',
                    'facebook' => 'Facebook sayfanızı açar',
                    'search' => 'Sitede belirttiğiniz kelime ile ürün aratır',
                    'checkout' => 'Ziyaretçiyi doğrudan kasaya & ödeme sayfasına aktarır',
                    'page' => 'Ana sayfa, katalog veya kategori sayfalarını seçin',
                    'custom' => 'Herhangi bir harici web sitesi veya özel link yazın',
                ])
                ->columns([
                    'default' => 2,
                    'sm' => 2,
                    'md' => 3,
                    'lg' => 3,
                ])
                ->default('whatsapp')
                ->live(),

            // WhatsApp Alanları
            TextInput::make('whatsapp_number')
                ->label('💬 WhatsApp Numarası veya wa.me Linki')
                ->default($defaultWhatsapp)
                ->placeholder('905xxxxxxxxx veya wa.me/...')
                ->helperText('💡 Ziyaretçinin cihazında WhatsApp sohbeti anında başlatılır.')
                ->visible(fn ($get) => ($get('channel') ?? 'whatsapp') === 'whatsapp')
                ->required(fn ($get) => ($get('channel') ?? 'whatsapp') === 'whatsapp'),

            TextInput::make('whatsapp_message')
                ->label('Hazır Sohbet Başlangıç Mesajı (Opsiyonel)')
                ->placeholder('Örn: Merhaba, patenli ayakkabılar hakkında bilgi almak istiyorum.')
                ->visible(fn ($get) => ($get('channel') ?? 'whatsapp') === 'whatsapp'),

            // Telefon Arama Alanları ("arama dahil")
            TextInput::make('phone_number')
                ->label('📞 Doğrudan Aranacak Telefon Numarası')
                ->default($defaultPhone)
                ->placeholder('0850xxxxxxx veya 05xxxxxxxxx')
                ->helperText('💡 Ziyaretçi yönlendirildiğinde özellikle cep telefonunda doğrudan arama ekranı tetiklenir (tel: bağlantısı).')
                ->visible(fn ($get) => $get('channel') === 'call')
                ->required(fn ($get) => $get('channel') === 'call'),

            // Instagram Alanı
            TextInput::make('instagram_account')
                ->label('📸 Instagram Profil Linki veya Kullanıcı Adı')
                ->default($defaultInstagram)
                ->placeholder('patenliayakkabilar veya https://instagram.com/...')
                ->helperText('💡 Ziyaretçi resmi Instagram sayfanıza aktarılır.')
                ->visible(fn ($get) => $get('channel') === 'instagram')
                ->required(fn ($get) => $get('channel') === 'instagram'),

            // TikTok Alanı
            TextInput::make('tiktok_account')
                ->label('🎵 TikTok Profil Linki veya Kullanıcı Adı')
                ->default($defaultTiktok)
                ->placeholder('patenliayakkabilar veya https://tiktok.com/@...')
                ->visible(fn ($get) => $get('channel') === 'tiktok')
                ->required(fn ($get) => $get('channel') === 'tiktok'),

            // Telegram Alanı
            TextInput::make('telegram_account')
                ->label('✈️ Telegram Kanal / Kullanıcı Adı')
                ->default('https://t.me/patenliayakkabilar')
                ->placeholder('patenliayakkabilar veya https://t.me/...')
                ->visible(fn ($get) => $get('channel') === 'telegram')
                ->required(fn ($get) => $get('channel') === 'telegram'),

            // Facebook Alanı
            TextInput::make('facebook_page')
                ->label('📘 Facebook Sayfa Linki')
                ->default($defaultFacebook)
                ->placeholder('https://facebook.com/patenliayakkabilar')
                ->visible(fn ($get) => $get('channel') === 'facebook')
                ->required(fn ($get) => $get('channel') === 'facebook'),

            // Site İçi Arama Alanı
            TextInput::make('search_query')
                ->label('🔍 Sitede Otomatik Aranacak Kelime / Ürün')
                ->placeholder('Örn: ışıklı, 4 tekerlekli, pembe, erkek çocuk...')
                ->helperText('💡 Ziyaretçi sitede doğrudan bu kelimenin arama sonuçları sayfasına aktarılır.')
                ->visible(fn ($get) => $get('channel') === 'search')
                ->required(fn ($get) => $get('channel') === 'search'),

            // Site İçi Sayfa Seçimi
            Select::make('site_page')
                ->label('Hedef Sayfa veya Kategori')
                ->native(false)
                ->options(self::getTargetUrlOptions())
                ->default('/checkout')
                ->visible(fn ($get) => $get('channel') === 'page')
                ->required(fn ($get) => $get('channel') === 'page'),

            // Manuel Özel URL
            TextInput::make('custom_url')
                ->label('🌐 Manuel Özel Web Linki (URL)')
                ->placeholder('https://... veya /sayfa')
                ->helperText('İstediğiniz herhangi bir tam web linki veya site içi yol.')
                ->visible(fn ($get) => $get('channel') === 'custom')
                ->required(fn ($get) => $get('channel') === 'custom'),

            // Yönlendirme Şekli
            Select::make('redirect_mode')
                ->label('Yönlendirme Şekli')
                ->native(false)
                ->options([
                    'silent' => '⚡ Bildirim Göstermeden Doğrudan Yönlendir (Sessiz / Anında)',
                    'notify' => '💬 Bilgilendirme Pop-up\'ı Göster (Geri Sayım & Mesaj ile)',
                ])
                ->default('silent')
                ->live()
                ->helperText('Bildirim göstermeden seçeneğinde ziyaretçiye herhangi bir uyarı veya pencere gösterilmez; anında ilgili kanala/adrese yönlendirilir.'),

            TextInput::make('redirect_message')
                ->label('Kullanıcıya Gösterilecek Mesaj (Opsiyonel)')
                ->placeholder('Örn: Sizi WhatsApp destek hattımıza aktarıyoruz...')
                ->visible(fn ($get) => $get('redirect_mode') === 'notify'),

            Select::make('countdown')
                ->label('Geri Sayım')
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


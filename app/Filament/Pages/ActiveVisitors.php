<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CannedVoiceMessages\CannedVoiceMessageResource;
use App\Models\ActiveVisitor;
use App\Models\CannedVoiceMessage;
use App\Models\Cart;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    'highIntentCount' => 0,
                    'cartCount' => 0,
                    'cartTotal' => 0,
                    'hesitatingCount' => 0,
                    'membersCount' => 0,
                    'guestsCount' => 0,
                ];
            }

            $activeRadarQuery = ActiveVisitor::query()
                ->where('last_heartbeat_at', '>=', now()->subMinutes(15))
                ->where('is_blocked', false);

            $onlineQuery = ActiveVisitor::online()->where('is_blocked', false);

            $onlineCount = (clone $onlineQuery)->count();
            $highIntentCount = (clone $activeRadarQuery)->where('intent_score', '>=', 60)->count();
            $cartCount = (clone $activeRadarQuery)->where('cart_items_count', '>', 0)->count();
            $cartTotal = (clone $activeRadarQuery)->sum('cart_total');
            $hesitatingCount = (clone $activeRadarQuery)->where('intent_level', 'hesitating')->count();
            $membersCount = (clone $activeRadarQuery)->where(function ($q) {
                $q->whereNotNull('user_id')->orWhere('is_identified', true);
            })->count();
            $totalActive = (clone $activeRadarQuery)->count();
            $guestsCount = max(0, $totalActive - $membersCount);
            $blockedCount = ActiveVisitor::where('is_blocked', true)->count();

            return [
                'onlineCount' => $onlineCount,
                'highIntentCount' => $highIntentCount,
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
                'hesitatingCount' => $hesitatingCount,
                'membersCount' => $membersCount,
                'guestsCount' => $guestsCount,
                'totalActive' => $totalActive,
                'blockedCount' => $blockedCount,
            ];
        } catch (\Throwable $e) {
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
                        $query->where('last_heartbeat_at', '>=', now()->subMinutes(15))
                            ->orWhere('is_blocked', true);
                    })
                    ->latest('last_heartbeat_at')
            )
            ->columns([
                // 1. Ziyaretçi Kimliği & Canlı Sinyal
                TextColumn::make('visitor_identity')
                    ->label('Ziyaretçi & Sinyal')
                    ->searchable(['ip_address', 'guest_name', 'guest_email', 'guest_phone', 'referrer_host', 'utm_source', 'utm_campaign', 'user.name', 'user.email'])
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

                        return new HtmlString('
                            <div style="display:flex;align-items:flex-start;gap:12px;min-width:220px;">
                                <div style="position:relative;width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#ff4e00,#b45309);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:15px;flex-shrink:0;box-shadow:0 0 12px rgba(255,78,0,0.35);">
                                    ' . $initial . '
                                </div>
                                <div style="flex:1;">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;flex-wrap:wrap;">
                                        <span style="font-weight:800;color:#f8fafc;font-size:13px;">' . $name . '</span>
                                        ' . $badgeHtml . '
                                    </div>
                                    <div style="margin-bottom:4px;">
                                        ' . $onlineBadge . '
                                    </div>
                                    ' . $contactHtml . '
                                    <div style="font-size:11px;color:#94a3b8;display:flex;align-items:center;gap:5px;margin-top:2px;">
                                        <span>' . $deviceIcon . ' ' . e($record->browser ?? 'Tarayıcı') . '</span>
                                        <span>•</span>
                                        <span style="font-family:monospace;color:#64748b;">' . e($record->ip_address) . '</span>
                                    </div>
                                    <div style="font-size:10px;color:#64748b;margin-top:2px;">
                                        ⏱️ ' . $duration . ' (' . $pageCount . '. sayfa)
                                    </div>
                                    ' . $sourceHtml . '
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
            ->content(fn (Table $table) => view('filament.pages.partials.active-visitors-table', ['table' => $table]))
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
                                ->label('Yönlendirilecek Hedef URL')
                                ->default($strategy['target_url'] ?? '/checkout')
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
                            $record->queueRedirect(
                                $data['redirect_url'] ?? '/checkout',
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
                    ->modalSubmitActionLabel('Şimdi Yönlendir')
                    ->form([
                        Select::make('quick_target')
                            ->label('Hızlı Hedef Seçimi')
                            ->native(false)
                            ->options(self::getTargetUrlOptions())
                            ->default('/checkout')
                            ->live(),

                        TextInput::make('custom_url')
                            ->label('Özel Hedef URL')
                            ->placeholder('/urun/ornek-paten veya tam link')
                            ->visible(fn ($get) => $get('quick_target') === 'custom')
                            ->required(fn ($get) => $get('quick_target') === 'custom'),

                        TextInput::make('redirect_message')
                            ->label('Kullanıcıya Gösterilecek Mesaj (Opsiyonel)')
                            ->placeholder('Örn: Sizi fırsat ürünlerimize aktarıyoruz...')
                            ->default('Sizi özel indirim sayfasına aktarıyoruz...'),

                        Select::make('countdown')
                            ->label('Geri Sayım')
                            ->native(false)
                            ->options([
                                '0' => 'Anında Yönlendir (0 sn)',
                                '3' => '3 Saniye Geri Sayım',
                                '5' => '5 Saniye Geri Sayım',
                            ])
                            ->default('3'),
                    ])
                    ->action(function (ActiveVisitor $record, array $data) {
                        $target = $data['quick_target'] === 'custom' ? $data['custom_url'] : $data['quick_target'];
                        $record->queueRedirect(
                            $target,
                            $data['redirect_message'] ?? null,
                            (int) ($data['countdown'] ?? 3)
                        );

                        Notification::make()
                            ->title('Yönlendirme Başlatıldı')
                            ->body('Ziyaretçi ' . $target . ' adresine yönlendiriliyor.')
                            ->success()
                            ->send();
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
                    ->modalDescription('Ziyaretçinin ekranında melodili mağaza zili çalar, yazdığınız mesaj Türkçe seslendirilir (TTS) ve şık bir bildirim kartı açılır.')
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
                            ->label('Ses Efekti & Seslendirme Tipi')
                            ->native(false)
                            ->options([
                                'chime_and_speech' => '🔔 Mağaza Zili + Ses Kaydı Oynat + Görsel Kart',
                                'speech_only' => '🗣️ Sadece Ses Kaydı Oynat + Görsel Kart',
                                'chime_only' => '🔔 Sadece Dikkat Çeken Mağaza Zili + Görsel Kart',
                            ])
                            ->default('chime_and_speech')
                            ->required(),

                        TextInput::make('title')
                            ->label('Bildirim Başlığı')
                            ->default('🎙️ Mağazamıza Hoş Geldiniz!')
                            ->required(),

                        Textarea::make('message')
                            ->label('Seslendirilecek ve Gösterilecek Mesaj')
                            ->rows(3)
                            ->default('Patenli Ayakkabılar\'a hoş geldiniz! Beğendiğiniz modellerde bugün geçerli özel fırsatları kaçırmayın, keyifli alışverişler dileriz!')
                            ->required(),

                        TextInput::make('coupon_code')
                            ->label('İndirim Kuponu (Opsiyonel)')
                            ->placeholder('Örn: SESLI10'),

                        TextInput::make('action_button')
                            ->label('Buton Metni (Opsiyonel)')
                            ->default('Tüm Modelleri Gör'),

                        TextInput::make('action_url')
                            ->label('Buton Linki (Opsiyonel)')
                            ->default('/patenli-ayakkabilar'),
                    ])
                    ->action(function (ActiveVisitor $record, array $data) {
                        $audioUrl = null;
                        if (($data['voice_source'] ?? 'canned') === 'custom_upload' && !empty($data['uploaded_audio'])) {
                            $audioUrl = asset('storage/' . ltrim($data['uploaded_audio'], '/'));
                        } elseif (($data['voice_source'] ?? 'canned') === 'canned' && !empty($data['canned_message_id'])) {
                            $canned = CannedVoiceMessage::find($data['canned_message_id']);
                            $audioUrl = $canned?->audio_url;
                        }

                        $record->queueVoiceMessage(
                            $data['message'],
                            $data['title'] ?? '🎙️ Canlı Mağaza Anonsu',
                            $data['sound_type'] ?? 'chime_and_speech',
                            $data['coupon_code'] ?? null,
                            $data['action_button'] ?? null,
                            $data['action_url'] ?? null,
                            $audioUrl
                        );

                        Notification::make()
                            ->title('Sesli İleti İletildi! 🎙️')
                            ->body($record->display_name . ' adlı ziyaretçinin ekranında sesli anons çalacak.' . ($audioUrl ? ' (Özel Ses Kaydı Aktif)' : ''))
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

    protected function getHeaderActions(): array
    {
        return [
            // Toplu Yönlendirme
            Action::make('bulk_redirect')
                ->label('📢 Tüm Canlıları Toplu Yönlendir')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane')
                ->modalHeading('📢 Sitedeki Tüm Aktif Ziyaretçileri Toplu Yönlendir')
                ->modalDescription('Şu an sitede olan tüm aktif kullanıcılara tek tıkla yönlendirme emri gönderir.')
                ->form([
                    Select::make('bulk_target')
                        ->label('Hedef Sayfa')
                        ->native(false)
                        ->options(self::getTargetUrlOptions())
                        ->default('/patenli-ayakkabilar')
                        ->live(),

                    TextInput::make('custom_url')
                        ->label('Özel URL')
                        ->visible(fn ($get) => $get('bulk_target') === 'custom')
                        ->required(fn ($get) => $get('bulk_target') === 'custom'),

                    TextInput::make('bulk_message')
                        ->label('Kullanıcılara Gösterilecek Mesaj')
                        ->default('Fırsat ürünlerimize aktarılıyorsunuz...'),
                ])
                ->action(function (array $data) {
                    $target = $data['bulk_target'] === 'custom' ? $data['custom_url'] : $data['bulk_target'];
                    $visitors = ActiveVisitor::online()->get();

                    foreach ($visitors as $v) {
                        $v->queueRedirect($target, $data['bulk_message'] ?? null, 3);
                    }

                    Notification::make()
                        ->title('Toplu Yönlendirme Başlatıldı')
                        ->body($visitors->count() . ' aktif kullanıcı ' . $target . ' sayfasına yönlendiriliyor.')
                        ->success()
                        ->send();
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
                ->modalDescription('Şu an sitede olan tüm aktif kullanıcılara aynı anda melodili mağaza zili çalar ve ses kaydınız veya bildiriminiz anons edilir.')
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
                        ->label('Ses Efekti & Seslendirme Tipi')
                        ->native(false)
                        ->options([
                            'chime_and_speech' => '🔔 Mağaza Zili + Ses Kaydı Oynat + Görsel Kart',
                            'speech_only' => '🗣️ Sadece Ses Kaydı Oynat + Görsel Kart',
                            'chime_only' => '🔔 Sadece Dikkat Çeken Mağaza Zili + Görsel Kart',
                        ])
                        ->default('chime_and_speech')
                        ->required(),

                    TextInput::make('title')
                        ->label('Anons Başlığı')
                        ->default('🎙️ Patenli Ayakkabılar Mağaza Anonsu')
                        ->required(),

                    Textarea::make('message')
                        ->label('Seslendirilecek ve Gösterilecek Mesaj')
                        ->rows(3)
                        ->default('Değerli ziyaretçilerimiz, Patenli Ayakkabılar\'a hoş geldiniz! Beğendiğiniz modellerde bugün geçerli sürpriz fırsatları kaçırmayın, keyifli alışverişler dileriz!')
                        ->required(),

                    TextInput::make('coupon_code')
                        ->label('Kupon Kodu (Opsiyonel)')
                        ->placeholder('Örn: CANLI10'),

                    TextInput::make('action_button')
                        ->label('Buton Metni (Opsiyonel)')
                        ->default('Çok Satanları İncele'),

                    TextInput::make('action_url')
                        ->label('Buton Linki (Opsiyonel)')
                        ->default('/patenli-ayakkabilar'),
                ])
                ->action(function (array $data) {
                    $audioUrl = null;
                    if (($data['voice_source'] ?? 'canned') === 'custom_upload' && !empty($data['uploaded_audio'])) {
                        $audioUrl = asset('storage/' . ltrim($data['uploaded_audio'], '/'));
                    } elseif (($data['voice_source'] ?? 'canned') === 'canned' && !empty($data['canned_message_id'])) {
                        $canned = CannedVoiceMessage::find($data['canned_message_id']);
                        $audioUrl = $canned?->audio_url;
                    }

                    $visitors = ActiveVisitor::online()->get();
                    foreach ($visitors as $v) {
                        $v->queueVoiceMessage(
                            $data['message'],
                            $data['title'] ?? '🎙️ Patenli Ayakkabılar Mağaza Anonsu',
                            $data['sound_type'] ?? 'chime_and_speech',
                            $data['coupon_code'] ?? null,
                            $data['action_button'] ?? null,
                            $data['action_url'] ?? null,
                            $audioUrl
                        );
                    }

                    Notification::make()
                        ->title('Toplu Sesli Anons İletildi! 🎙️')
                        ->body($visitors->count() . ' aktif ziyaretçinin ekranına sesli anons gönderildi.' . ($audioUrl ? ' (Özel Ses Kaydı Aktif)' : ''))
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
     * Yönlendirme ve anonslarda kullanılacak doğrulanmış güncel sayfa ve kategori linkleri
     */
    public static function getTargetUrlOptions(): array
    {
        $options = [
            '/' => '🏠 Ana Sayfa',
            '/patenli-ayakkabilar' => '👟 Tüm Modeller (Katalog & Çok Satanlar)',
            '/checkout' => '🛒 Sepetim & Ödeme Sayfası',
        ];

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
        $options['custom'] = '🔗 Özel URL Yaz...';

        return $options;
    }
}


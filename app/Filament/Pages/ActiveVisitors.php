<?php

namespace App\Filament\Pages;

use App\Models\ActiveVisitor;
use App\Models\Cart;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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

            $onlineQuery = ActiveVisitor::online();

            $onlineCount = (clone $onlineQuery)->count();
            $highIntentCount = (clone $onlineQuery)->where('intent_score', '>=', 60)->count();
            $cartCount = (clone $onlineQuery)->where('cart_items_count', '>', 0)->count();
            $cartTotal = (clone $onlineQuery)->sum('cart_total');
            $hesitatingCount = (clone $onlineQuery)->where('intent_level', 'hesitating')->count();
            $membersCount = (clone $onlineQuery)->where(function ($q) {
                $q->whereNotNull('user_id')->orWhere('is_identified', true);
            })->count();
            $guestsCount = max(0, $onlineCount - $membersCount);

            return [
                'onlineCount' => $onlineCount,
                'highIntentCount' => $highIntentCount,
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
                'hesitatingCount' => $hesitatingCount,
                'membersCount' => $membersCount,
                'guestsCount' => $guestsCount,
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
            ];
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->query(
                ActiveVisitor::query()
                    ->with(['user', 'cart.items.product'])
                    ->where('last_heartbeat_at', '>=', now()->subMinutes(15))
                    ->latest('last_heartbeat_at')
            )
            ->columns([
                // 1. Ziyaretçi Kimliği & Canlı Sinyal
                TextColumn::make('visitor_identity')
                    ->label('Ziyaretçi & Sinyal')
                    ->searchable(['ip_address', 'guest_name', 'guest_email', 'guest_phone', 'user.name', 'user.email'])
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

                // 5. Önerilen Strateji
                TextColumn::make('recommended_strategy')
                    ->label('Önerilen Strateji')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $strategy = $record->recommended_strategy;
                        if (!$strategy) return '-';

                        $title = $strategy['title'] ?? 'Strateji';
                        return new HtmlString('
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;background:rgba(255,78,0,0.15);color:#ff7849;border:1px solid rgba(255,78,0,0.35);">
                                ⚡ ' . e($title) . '
                            </span>
                        ');
                    }),
            ])
            ->filters([
                SelectFilter::make('intent_filter')
                    ->label('Satış Niyeti')
                    ->options([
                        'high_intent' => '🔥 Sıcak Adaylar (Niyet >= 60)',
                        'hesitating' => '⚡ Tereddütte Olanlar',
                        'with_cart' => '🛒 Sepetinde Ürün Olanlar',
                        'members' => '👤 Kayıtlı Üyeler / Müşteriler',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === 'high_intent') {
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
                            ->options([
                                '/' => '🏠 Ana Sayfa',
                                '/patenli-ayakkabilar' => '👟 Tüm Modeller (Çok Satanlar)',
                                '/checkout' => '🛒 Sepetim & Ödeme Sayfası',
                                '/kategori/erkek-cocuk-patenli-ayakkabi' => '👦 Erkek Çocuk Patenleri',
                                '/kategori/kiz-cocuk-patenli-ayakkabi' => '👧 Kız Çocuk Patenleri',
                                '/iletisim' => '📞 İletişim & Destek',
                                'custom' => '🔗 Özel URL Yaz...',
                            ])
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
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat')
                    ->modalContent(fn (ActiveVisitor $record) => view('filament.pages.partials.visitor-journey-modal', ['record' => $record])),

                // ─── 4. Diğer Aksiyonlar ───
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
                        ->requiresConfirmation()
                        ->action(function (ActiveVisitor $record) {
                            $record->blockVisitor();
                            Notification::make()->title('Ziyaretçi engellendi.')->danger()->send();
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
                        ->options([
                            '/' => '🏠 Ana Sayfa',
                            '/patenli-ayakkabilar' => '👟 Tüm Modeller / Çok Satanlar',
                            '/checkout' => '🛒 Sepet / Ödeme Sayfası',
                            'custom' => '🔗 Özel URL...',
                        ])
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

            // Eski Kayıtları Temizle
            Action::make('clear_old')
                ->label('🧹 Eski Kayıtları Temizle')
                ->color('gray')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action(function () {
                    $count = ActiveVisitor::where('last_heartbeat_at', '<', now()->subHours(24))->delete();
                    Notification::make()->title("{$count} eski kayıt temizlendi.")->success()->send();
                }),
        ];
    }
}

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
            $membersCount = (clone $onlineQuery)->whereNotNull('user_id')->count();
            $guestsCount = $onlineCount - $membersCount;

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
                // 1. Durum / Sinyal
                TextColumn::make('status')
                    ->label('Durum')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $isOnline = $record->is_currently_online;
                        $diff = $record->last_heartbeat_at ? $record->last_heartbeat_at->diffForHumans(null, true) : 'bilinmiyor';

                        if ($isOnline) {
                            return new HtmlString('
                                <div class="flex items-center gap-1.5">
                                    <span class="relative flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                    </span>
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400 text-xs">Canlı</span>
                                    <span class="text-gray-400 text-xs">(' . $diff . ')</span>
                                </div>
                            ');
                        }

                        return new HtmlString('
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex rounded-full h-2.5 w-2.5 bg-amber-400"></span>
                                <span class="text-amber-600 dark:text-amber-400 text-xs">Ayrıldı</span>
                                <span class="text-gray-400 text-xs">(' . $diff . ')</span>
                            </div>
                        ');
                    }),

                // 2. Müşteri / Ziyaretçi Kimliği
                TextColumn::make('display_name')
                    ->label('Ziyaretçi')
                    ->searchable(['ip_address', 'user.name', 'user.email'])
                    ->getStateUsing(function (ActiveVisitor $record) {
                        if ($record->user_id && $record->user) {
                            return new HtmlString('
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <span>' . e($record->user->name) . '</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300">Üye</span>
                                    </div>
                                    <div class="text-xs text-gray-500">' . e($record->user->email) . '</div>
                                </div>
                            ');
                        }

                        $shortToken = strtoupper(substr(str_replace(['pa_vt_', '-'], '', $record->visitor_token), 0, 6));
                        return new HtmlString('
                            <div>
                                <div class="font-bold text-gray-700 dark:text-gray-300">Misafir #' . $shortToken . '</div>
                                <div class="text-xs text-gray-400 font-mono">' . e($record->ip_address) . '</div>
                            </div>
                        ');
                    }),

                // 3. Cihaz & Tarayıcı
                TextColumn::make('device_type')
                    ->label('Cihaz')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $deviceIcon = match ($record->device_type) {
                            'mobile' => '📱 Mobil',
                            'tablet' => '📟 Tablet',
                            default => '💻 Masaüstü',
                        };

                        return new HtmlString('
                            <div class="text-xs">
                                <div class="font-medium text-gray-800 dark:text-gray-200">' . $deviceIcon . '</div>
                                <div class="text-gray-400">' . e($record->browser ?? 'Tarayıcı') . ' (' . e($record->operating_system ?? 'OS') . ')</div>
                            </div>
                        ');
                    }),

                // 4. Bulunduğu Sayfa
                TextColumn::make('current_path')
                    ->label('Bulunduğu Sayfa')
                    ->searchable(['current_title', 'current_path'])
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $title = $record->current_title ?: $record->current_path;
                        $url = $record->current_url ?: $record->current_path;
                        $pageViews = $record->page_views_count ?: 1;
                        $duration = $record->duration_formatted;

                        return new HtmlString('
                            <div class="max-w-xs">
                                <a href="' . e($url) . '" target="_blank" class="font-semibold text-xs text-orange-600 dark:text-orange-400 hover:underline line-clamp-1 flex items-center gap-1">
                                    ' . e($title) . '
                                    <span class="text-[10px] text-gray-400">↗</span>
                                </a>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    ' . $pageViews . '. sayfa • ' . $duration . ' süredir sitede
                                </div>
                            </div>
                        ');
                    }),

                // 5. Davranış Teşhisi & Niyet Skoru
                TextColumn::make('behavior_insight')
                    ->label('Davranış Yorumu & Niyet')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $score = $record->intent_score ?? 15;
                        $insight = $record->behavior_insight ?? 'Keşif aşamasında.';

                        $badgeColor = match (true) {
                            $score >= 80 => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
                            $score >= 60 => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
                            $score >= 40 => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300',
                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                        };

                        $scoreLabel = match (true) {
                            $score >= 80 => '🔥 Çok Sıcak (%' . $score . ')',
                            $score >= 60 => '⚡ Tereddütte (%' . $score . ')',
                            $score >= 40 => '👀 İlgili (%' . $score . ')',
                            default => '🔍 Keşif (%' . $score . ')',
                        };

                        return new HtmlString('
                            <div class="max-w-xs">
                                <div class="mb-1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ' . $badgeColor . '">
                                        ' . $scoreLabel . '
                                    </span>
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
                                    ' . e($insight) . '
                                </div>
                            </div>
                        ');
                    }),

                // 6. Sepet Durumu
                TextColumn::make('cart_total')
                    ->label('Sepet')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        if ($record->cart_items_count > 0) {
                            return new HtmlString('
                                <div class="text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800">
                                        🛒 ' . $record->cart_items_count . ' Ürün (' . number_format($record->cart_total, 2) . ' ₺)
                                    </span>
                                </div>
                            ');
                        }

                        return new HtmlString('<span class="text-xs text-gray-400">Boş</span>');
                    }),

                // 7. Önerilen Strateji
                TextColumn::make('recommended_strategy')
                    ->label('Önerilen Strateji')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $strategy = $record->recommended_strategy;
                        if (!$strategy) return '-';

                        $title = $strategy['title'] ?? 'Strateji';
                        return new HtmlString('
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800">
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
                        'members' => '👤 Kayıtlı Üyeler',
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
                            $query->whereNotNull('user_id');
                        }
                    }),
            ])
            ->recordActions([
                // ─── 1. Önerilen Stratejiyi 1-Tıkla Uygula ───
                Action::make('apply_strategy')
                    ->label('⚡ Strateji Uygula')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-bolt')
                    ->modalHeading(fn (ActiveVisitor $record) => '⚡ Satış Stratejisini Uygula: ' . ($record->recommended_strategy['title'] ?? 'Özel Teklif'))
                    ->modalDescription(fn (ActiveVisitor $record) => 'Davranış Teşhisi: ' . ($record->behavior_insight ?? ''))
                    ->modalSubmitActionLabel('🚀 Ziyaretçinin Ekranına Fırlat')
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
                                ->default($strategy['title'] ?? 'Size Özel Fırsat!')
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
                            ->body('Komut ziyaretçinin ekranına fırlatıldı, birkaç saniye içinde icra edilecek.')
                            ->success()
                            ->send();
                    }),

                // ─── 2. Hızlı Yönlendir ───
                Action::make('force_redirect')
                    ->label('🚀 Yönlendir')
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
                ]),
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
                ->label('💬 Herkese Canlı Kupon / Fırsat Fırlat')
                ->color('success')
                ->icon('heroicon-o-gift')
                ->modalHeading('💬 Herkese Canlı Fırsat / Kupon Fırlat')
                ->form([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->default('Günün Sürpriz Fırsatı!')
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

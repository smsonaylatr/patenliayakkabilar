<?php

namespace App\Filament\Resources\Segments;

use App\Filament\Resources\Segments\Pages\ListSegments;
use App\Filament\Resources\Users\UserResource;
use App\Models\ActiveVisitor;
use App\Models\Coupon;
use App\Services\TrafficAnalyticsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class SegmentResource extends Resource
{
    protected static ?string $model = ActiveVisitor::class;

    protected static ?string $slug = 'segments';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Müşteriler';

    protected static ?string $modelLabel = 'Müşteri Segmenti';

    protected static ?string $pluralModelLabel = 'Müşteri Segmentleri';

    protected static ?string $navigationLabel = 'Müşteri Segmentleri';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('15s')
            ->defaultSort('last_heartbeat_at', 'desc')
            ->columns([
                // 1. Müşteri & Yıldız Durumu
                TextColumn::make('visitor_identity')
                    ->label('Müşteri & Yıldız Durumu')
                    ->searchable(['ip_address', 'guest_name', 'guest_id', 'visitor_token', 'guest_email', 'guest_phone', 'user.name', 'user.email', 'user.phone'])
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $name = $record->user_id && $record->user ? e($record->user->name) : e($record->display_name);
                        $isMember = ($record->user_id && $record->user) || $record->is_identified;
                        $hasCustomName = !empty($record->guest_name);
                        $initial = mb_substr($name, 0, 1);
                        $starsHtml = $record->stars_html;
                        $guestId = e($record->guest_id);
                        $visitCount = (int) ($record->visit_count ?: 1);

                        $formattedPhone = $record->formatted_phone ?: ($record->user?->phone ?? $record->guest_phone);
                        $cleanWaPhone = $record->clean_whatsapp_phone;
                        $waUrl = $cleanWaPhone ? $record->getSmartWhatsappUrl() : null;
                        $email = $record->user?->email ?? $record->guest_email;

                        $badgeHtml = '';
                        if ($isMember) {
                            $badgeHtml = '<span style="background:rgba(255,78,0,0.12);color:#ff7849;border:1px solid rgba(255,78,0,0.3);padding:1px 6px;border-radius:4px;font-size:9px;font-weight:700;">MÜŞTERİ</span>';
                        } elseif ($hasCustomName) {
                            $badgeHtml = '<span style="background:rgba(255,255,255,0.06);color:#94a3b8;border:1px solid rgba(255,255,255,0.1);padding:1px 6px;border-radius:4px;font-size:9px;font-weight:600;">MİSAFİR</span>';
                        }

                        $visitBadge = '<span style="display:inline-flex;align-items:center;padding:1px 6px;border-radius:4px;font-size:9.5px;font-weight:700;' . ($visitCount >= 3 ? 'background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.25);' : 'background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.25);') . '">' . $visitCount . '. Gelişi</span>';

                        $waButtonHtml = '';
                        if ($waUrl) {
                            $waButtonHtml = '<a href="' . e($waUrl) . '" target="_blank" title="WhatsApp ile hemen sohbet başlat" style="display:inline-flex;align-items:center;gap:3px;background:#22c55e;color:#ffffff;padding:1.5px 6px;border-radius:4px;font-size:9.5px;font-weight:700;text-decoration:none;box-shadow:0 1px 3px rgba(34,197,94,0.35);transition:all 0.15s ease;" onmouseover="this.style.background=\'#16a34a\'" onmouseout="this.style.background=\'#22c55e\'"><svg style="width:10px;height:10px;fill:currentColor;" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg><span>WhatsApp</span></a>';
                        }

                        $contactHtml = '';
                        if ($formattedPhone || $email) {
                            $contactHtml = '<div style="font-size:11px;color:#cbd5e1;font-weight:500;display:flex;align-items:center;gap:6px;margin-top:3px;flex-wrap:wrap;">'
                                . ($formattedPhone ? '<span style="color:#38bdf8;font-weight:600;">📱 ' . e($formattedPhone) . '</span>' : '')
                                . $waButtonHtml
                                . ($formattedPhone && $email ? '<span>•</span>' : '')
                                . ($email ? '<span style="color:#94a3b8;">' . e($email) . '</span>' : '')
                                . '</div>';
                        }

                        $lastSeen = $record->last_heartbeat_at ? $record->last_heartbeat_at->diffForHumans() : 'Az önce';

                        return new HtmlString('
                            <div style="display:flex;align-items:flex-start;gap:10px;min-width:230px;">
                                <div style="position:relative;width:38px;height:38px;border-radius:50%;background:rgba(30,41,59,0.9);border:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;font-weight:700;color:#f1f5f9;font-size:14px;flex-shrink:0;">
                                    ' . ($record->is_blocked ? '!' : $initial) . '
                                </div>
                                <div style="flex:1;">
                                    <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-bottom:2px;">
                                        <span style="font-weight:700;color:#f8fafc;font-size:13px;">' . $name . '</span>
                                        ' . $starsHtml . '
                                        ' . $visitBadge . '
                                        ' . $badgeHtml . '
                                    </div>
                                    ' . $contactHtml . '
                                    <div style="font-size:10.5px;color:#64748b;margin-top:3px;display:flex;align-items:center;gap:6px;">
                                        <span>Son görülme: <strong style="color:#cbd5e1;">' . $lastSeen . '</strong></span>
                                        <span>•</span>
                                        <span style="font-family:monospace;color:#94a3b8;">#' . $guestId . '</span>
                                    </div>
                                </div>
                            </div>
                        ');
                    }),

                // 2. Davranış Teşhisi & Niyet
                TextColumn::make('behavior_insight')
                    ->label('Davranış Teşhisi & Niyet')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $score = $record->intent_score ?? 15;
                        $insight = $record->behavior_insight ?? 'Sitede gezinmeye devam ediyor.';

                        $badgeColor = $score >= 80 ? '#34d399' : ($score >= 60 ? '#ff7849' : '#94a3b8');
                        $badgeBg = $score >= 80 ? 'rgba(16, 185, 129, 0.12)' : ($score >= 60 ? 'rgba(255, 78, 0, 0.12)' : 'rgba(255, 255, 255, 0.05)');
                        $badgeBorder = $score >= 80 ? 'rgba(16, 185, 129, 0.3)' : ($score >= 60 ? 'rgba(255, 78, 0, 0.3)' : 'rgba(255, 255, 255, 0.08)');

                        $label = match (true) {
                            $score >= 80 => 'YÜKSEK NİYET (%' . $score . ')',
                            $score >= 60 => 'TEREDDÜTTE (%' . $score . ')',
                            $score >= 40 => 'İLGİLİ (%' . $score . ')',
                            default => 'KEŞİF (%' . $score . ')',
                        };

                        $freqSignal = app(TrafficAnalyticsService::class)->getVisitorFrequencySignal($record);

                        return new HtmlString('
                            <div style="min-width:210px;max-width:280px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                                    <span style="font-size:9.5px;font-weight:700;padding:1.5px 6px;border-radius:4px;background:' . $badgeBg . ';color:' . $badgeColor . ';border:1px solid ' . $badgeBorder . ';">
                                        ' . $label . '
                                    </span>
                                    <span style="font-size:9px;color:#94a3b8;font-weight:600;">
                                        ' . ($freqSignal['badge'] ?? '') . '
                                    </span>
                                </div>
                                <div style="background:rgba(255,255,255,0.06);border-radius:999px;height:4px;width:100%;overflow:hidden;margin-bottom:5px;">
                                    <div style="background:#ff4e00;width:' . min(100, max(5, $score)) . '%;height:100%;border-radius:999px;"></div>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;background:rgba(15,23,42,0.55);border:1px solid rgba(255,255,255,0.06);padding:6px 9px;border-radius:6px;line-height:1.35;">
                                    ' . e($insight) . '
                                </div>
                            </div>
                        ');
                    }),

                // 3. İlgilendiği Ürün & Sayfa
                TextColumn::make('current_path')
                    ->label('Son İlgilendiği Ürün / Sayfa')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $interestedProduct = $record->interested_product_info;
                        $pageInfo = $record->page_info ?? [];
                        $url = $record->current_url ?: $record->current_path;
                        $title = $pageInfo['title'] ?? ($record->current_title ?: $record->current_path);

                        if ($interestedProduct) {
                            $imgHtml = !empty($interestedProduct['image'])
                                ? '<img src="' . e($interestedProduct['image']) . '" style="width:38px;height:38px;border-radius:7px;object-fit:cover;border:1px solid rgba(255,255,255,0.1);flex-shrink:0;" />'
                                : '<div style="width:38px;height:38px;border-radius:7px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;font-size:10px;color:#94a3b8;flex-shrink:0;">Model</div>';

                            return new HtmlString('
                                <div style="display:flex;align-items:center;gap:9px;max-width:260px;">
                                    ' . $imgHtml . '
                                    <div style="overflow:hidden;flex:1;">
                                        <div style="display:flex;align-items:center;gap:4px;margin-bottom:1px;">
                                            <span style="font-size:8.5px;font-weight:700;text-transform:uppercase;background:rgba(255,78,0,0.1);color:#ff7849;border:1px solid rgba(255,78,0,0.25);padding:1px 5px;border-radius:4px;">
                                                ' . e($interestedProduct['badge'] ?? 'İncelenen Model') . '
                                            </span>
                                            ' . (!empty($interestedProduct['price']) ? '<strong style="color:#f8fafc;font-size:11px;">' . number_format($interestedProduct['price'], 2) . ' ₺</strong>' : '') . '
                                        </div>
                                        <a href="' . e($interestedProduct['url'] ?? $url) . '" target="_blank" style="font-weight:700;color:#f1f5f9;font-size:11.5px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-decoration:none;">
                                            ' . e($interestedProduct['name']) . ' ↗
                                        </a>
                                    </div>
                                </div>
                            ');
                        }

                        return new HtmlString('
                            <div style="max-width:240px;">
                                <a href="' . e($url) . '" target="_blank" style="font-weight:700;color:#f1f5f9;font-size:12px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-decoration:none;">
                                    ' . e($title) . ' ↗
                                </a>
                                <div style="font-size:10px;color:#64748b;font-family:monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px;">
                                    ' . e($record->current_path) . '
                                </div>
                            </div>
                        ');
                    }),

                // 4. Sepet Durumu
                TextColumn::make('cart_total')
                    ->label('Sepet Durumu')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        if ($record->cart_items_count > 0) {
                            $cartThumb = $record->first_cart_thumbnail_image;
                            $thumbs = $record->cart_thumbnails;
                            $cartThumbName = !empty($thumbs[0]['name']) ? $thumbs[0]['name'] : null;

                            $imgHtml = $cartThumb
                                ? '<img src="' . e($cartThumb) . '" style="width:34px;height:34px;border-radius:6px;object-fit:cover;border:1px solid rgba(255,255,255,0.1);flex-shrink:0;" />'
                                : '<div style="width:34px;height:34px;border-radius:6px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;font-size:10px;color:#94a3b8;flex-shrink:0;">Sepet</div>';

                            return new HtmlString('
                                <div style="display:flex;align-items:center;gap:8px;">
                                    ' . $imgHtml . '
                                    <div>
                                        <div style="font-weight:700;color:#f8fafc;font-size:12.5px;">
                                            ' . number_format($record->cart_total, 2) . ' ₺
                                        </div>
                                        <div style="font-size:9.5px;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;" title="' . e($cartThumbName ?? '') . '">
                                            ' . $record->cart_items_count . ' ürün' . ($cartThumbName ? ' • ' . e($cartThumbName) : '') . '
                                        </div>
                                    </div>
                                </div>
                            ');
                        }

                        return new HtmlString('<span style="color:#64748b;font-size:11px;">Sepet Boş</span>');
                    }),

                // 5. Kaynak & Cihaz
                TextColumn::make('source_info')
                    ->label('Kaynak & Cihaz')
                    ->getStateUsing(function (ActiveVisitor $record) {
                        $source = $record->source_info;
                        $device = match ($record->device_type) {
                            'mobile' => '📱 Mobil',
                            'tablet' => '📱 Tablet',
                            default => '💻 Masaüstü',
                        };

                        return new HtmlString('
                            <div style="font-size:11px;">
                                <div style="display:inline-flex;align-items:center;gap:4px;padding:2px 7px;border-radius:5px;background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid rgba(255,255,255,0.08);font-weight:600;margin-bottom:2px;">
                                    ' . e($source['name'] ?? 'Doğrudan') . '
                                </div>
                                <div style="font-size:10px;color:#94a3b8;margin-top:1px;">
                                    ' . $device . ' • ' . e($record->browser ?? 'Tarayıcı') . '
                                </div>
                            </div>
                        ');
                    }),
            ])
            ->filters([
                SelectFilter::make('stars')
                    ->label('Yıldız / Ziyaret Sayısı')
                    ->options([
                        'all_stars' => '⭐ Tüm 2-3 Yıldızlılar (2+ Ziyaret)',
                        'three_stars' => '⭐⭐⭐ 3 Yıldız - Müdavimler (3+ Ziyaret)',
                        'two_stars' => '⭐⭐ 2 Yıldız - İkinci Kez Gelenler (2 Ziyaret)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === 'three_stars') {
                            $query->where('visit_count', '>=', 3);
                        } elseif ($val === 'two_stars') {
                            $query->where('visit_count', 2);
                        } else {
                            $query->where('visit_count', '>=', 2);
                        }
                    }),

                SelectFilter::make('has_cart')
                    ->label('Sepet Durumu')
                    ->options([
                        'with_cart' => 'Sepetinde Ürün Olanlar',
                        'empty_cart' => 'Sepeti Boş Olanlar',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === 'with_cart') {
                            $query->where('cart_items_count', '>', 0);
                        } elseif ($val === 'empty_cart') {
                            $query->where(function ($q) {
                                $q->whereNull('cart_items_count')->orWhere('cart_items_count', 0);
                            });
                        }
                    }),

                SelectFilter::make('intent_level')
                    ->label('Satın Alma Niyeti')
                    ->options([
                        'high' => '🔥 Yüksek Niyet (%60+)',
                        'hesitating' => '🤔 Tereddütte Olanlar',
                        'exploring' => '🔍 Keşif Aşamasındakiler',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === 'high') {
                            $query->where('intent_score', '>=', 60);
                        } elseif ($val === 'hesitating') {
                            $query->where('intent_level', 'hesitating');
                        } elseif ($val === 'exploring') {
                            $query->where('intent_score', '<', 40);
                        }
                    }),

                SelectFilter::make('has_phone')
                    ->label('💬 WhatsApp / Telefon')
                    ->options([
                        'with_phone' => '✅ Telefonu Olanlar (WhatsApp Hazır)',
                        'no_phone' => '❌ Telefonu Olmayanlar',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? null;
                        if ($val === 'with_phone') {
                            $query->where(function ($q) {
                                $q->where(function ($sq) {
                                    $sq->whereNotNull('guest_phone')->where('guest_phone', '!=', '');
                                })->orWhereHas('user', function ($uq) {
                                    $uq->whereNotNull('phone')->where('phone', '!=', '');
                                });
                            });
                        } elseif ($val === 'no_phone') {
                            $query->where(function ($q) {
                                $q->where(function ($sq) {
                                    $sq->whereNull('guest_phone')->orWhere('guest_phone', '');
                                })->whereDoesntHave('user', function ($uq) {
                                    $uq->whereNotNull('phone')->where('phone', '!=', '');
                                });
                            });
                        }
                    }),
            ])
            ->recordActions([
                // 1. WhatsApp İletişim & Pazarlama Asistanı
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->button()
                    ->size('sm')
                    ->color('success')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->visible(fn (ActiveVisitor $record) => !empty($record->clean_whatsapp_phone))
                    ->modalWidth(Width::TwoExtraLarge)
                    ->modalHeading(fn (ActiveVisitor $record) => '🟢 WhatsApp Pazarlama: ' . $record->display_name)
                    ->modalDescription(fn (ActiveVisitor $record) => 'Telefon: ' . ($record->formatted_phone ?: $record->contact_phone) . ' • ' . ($record->visit_count ?: 1) . '. Gelişi • ' . $record->stars_string)
                    ->modalSubmitActionLabel('💬 WhatsApp Sohbetini Aç ↗')
                    ->form(function (ActiveVisitor $record) {
                        $hasCart = ($record->cart_items_count ?? 0) > 0;
                        $hasProduct = !empty($record->interested_product_info['name']);
                        $defaultTemplate = $hasCart ? 'cart' : ($hasProduct ? 'product' : 'vip');
                        $defaultCoupon = $defaultTemplate === 'vip' ? 'VIP15' : 'SADIK10';
                        $defaultMessage = $record->getSmartWhatsappMarketingMessage($defaultTemplate, $defaultCoupon);

                        return [
                            \Filament\Forms\Components\Select::make('template')
                                ->label('Pazarlama Şablonu')
                                ->native(false)
                                ->options([
                                    'cart' => '🛒 Sepet Hatırlatma & %10 Kupon (SADIK10)',
                                    'product' => '👟 İncelenen Ürün & Beden Danışmanlığı',
                                    'vip' => '⭐ 2-3 Yıldızlı Sadık Ziyaretçi Sürprizi (%15 VIP)',
                                    'shipping' => '🚀 Aynı Gün Hızlı Kargo Fırsatı',
                                    'custom' => '✍️ Serbest Mesaj',
                                ])
                                ->default($defaultTemplate)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, ActiveVisitor $record) {
                                    $coupon = $state === 'vip' ? 'VIP15' : 'SADIK10';
                                    $set('coupon_code', $coupon);
                                    $set('message', $record->getSmartWhatsappMarketingMessage($state, $coupon));
                                }),

                            \Filament\Forms\Components\TextInput::make('coupon_code')
                                ->label('Tanımlanacak Kupon Kodu')
                                ->default($defaultCoupon)
                                ->required(),

                            \Filament\Forms\Components\Textarea::make('message')
                                ->label('WhatsApp Mesaj Taslağı')
                                ->rows(4)
                                ->default($defaultMessage)
                                ->helperText('Mesajı dilediğiniz gibi düzenleyebilirsiniz. Butona tıkladığınızda bu metinle WhatsApp açılır.')
                                ->required(),

                            \Filament\Forms\Components\Toggle::make('create_coupon')
                                ->label('Kupon sitede otomatik aktif edilsin')
                                ->default(true)
                                ->helperText('İşaretliyse, müşterinin sepet sayfasında bu kuponu hemen kullanabilmesi sağlanır.'),
                        ];
                    })
                    ->action(function (ActiveVisitor $record, array $data, $livewire) {
                        if (!empty($data['create_coupon']) && !empty($data['coupon_code'])) {
                            Coupon::firstOrCreate(
                                ['code' => strtoupper(trim($data['coupon_code']))],
                                [
                                    'type' => 'percentage',
                                    'value' => str_contains($data['coupon_code'], '15') ? 15 : 10,
                                    'status' => true,
                                    'expires_at' => now()->addDays(7),
                                    'usage_limit' => 50,
                                ]
                            );
                        }

                        $phone = $record->clean_whatsapp_phone;
                        if (!$phone) {
                            Notification::make()->title('Geçerli bir telefon numarası bulunamadı!')->danger()->send();
                            return;
                        }

                        $message = urlencode($data['message'] ?? '');
                        $url = "https://wa.me/{$phone}?text={$message}";

                        if (method_exists($livewire, 'js')) {
                            $livewire->js("window.open('{$url}', '_blank');");
                        }

                        Notification::make()
                            ->title("WhatsApp Sohbeti Başlatıldı: {$record->display_name}")
                            ->body(($record->formatted_phone ?: $phone) . ' numarası için WhatsApp açıldı.')
                            ->success()
                            ->send();
                    }),

                // 2. Özel Satış Teklifi / Strateji Uygula
                Action::make('apply_strategy')
                    ->label('Teklif Gönder')
                    ->button()
                    ->size('sm')
                    ->color('primary')
                    ->icon('heroicon-o-bolt')
                    ->modalWidth(Width::TwoExtraLarge)
                    ->modalHeading(fn (ActiveVisitor $record) => '2-3 Yıldızlı Müşteriye Özel Teklif: ' . $record->display_name)
                    ->modalDescription(fn (ActiveVisitor $record) => 'Bu müşteri mağazanızı ' . ($record->visit_count ?: 1) . '. kez ziyaret ediyor. Ona anında özel bir fırsat veya kupon iletin.')
                    ->modalSubmitActionLabel('Ziyaretçiye Gönder')
                    ->form(function (ActiveVisitor $record) {
                        $strategy = $record->recommended_strategy ?? [];
                        return [
                            \Filament\Forms\Components\TextInput::make('offer_title')
                                ->label('Fırsat Başlığı')
                                ->default($strategy['title'] ?? 'Tekrar Hoş Geldiniz! Özel %10 İndirim')
                                ->required(),
                            \Filament\Forms\Components\Textarea::make('offer_message')
                                ->label('Mesaj Metni')
                                ->rows(3)
                                ->default($strategy['suggested_message'] ?? 'Sizi tekrar aramızda görmekten mutluluk duyduk. Bu siparişinize özel kuponunuz tanımlandı!')
                                ->required(),
                            \Filament\Forms\Components\TextInput::make('coupon_code')
                                ->label('Kupon Kodu')
                                ->default($strategy['suggested_coupon'] ?? 'SADIK10'),
                        ];
                    })
                    ->action(function (ActiveVisitor $record, array $data) {
                        $record->queueOffer(
                            $data['offer_title'],
                            $data['offer_message'],
                            $data['coupon_code'] ?? null
                        );
                        Notification::make()->title('Özel teklif ziyaretçinin ekranına iletildi!')->success()->send();
                    }),

                // 3. Gezinme Yolculuğunu İncele
                Action::make('inspect_journey')
                    ->label('İncele')
                    ->button()
                    ->size('sm')
                    ->color('gray')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (ActiveVisitor $record) => 'Müşteri Yolculuğu - ' . $record->display_name . ' (' . ($record->visit_count ?: 1) . '. Gelişi)')
                    ->modalWidth(Width::FiveExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat')
                    ->modalContent(fn (ActiveVisitor $record) => view('filament.pages.partials.visitor-journey-modal', ['record' => $record])),

                // 4. Müşteri Profiline Git (Eğer kayıtlı kullanıcıysa)
                Action::make('view_customer')
                    ->label('Profil')
                    ->button()
                    ->size('sm')
                    ->color('gray')
                    ->icon('heroicon-o-user')
                    ->visible(fn (ActiveVisitor $record) => (bool) $record->user_id)
                    ->url(fn (ActiveVisitor $record) => UserResource::getUrl('view', ['record' => $record->user_id])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSegments::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Segments;

use App\Filament\Resources\Segments\Pages\ListSegments;
use App\Filament\Resources\Users\UserResource;
use App\Models\ActiveVisitor;
use App\Services\TrafficAnalyticsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
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

                        $phone = $record->user?->phone ?? $record->guest_phone;
                        $email = $record->user?->email ?? $record->guest_email;

                        $badgeHtml = '';
                        if ($isMember) {
                            $badgeHtml = '<span style="background:rgba(255,78,0,0.12);color:#ff7849;border:1px solid rgba(255,78,0,0.3);padding:1px 6px;border-radius:4px;font-size:9px;font-weight:700;">MÜŞTERİ</span>';
                        } elseif ($hasCustomName) {
                            $badgeHtml = '<span style="background:rgba(255,255,255,0.06);color:#94a3b8;border:1px solid rgba(255,255,255,0.1);padding:1px 6px;border-radius:4px;font-size:9px;font-weight:600;">MİSAFİR</span>';
                        }

                        $visitBadge = '<span style="display:inline-flex;align-items:center;padding:1px 6px;border-radius:4px;font-size:9.5px;font-weight:700;' . ($visitCount >= 3 ? 'background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.25);' : 'background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.25);') . '">' . $visitCount . '. Gelişi</span>';

                        $contactHtml = '';
                        if ($phone || $email) {
                            $contactHtml = '<div style="font-size:11px;color:#cbd5e1;font-weight:500;display:flex;align-items:center;gap:6px;margin-top:3px;">'
                                . ($phone ? '<span style="color:#38bdf8;font-weight:600;">📱 ' . e($phone) . '</span>' : '')
                                . ($phone && $email ? '<span>•</span>' : '')
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
            ])
            ->recordActions([
                // 1. WhatsApp İletişim (Telefonu varsa tek tıkla mesaj aç)
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->button()
                    ->size('sm')
                    ->color('success')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->visible(fn (ActiveVisitor $record) => !empty($record->user?->phone ?? $record->guest_phone))
                    ->url(function (ActiveVisitor $record) {
                        $phone = preg_replace('/[^0-9]/', '', (string) ($record->user?->phone ?? $record->guest_phone));
                        if (str_starts_with($phone, '0')) {
                            $phone = '90' . substr($phone, 1);
                        } elseif (!str_starts_with($phone, '90')) {
                            $phone = '90' . $phone;
                        }
                        $name = $record->user_id && $record->user ? $record->user->name : ($record->guest_name ?: 'Değerli Müşterimiz');
                        $msg = urlencode("Merhaba {$name}, Patenli Ayakkabılar mağazamızı tekrar ziyaret ettiğiniz için teşekkür ederiz! Beğendiğiniz modeller için size özel yardımcı olabileceğimiz bir konu var mı?");
                        return "https://wa.me/{$phone}?text={$msg}";
                    }, shouldOpenInNewTab: true),

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

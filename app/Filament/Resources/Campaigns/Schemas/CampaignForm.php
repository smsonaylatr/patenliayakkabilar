<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\CustomerSegment;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('CampaignTabs')
                    ->tabs([
                        // SEKME 1: GENEL & STRATEJİ
                        Tab::make('Genel & Strateji')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Kampanya Kimliği & Kanal')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Kampanya Adı')
                                            ->placeholder('Örn: Okula Dönüş - Işıklı Patenlerde %20 İndirim')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                                                if ($state && empty($get('tracking_settings.utm_campaign'))) {
                                                    $set('tracking_settings.utm_campaign', Str::slug($state));
                                                }
                                            }),

                                        Select::make('channel')
                                            ->label('Yayın / İletişim Kanalı')
                                            ->options([
                                                'google_ads' => '🎯 Google Ads (Performance Max / Search)',
                                                'meta_ads' => '📱 Meta Ads (Instagram / Facebook)',
                                                'email' => '✉️ E-Posta Bülteni (Newsletter)',
                                                'sms' => '💬 SMS Pazarlama',
                                                'omnichannel' => '🌐 Çok Kanallı (Omnichannel Entegre)',
                                            ])
                                            ->required()
                                            ->default('google_ads')
                                            ->native(false),

                                        Select::make('status')
                                            ->label('Kampanya Durumu')
                                            ->options([
                                                'draft' => 'Taslak (Hazırlanıyor)',
                                                'scheduled' => 'Zamanlandı (Otomatik Başlayacak)',
                                                'running' => 'Yayında / Aktif',
                                                'paused' => 'Durduruldu (Mola)',
                                                'completed' => 'Tamamlandı (Arşiv)',
                                            ])
                                            ->required()
                                            ->default('draft')
                                            ->native(false),

                                        DateTimePicker::make('scheduled_at')
                                            ->label('Başlangıç / Zamanlanmış Tarih')
                                            ->nullable(),

                                        Select::make('coupon_id')
                                            ->label('Bağlı İndirim Kuponu (Opsiyonel)')
                                            ->relationship('coupon', 'code')
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->native(false)
                                            ->helperText('Bu kampanyaya özel tanımlanmış kupon kodunu seçin.'),

                                        TextInput::make('subject')
                                            ->label('E-posta Konusu / Kampanya Spot Başlığı')
                                            ->placeholder('Örn: Çocuğunuzun En Çok İstediği Patenli Ayakkabıda Fırsat!')
                                            ->maxLength(255),
                                    ])->columns(2),

                                Section::make('Bütçe & Finansal Hedefler')
                                    ->schema([
                                        TextInput::make('budget')
                                            ->label('Kampanya Bütçesi (₺)')
                                            ->numeric()
                                            ->prefix('₺')
                                            ->default(0)
                                            ->helperText('Bu kampanya için ayrılan toplam reklam/iletişim bütçesi.'),

                                        TextInput::make('spent_amount')
                                            ->label('Harcanan Bütçe (₺)')
                                            ->numeric()
                                            ->prefix('₺')
                                            ->default(0)
                                            ->helperText('Şu ana kadar harcanan tutar.'),

                                        TextInput::make('target_roas')
                                            ->label('Hedef ROAS (Gelir/Harcama Çarpanı)')
                                            ->numeric()
                                            ->suffix('x')
                                            ->placeholder('4.50')
                                            ->helperText('Örn: 4.50 girilirse 1 ₺ harcamaya 4.5 ₺ ciro hedeflenir.'),

                                        TextInput::make('target_cpa')
                                            ->label('Hedef CPA (Edinme Başı Maliyet ₺)')
                                            ->numeric()
                                            ->prefix('₺')
                                            ->placeholder('85.00')
                                            ->helperText('Hedeflenen sipariş başına reklam maliyeti.'),
                                    ])->columns(4),

                                Section::make('Kampanya Metni & Şablonu')
                                    ->schema([
                                        Textarea::make('message_template')
                                            ->label('Mesaj / Reklam Metni Şablonu')
                                            ->rows(4)
                                            ->placeholder('Kampanya açıklamasını, e-posta veya SMS gövdesini buraya girin. Kişiselleştirme etiketleri: {ad}, {kupon_kodu}, {link}')
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // SEKME 2: KİTLE SİNYALLERİ (AUDIENCE SIGNALS)
                        Tab::make('Kitle Sinyalleri')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Hedef Kitle Sinyalleri & Segmentasyon')
                                    ->description('Yapay zeka ve reklam algoritmalarının en yüksek dönüşümü getirecek potansiyel müşterileri bulmasını sağlar.')
                                    ->schema([
                                        Select::make('customer_segment_id')
                                            ->label('Birincil Hedef Müşteri Segmenti')
                                            ->relationship('customerSegment', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->native(false)
                                            ->helperText('Sistemde kayıtlı ana müşteri segmentini bağlayın.'),

                                        Select::make('audience_signals.target_segments')
                                            ->label('Ek Hedef Segmentler (Çoklu Sinyal)')
                                            ->options(fn () => CustomerSegment::pluck('name', 'id'))
                                            ->multiple()
                                            ->searchable()
                                            ->native(false)
                                            ->helperText('Kampanyanın hedefleyeceği diğer alt müşteri kitleleri.'),

                                        TagsInput::make('audience_signals.intent_keywords')
                                            ->label('Satın Alma & Arama Niyeti Anahtar Kelimeleri (Intent Keywords)')
                                            ->placeholder('Kelime yazıp Enter\'a basın...')
                                            ->helperText('Örn: ışıklı paten ayakkabı, 4 tekerlekli spor ayakkabı, çocuk doğum günü hediyesi, tekerlekli ayakkabı fiyatları')
                                            ->columnSpanFull(),

                                        Select::make('audience_signals.demographics')
                                            ->label('Hedef Demografi & Yaş Grubu')
                                            ->options([
                                                'all' => 'Tüm Aile & Yaş Grupları',
                                                'kids_4_7' => '4 - 7 Yaş (Küçük Çocuk Grubu)',
                                                'kids_8_12' => '8 - 12 Yaş (İlkokul / Ortaokul Grubu)',
                                                'teens_13_17' => '13 - 17 Yaş (Gençler / Teenagers)',
                                                'parents' => 'Ebeveynler (Anne & Baba Karar Vericiler)',
                                            ])
                                            ->default('all')
                                            ->native(false),

                                        Select::make('audience_signals.interest_categories')
                                            ->label('İlgilenilen Ürün Kategorileri')
                                            ->options(fn () => Category::pluck('name', 'id'))
                                            ->multiple()
                                            ->searchable()
                                            ->native(false),

                                        CheckboxList::make('audience_signals.behavior_signals')
                                            ->label('Davranışsal Kitle Sinyalleri (Dinamik)')
                                            ->options([
                                                'abandoned_cart' => '🛒 Sepeti Terk Edenler (Son 7 günde ürün ekleyip almayanlar)',
                                                'repeat_visitors' => '👀 Çoklu İnceleme Yapanlar (Son 14 günde 3+ kez siteye gelenler)',
                                                'churn_risk' => '⚠️ Sadık Müşteri Yeniden Kazanımı (Son 60 gündür sipariş vermeyenler)',
                                                'vip_spenders' => '💎 VIP Alıcılar (Ortalama sepet tutarı 2.500 ₺ üzeri olanlar)',
                                                'coupon_lovers' => '🏷️ İndirim Avcıları (Sadece kupon/promosyon dönemlerinde alanlar)',
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),

                                        CheckboxList::make('audience_signals.excluded_audiences')
                                            ->label('Hariç Tutulacak Kitleler (Bütçe İsrafını Önleme)')
                                            ->options([
                                                'recent_buyers_7d' => '🚫 Son 7 günde sipariş tamamlamış müşteriler',
                                                'recent_buyers_30d' => '🚫 Son 30 günde aynı kategoriden ürün satın alanlar',
                                                'unsubscribed' => '🚫 İletişim iznini iptal etmiş kullanıcılar',
                                                'returned_orders' => '🚫 Son 3 ayda ürün iadesi yapmış olanlar',
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        // SEKME 3: GELİŞMİŞ DÖNÜŞÜM TAKİBİ (ENHANCED TRACKING)
                        Tab::make('Gelişmiş Dönüşüm Takibi')
                            ->icon('heroicon-o-chart-bar-square')
                            ->schema([
                                Section::make('Dönüşüm Hedefi & Parametreler')
                                    ->description('Google Enhanced Conversions ve Meta Conversions API (CAPI) standartlarında birinci taraf veri izleme.')
                                    ->schema([
                                        Select::make('conversion_goal')
                                            ->label('Birincil Kampanya Dönüşüm Hedefi')
                                            ->options([
                                                'purchase' => '💰 Alışveriş / Sipariş Tamamlama (Purchase)',
                                                'add_to_cart' => '🛒 Sepete Ekleme (AddToCart)',
                                                'begin_checkout' => '💳 Ödeme Başlatma (BeginCheckout)',
                                                'lead' => '📞 Potansiyel Müşteri / WhatsApp İletişimi (Lead)',
                                                'view_item' => '👁️ Ziyaret / Ürün Görüntüleme (ViewContent)',
                                            ])
                                            ->default('purchase')
                                            ->required()
                                            ->native(false),

                                        TextInput::make('tracking_settings.landing_page_url')
                                            ->label('Varış / İniş Sayfası (Landing Page URL)')
                                            ->placeholder('https://patenliayakkabilar.com/kategori/4-tekerlekli-patenli-ayakkabi')
                                            ->url()
                                            ->helperText('Kullanıcıların reklam/kampanya linkine tıkladığında gideceği sayfa.'),

                                        TextInput::make('tracking_settings.utm_source')
                                            ->label('UTM Kaynağı (utm_source)')
                                            ->placeholder('google, instagram, newsletter, sms')
                                            ->default('google'),

                                        TextInput::make('tracking_settings.utm_medium')
                                            ->label('UTM Aracı (utm_medium)')
                                            ->placeholder('cpc, paid_social, email, sms')
                                            ->default('cpc'),

                                        TextInput::make('tracking_settings.utm_campaign')
                                            ->label('UTM Kampanya Adı (utm_campaign)')
                                            ->placeholder('okula_donus_paten_2026')
                                            ->required(),

                                        TextInput::make('tracking_settings.utm_content')
                                            ->label('UTM İçerik / Varyant (utm_content)')
                                            ->placeholder('banner_kirmizi_led_isikli'),

                                        Toggle::make('tracking_settings.enhanced_conversions_enabled')
                                            ->label('Google Enhanced Conversions (Gelişmiş Dönüşümler) Aktif')
                                            ->default(true)
                                            ->helperText('Müşteri e-posta ve telefonunun güvenli SHA-256 hash ile Google Ads\'e eşleştirilmesini sağlar.'),

                                        Toggle::make('tracking_settings.meta_capi_enabled')
                                            ->label('Meta CAPI (Conversions API) Sunucu Takibi Aktif')
                                            ->default(true)
                                            ->helperText('iOS 14+ kısıtlamalarını aşarak sunucu taraflı dönüşüm doğrulaması gönderir.'),
                                    ])->columns(2),

                                Section::make('Canlı Takip Linki & Önizleme')
                                    ->schema([
                                        Placeholder::make('tracking_url_preview')
                                            ->label('Üretilen Tam UTM Takip Linki')
                                            ->content(function (Get $get, $record): HtmlString {
                                                $url = $get('tracking_settings.landing_page_url') ?: config('app.url', 'https://patenliayakkabilar.com');
                                                $source = $get('tracking_settings.utm_source') ?: 'google';
                                                $medium = $get('tracking_settings.utm_medium') ?: 'cpc';
                                                $campaign = $get('tracking_settings.utm_campaign') ?: 'kampanya';
                                                $content = $get('tracking_settings.utm_content');

                                                $params = [
                                                    'utm_source' => $source,
                                                    'utm_medium' => $medium,
                                                    'utm_campaign' => $campaign,
                                                ];
                                                if ($content) {
                                                    $params['utm_content'] = $content;
                                                }
                                                if ($record?->id) {
                                                    $params['campaign_id'] = $record->id;
                                                }

                                                $fullUrl = $url . (str_contains($url, '?') ? '&' : '?') . http_build_query($params);

                                                return new HtmlString(
                                                    '<div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 font-mono text-xs break-all select-all text-primary-600 dark:text-primary-400">' .
                                                    '<span>' . e($fullUrl) . '</span>' .
                                                    '</div>'
                                                );
                                            }),
                                    ]),
                            ]),

                        // SEKME 4: ZENGİN VARLIKLAR (RICH ASSETS)
                        Tab::make('Zengin Varlıklar')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Başlıklar ve Metin Varlıkları')
                                    ->description('Google Performance Max ve Meta reklam algoritmalarının dinamik olarak birleştireceği kreatif varlıklar.')
                                    ->schema([
                                        TagsInput::make('rich_assets.headlines')
                                            ->label('Dinamik Başlıklar (Headlines - 30 karakter önerilir)')
                                            ->placeholder('Başlık yazıp Enter\'a basın...')
                                            ->helperText('Algoritma farklı kitlelere farklı başlıkları dener. En az 3 adet giriniz.')
                                            ->default([
                                                'Patenli Ayakkabılarda Fırsat',
                                                'Işıklı ve 4 Tekerlekli Patenler',
                                                'Aynı Gün Ücretsiz Kargo',
                                            ])
                                            ->columnSpanFull(),

                                        TextInput::make('rich_assets.long_headline')
                                            ->label('Uzun Başlık (Long Headline - 90 karakter)')
                                            ->placeholder('Örn: Hem Günlük Ayakkabı Hem Eğlenceli Paten: Tek Tuşla Tekerlekleri Açın!')
                                            ->columnSpanFull(),

                                        TagsInput::make('rich_assets.descriptions')
                                            ->label('Tanıtım Açıklamaları (Descriptions)')
                                            ->placeholder('Açıklama ekleyip Enter\'a basın...')
                                            ->helperText('En az 2 farklı açıklama varyasyonu ekleyin.')
                                            ->default([
                                                'Gizlenebilir tekerlek mekanizmasıyla ister yürüyün ister kayın. Ücretsiz kargo!',
                                                'Çocukların bayıldığı rengarenk LED ışıklar ve dayanıklı gövde. 14 gün değişim garantisi.',
                                            ])
                                            ->columnSpanFull(),

                                        Select::make('rich_assets.call_to_action')
                                            ->label('Harekete Geçirici Mesaj (CTA)')
                                            ->options([
                                                'Şimdi Keşfet' => 'Şimdi Keşfet',
                                                'Hemen Satın Al' => 'Hemen Satın Al',
                                                'Fırsatı Yakala' => 'Fırsatı Yakala',
                                                'Koleksiyonu İncele' => 'Koleksiyonu İncele',
                                                'İndirimi Uygula' => 'İndirimi Uygula',
                                            ])
                                            ->default('Şimdi Keşfet')
                                            ->native(false),

                                        TagsInput::make('rich_assets.callouts')
                                            ->label('Belirtme Öğeleri / Rozetler (Callouts)')
                                            ->placeholder('Rozet ekleyin...')
                                            ->helperText('Öne çıkan güven ve avantaj unsurları.')
                                            ->default([
                                                '🚚 Aynı Gün Kargo',
                                                '✨ 4 Tekerlekli & LED Işıklı',
                                                '🔄 14 Gün Kolay Değişim',
                                                '🛡️ Orijinal ve Faturalı',
                                            ])
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Zengin Site Bağlantıları (Sitelinks)')
                                    ->description('Reklam ve kampanya altında görüntülenecek 2-4 adet ek yönlendirme bağlantısı.')
                                    ->schema([
                                        Repeater::make('rich_assets.sitelinks')
                                            ->label('Site Bağlantıları')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Bağlantı Başlığı')
                                                    ->required()
                                                    ->placeholder('Örn: 4 Tekerlekli Modeller'),

                                                TextInput::make('description')
                                                    ->label('Kısa Açıklama')
                                                    ->placeholder('Örn: En popüler ve dengeli paten modelleri'),

                                                TextInput::make('url')
                                                    ->label('Hedef URL')
                                                    ->url()
                                                    ->required()
                                                    ->placeholder('https://patenliayakkabilar.com/kategori/4-tekerlekli'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(2)
                                            ->addActionLabel('+ Yeni Site Bağlantısı Ekle')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Görsel ve Medya Varlıkları')
                                    ->schema([
                                        FileUpload::make('rich_assets.images')
                                            ->label('Kampanya Banner ve Kreatif Görselleri')
                                            ->helperText('Önerilen boyutlar: Yatay (1200x628 - 1.91:1), Kare (1080x1080 - 1:1), Dikey/Story (1080x1920 - 9:16).')
                                            ->multiple()
                                            ->directory('campaigns')
                                            ->image()
                                            ->imageEditor()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // SEKME 5: PERFORMANS & ANALİTİK
                        Tab::make('Performans Analitiği')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Section::make('Dönüşüm ve Satış Verileri')
                                    ->schema([
                                        TextInput::make('conversions_count')
                                            ->label('Gerçekleşen Dönüşüm (Sipariş) Sayısı')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Bu kampanyadan gelen onaylı sipariş sayısı.'),

                                        TextInput::make('conversion_revenue')
                                            ->label('Toplam Dönüşüm Cirosu (₺)')
                                            ->numeric()
                                            ->prefix('₺')
                                            ->default(0)
                                            ->helperText('Kampanya aracılığıyla elde edilen brüt ciro.'),

                                        TextInput::make('sent_count')
                                            ->label('Gönderim / Gösterim Sayısı')
                                            ->numeric()
                                            ->default(0),

                                        TextInput::make('clicked_count')
                                            ->label('Tıklama Sayısı')
                                            ->numeric()
                                            ->default(0),

                                        Placeholder::make('roas_display')
                                            ->label('Gerçekleşen ROAS (Gelir / Harcama)')
                                            ->content(function ($record): HtmlString {
                                                if (!$record) return new HtmlString('-');
                                                $roas = $record->roas;
                                                $target = (float) $record->target_roas;
                                                if (!$roas) return new HtmlString('<span class="text-gray-400">Veri yok</span>');

                                                $isAboveTarget = $target > 0 && $roas >= $target;
                                                $color = $isAboveTarget ? 'text-green-600 font-bold' : ($roas >= 2.0 ? 'text-blue-600' : 'text-amber-600');
                                                return new HtmlString("<span class=\"{$color} text-lg\">{$roas}x</span>" . ($target > 0 ? " (Hedef: {$target}x)" : ""));
                                            }),

                                        Placeholder::make('cr_display')
                                            ->label('Dönüşüm Oranı (CR %)')
                                            ->content(function ($record): HtmlString {
                                                if (!$record) return new HtmlString('-');
                                                $cr = $record->conversion_rate;
                                                return new HtmlString("<span class=\"font-semibold text-lg text-primary-600\">%{$cr}</span>");
                                            }),
                                    ])->columns(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

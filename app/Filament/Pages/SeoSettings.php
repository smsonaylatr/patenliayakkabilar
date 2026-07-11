<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\Product;
use App\Models\Category;
use App\Models\BlogPost;
use App\Models\Page;
use Filament\Pages\Page as FilamentPage;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SeoSettings extends FilamentPage implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-magnifying-glass';
    }

    public static function getNavigationLabel(): string
    {
        return 'SEO Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'SEO & Şirket Ayarları';
    }

    protected string $view = 'filament.pages.seo-settings';

    public ?array $data = [];

    // SEO sağlık metrikleri
    public int $totalProducts = 0;
    public int $productsWithoutMeta = 0;
    public int $totalCategories = 0;
    public int $categoriesWithoutMeta = 0;
    public int $totalPages = 0;
    public int $pagesWithoutMeta = 0;
    public int $totalBlogPosts = 0;
    public int $blogPostsWithoutMeta = 0;

    public function mount(): void
    {
        // Ayarları yükle
        $keys = [
            'company_name', 'company_phone', 'company_email',
            'company_address', 'company_city', 'company_district',
            'company_tax_office', 'company_tax_number',
            'social_facebook', 'social_instagram', 'social_twitter',
            'social_youtube', 'social_tiktok',
            'seo_default_title_suffix', 'seo_default_description',
            'seo_google_verification', 'seo_yandex_verification',
            'seo_bing_verification',
            'google_analytics_id', 'gtm_container_id',
            'shipping_info_text', 'return_policy_text',
            'return_policy_days',
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        $this->form->fill([
            'company_name' => $settings['company_name'] ?? 'Patenli Ayakkabılar',
            'company_phone' => $settings['company_phone'] ?? '',
            'company_email' => $settings['company_email'] ?? '',
            'company_address' => $settings['company_address'] ?? '',
            'company_city' => $settings['company_city'] ?? '',
            'company_district' => $settings['company_district'] ?? '',
            'company_tax_office' => $settings['company_tax_office'] ?? '',
            'company_tax_number' => $settings['company_tax_number'] ?? '',
            'social_facebook' => $settings['social_facebook'] ?? '',
            'social_instagram' => $settings['social_instagram'] ?? '',
            'social_twitter' => $settings['social_twitter'] ?? '',
            'social_youtube' => $settings['social_youtube'] ?? '',
            'social_tiktok' => $settings['social_tiktok'] ?? '',
            'seo_default_title_suffix' => $settings['seo_default_title_suffix'] ?? '| Patenli Ayakkabılar',
            'seo_default_description' => $settings['seo_default_description'] ?? 'Çocuklar için en güvenli ve eğlenceli patenli ayakkabı modelleri.',
            'seo_google_verification' => $settings['seo_google_verification'] ?? '',
            'seo_yandex_verification' => $settings['seo_yandex_verification'] ?? '',
            'seo_bing_verification' => $settings['seo_bing_verification'] ?? '',
            'google_analytics_id' => $settings['google_analytics_id'] ?? '',
            'gtm_container_id' => $settings['gtm_container_id'] ?? '',
            'shipping_info_text' => $settings['shipping_info_text'] ?? 'Türkiye genelinde 1-3 iş günü içinde kargo ile teslim.',
            'return_policy_text' => $settings['return_policy_text'] ?? '14 gün içinde koşulsuz iade hakkı.',
            'return_policy_days' => $settings['return_policy_days'] ?? '14',
        ]);

        // SEO sağlık metriklerini hesapla
        $this->calculateSeoHealth();
    }

    protected function calculateSeoHealth(): void
    {
        $this->totalProducts = Product::where('status', true)->count();
        $this->productsWithoutMeta = Product::where('status', true)
            ->where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '');
            })->count();

        $this->totalCategories = Category::where('status', true)->count();
        $this->categoriesWithoutMeta = Category::where('status', true)
            ->where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '');
            })->count();

        $this->totalPages = Page::where('is_active', true)->count();
        $this->pagesWithoutMeta = Page::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '');
            })->count();

        $this->totalBlogPosts = BlogPost::where('status', true)->count();
        $this->blogPostsWithoutMeta = BlogPost::where('status', true)
            ->where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '');
            })->count();
    }

    public function form($form)
    {
        return $form
            ->schema([
                Tabs::make('SEO Ayarları')
                    ->tabs([

                        // ==========================================
                        // TAB 1: ŞİRKET BİLGİLERİ
                        // ==========================================
                        Tab::make('Şirket Bilgileri')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Section::make('Temel Bilgiler')
                                    ->description('Google\'da bilgi panelinde ve yapılandırılmış verilerde görünecek şirket bilgileri.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('company_name')
                                                ->label('Şirket / Marka Adı')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('company_phone')
                                                ->label('Telefon')
                                                ->tel()
                                                ->placeholder('+90 5XX XXX XX XX'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('company_email')
                                                ->label('E-posta')
                                                ->email()
                                                ->placeholder('info@patenliayakkabilar.com'),
                                            TextInput::make('company_city')
                                                ->label('Şehir')
                                                ->placeholder('İstanbul'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('company_district')
                                                ->label('İlçe')
                                                ->placeholder('Kadıköy'),
                                            TextInput::make('company_address')
                                                ->label('Açık Adres')
                                                ->placeholder('Sokak adı, bina no, kat'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('company_tax_office')
                                                ->label('Vergi Dairesi'),
                                            TextInput::make('company_tax_number')
                                                ->label('Vergi Numarası'),
                                        ]),
                                    ]),

                                Section::make('Sosyal Medya Hesapları')
                                    ->description('Google bilgi paneli ve Organization schema\'da kullanılır.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('social_instagram')
                                                ->label('Instagram')
                                                ->url()
                                                ->placeholder('https://instagram.com/patenliayakkabilar')
                                                ->prefixIcon('heroicon-o-camera'),
                                            TextInput::make('social_facebook')
                                                ->label('Facebook')
                                                ->url()
                                                ->placeholder('https://facebook.com/patenliayakkabilar')
                                                ->prefixIcon('heroicon-o-globe-alt'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('social_tiktok')
                                                ->label('TikTok')
                                                ->url()
                                                ->placeholder('https://tiktok.com/@patenliayakkabilar')
                                                ->prefixIcon('heroicon-o-play'),
                                            TextInput::make('social_youtube')
                                                ->label('YouTube')
                                                ->url()
                                                ->placeholder('https://youtube.com/@patenliayakkabilar')
                                                ->prefixIcon('heroicon-o-play-circle'),
                                        ]),
                                        TextInput::make('social_twitter')
                                            ->label('X (Twitter)')
                                            ->url()
                                            ->placeholder('https://x.com/patenliayakkabi')
                                            ->prefixIcon('heroicon-o-chat-bubble-left'),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: GENEL SEO AYARLARI
                        // ==========================================
                        Tab::make('Genel SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('Varsayılan Meta Verileri')
                                    ->description('Sayfalarda özel meta tanımlanmadığında bu değerler kullanılır.')
                                    ->schema([
                                        TextInput::make('seo_default_title_suffix')
                                            ->label('Başlık Son Eki')
                                            ->helperText('Örn: "Ürün Adı | Patenli Ayakkabılar" → "| Patenli Ayakkabılar" kısmı')
                                            ->maxLength(50),
                                        Textarea::make('seo_default_description')
                                            ->label('Varsayılan Meta Açıklama')
                                            ->maxLength(160)
                                            ->rows(3)
                                            ->helperText(fn ($state) => 'Karakter: ' . mb_strlen($state ?? '') . '/160'),
                                    ]),

                                Section::make('Arama Motoru Doğrulama')
                                    ->description('Arama motorları site sahipliğini doğrulamak için meta tag ister. Sadece content değerini girin.')
                                    ->schema([
                                        TextInput::make('seo_google_verification')
                                            ->label('Google Search Console')
                                            ->placeholder('google-site-verification meta tag content değeri')
                                            ->helperText('Google Search Console → Ayarlar → Sahiplik doğrulama → HTML etiketi'),
                                        TextInput::make('seo_yandex_verification')
                                            ->label('Yandex Webmaster')
                                            ->placeholder('yandex-verification meta tag content değeri'),
                                        TextInput::make('seo_bing_verification')
                                            ->label('Bing Webmaster')
                                            ->placeholder('msvalidate.01 meta tag content değeri'),
                                    ]),

                                Section::make('Kargo & İade Bilgileri')
                                    ->description('Product schema\'da (yapılandırılmış veri) kullanılır.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Textarea::make('shipping_info_text')
                                                ->label('Kargo Bilgisi')
                                                ->rows(2),
                                            Textarea::make('return_policy_text')
                                                ->label('İade Politikası')
                                                ->rows(2),
                                        ]),
                                        TextInput::make('return_policy_days')
                                            ->label('İade Süresi (Gün)')
                                            ->numeric()
                                            ->default(14)
                                            ->minValue(1)
                                            ->maxValue(60),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: ANALİTİK
                        // ==========================================
                        Tab::make('Analitik & İzleme')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Google Analytics 4')
                                    ->description('Ziyaretçi takibi için GA4 ölçüm kimliğini girin. analytics.google.com adresinden oluşturabilirsiniz.')
                                    ->schema([
                                        TextInput::make('google_analytics_id')
                                            ->label('GA4 Ölçüm Kimliği')
                                            ->placeholder('G-XXXXXXXXXX')
                                            ->helperText('Google Analytics → Yönetici → Veri Akışları → Ölçüm kimliği'),
                                    ]),

                                Section::make('Google Tag Manager')
                                    ->description('Gelişmiş etiket yönetimi için GTM konteyner kimliğini girin. tagmanager.google.com adresinden oluşturabilirsiniz.')
                                    ->schema([
                                        TextInput::make('gtm_container_id')
                                            ->label('GTM Konteyner Kimliği')
                                            ->placeholder('GTM-XXXXXXX')
                                            ->helperText('Tag Manager → Çalışma Alanı → Konteyner kimliği'),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 4: SEO ARAÇLARI
                        // ==========================================
                        Tab::make('SEO Araçları')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Section::make('Site Dosyaları')
                                    ->description('Sitenizin SEO dosyalarını kontrol edin.')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            \Filament\Forms\Components\Placeholder::make('sitemap_link')
                                                ->label('Sitemap.xml')
                                                ->content(new \Illuminate\Support\HtmlString(
                                                    '<a href="' . url('/sitemap.xml') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">→ Sitemap\'i Aç</a>'
                                                )),
                                            \Filament\Forms\Components\Placeholder::make('merchant_link')
                                                ->label('Merchant Feed')
                                                ->content(new \Illuminate\Support\HtmlString(
                                                    '<a href="' . url('/feeds/google-merchant.xml') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">→ Feed\'i Aç</a>'
                                                )),
                                            \Filament\Forms\Components\Placeholder::make('robots_link')
                                                ->label('robots.txt')
                                                ->content(new \Illuminate\Support\HtmlString(
                                                    '<a href="' . url('/robots.txt') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">→ robots.txt Aç</a>'
                                                )),
                                            \Filament\Forms\Components\Placeholder::make('llms_link')
                                                ->label('llms.txt')
                                                ->content(new \Illuminate\Support\HtmlString(
                                                    '<a href="' . url('/llms.txt') . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">→ llms.txt Aç</a>'
                                                )),
                                        ]),
                                    ]),

                                Section::make('Google Araçları')
                                    ->description('SEO performansınızı test edin ve takip edin.')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            \Filament\Forms\Components\Placeholder::make('gsc_link')
                                                ->label('Search Console')
                                                ->content(new \Illuminate\Support\HtmlString(
                                                    '<a href="https://search.google.com/search-console" target="_blank" rel="noopener" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">→ Aç</a>'
                                                )),
                                            \Filament\Forms\Components\Placeholder::make('rich_results_link')
                                                ->label('Rich Results Test')
                                                ->content(new \Illuminate\Support\HtmlString(
                                                    '<a href="https://search.google.com/test/rich-results?url=' . urlencode(config('app.url')) . '" target="_blank" rel="noopener" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">→ Schema Test</a>'
                                                )),
                                            \Filament\Forms\Components\Placeholder::make('pagespeed_link')
                                                ->label('PageSpeed Insights')
                                                ->content(new \Illuminate\Support\HtmlString(
                                                    '<a href="https://pagespeed.web.dev/analysis?url=' . urlencode(config('app.url')) . '" target="_blank" rel="noopener" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">→ Hız Testi</a>'
                                                )),
                                            \Filament\Forms\Components\Placeholder::make('merchant_center_link')
                                                ->label('Merchant Center')
                                                ->content(new \Illuminate\Support\HtmlString(
                                                    '<a href="https://merchants.google.com" target="_blank" rel="noopener" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">→ Aç</a>'
                                                )),
                                        ]),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        // Cache'leri temizle
        \Illuminate\Support\Facades\Cache::forget('hero_settings');

        Notification::make()
            ->title('Başarılı')
            ->body('SEO ayarları başarıyla kaydedildi.')
            ->success()
            ->send();
    }

    /**
     * Tüm ürünlerin SEO verilerini otomatik üret
     */
    public function regenerateAllProductSeo(): void
    {
        $products = Product::where('status', true)
            ->where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '');
            })
            ->with('category')
            ->get();

        $count = 0;
        foreach ($products as $product) {
            $product->meta_title = '';
            $product->meta_description = '';
            $product->updated_at = now();
            $product->save();
            $count++;
        }

        $this->calculateSeoHealth();

        Notification::make()
            ->title('SEO Verileri Üretildi')
            ->body("{$count} ürünün meta verileri otomatik oluşturuldu.")
            ->success()
            ->send();
    }

    /**
     * Tüm kategorilerin SEO verilerini otomatik üret
     */
    public function regenerateAllCategorySeo(): void
    {
        $categories = Category::where('status', true)
            ->where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '');
            })
            ->get();

        $count = 0;
        foreach ($categories as $category) {
            $category->meta_title = '';
            $category->meta_description = '';
            $category->updated_at = now();
            $category->save();
            $count++;
        }

        $this->calculateSeoHealth();

        Notification::make()
            ->title('SEO Verileri Üretildi')
            ->body("{$count} kategorinin meta verileri otomatik oluşturuldu.")
            ->success()
            ->send();
    }

    /**
     * Tüm sayfaların SEO verilerini otomatik üret
     */
    public function regenerateAllPageSeo(): void
    {
        $pages = Page::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '');
            })
            ->get();

        $count = 0;
        foreach ($pages as $page) {
            $page->meta_title = '';
            $page->meta_description = '';
            $page->updated_at = now();
            $page->save();
            $count++;
        }

        $this->calculateSeoHealth();

        Notification::make()
            ->title('SEO Verileri Üretildi')
            ->body("{$count} sayfanın meta verileri otomatik oluşturuldu.")
            ->success()
            ->send();
    }

    /**
     * Tüm blog yazılarının SEO verilerini otomatik üret
     */
    public function regenerateAllBlogSeo(): void
    {
        $posts = BlogPost::where('status', true)
            ->where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '');
            })
            ->get();

        $count = 0;
        foreach ($posts as $post) {
            $post->meta_title = '';
            $post->meta_description = '';
            $post->updated_at = now();
            $post->save();
            $count++;
        }

        $this->calculateSeoHealth();

        Notification::make()
            ->title('SEO Verileri Üretildi')
            ->body("{$count} blog yazısının meta verileri otomatik oluşturuldu.")
            ->success()
            ->send();
    }
}

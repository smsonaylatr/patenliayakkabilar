<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveVisitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'cart_summary' => 'array',
        'journey_trail' => 'array',
        'recommended_strategy' => 'array',
        'pending_command' => 'array',
        'last_command_executed' => 'array',
        'is_online' => 'boolean',
        'is_blocked' => 'boolean',
        'is_identified' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
        'cart_total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('last_heartbeat_at', '>=', now()->subSeconds(45));
    }

    public function scopeIdle($query)
    {
        return $query->whereBetween('last_heartbeat_at', [now()->subMinutes(5), now()->subSeconds(45)]);
    }

    public function scopeHighIntent($query)
    {
        return $query->where('intent_score', '>=', 60);
    }

    public function scopeHesitating($query)
    {
        return $query->where('intent_level', 'hesitating');
    }

    public function scopeWithCart($query)
    {
        return $query->where('cart_items_count', '>', 0);
    }

    public function scopeMembers($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeGuests($query)
    {
        return $query->whereNull('user_id');
    }

    public function getIsCurrentlyOnlineAttribute(): bool
    {
        return $this->last_heartbeat_at && $this->last_heartbeat_at->gte(now()->subSeconds(45));
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->user_id && $this->user) {
            return $this->user->name;
        }

        if (!empty($this->guest_name)) {
            return trim($this->guest_name);
        }

        $shortId = strtoupper(substr(str_replace(['pa_vt_', '-'], '', $this->visitor_token), 0, 6));
        return "Misafir #{$shortId}";
    }

    public function getDurationFormattedAttribute(): string
    {
        $seconds = abs((int) ($this->time_spent_seconds ?: 0));
        if ($seconds === 0 && $this->first_seen_at) {
            $seconds = abs((int) $this->first_seen_at->diffInSeconds(now()));
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 1) {
            return "{$remainingSeconds} sn";
        }

        return "{$minutes} dk {$remainingSeconds} sn";
    }

    protected static array $productCache = [];
    protected static array $pageInfoCache = [];

    /**
     * Ham sayfa başlığını marka eklerinden ve SEO kalıplarından arındırır.
     */
    public static function cleanTitle(?string $title): string
    {
        if (empty($title)) {
            return '';
        }

        $clean = trim($title);

        $patterns = [
            '/\s*[-|–—•]\s*Patenli\s*Ayakkabılar®?/ui',
            '/\s*[-|–—•]\s*Tekerlekli\s*Ayakkabı(\s*Modelleri)?/ui',
            '/^Patenli\s*Ayakkabılar®?\s*[-|–—•]\s*/ui',
        ];

        foreach ($patterns as $pattern) {
            $clean = preg_replace($pattern, '', $clean);
        }

        $clean = trim($clean, " \t\n\r\0\x0B-|–—•");

        if (preg_match('/^Patenli\s*Ayakkabılar®?$/ui', $clean)) {
            return '';
        }

        return $clean;
    }

    /**
     * Verilen URL yolu (path) ve ham başlığı insan tarafından kolayca anlaşılır sayfa bilgisine dönüştürür.
     */
    public static function resolvePageInfo(?string $path, ?string $rawTitle = null): array
    {
        if ($path && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'))) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = $parsed ?: '/';
        }
        $path = '/' . ltrim($path ?: '/', '/');
        $pathWithoutQuery = explode('?', $path)[0];
        $cacheKey = $pathWithoutQuery . '|' . ($rawTitle ?? '');

        if (isset(static::$pageInfoCache[$cacheKey])) {
            return static::$pageInfoCache[$cacheKey];
        }

        $cleanTitle = static::cleanTitle($rawTitle);

        // 1. Ana Sayfa
        if ($pathWithoutQuery === '/' || empty($pathWithoutQuery)) {
            $info = [
                'type' => 'home',
                'title' => 'Ana Sayfa',
                'subtitle' => 'Vitrin & Popüler Modeller',
                'badge' => 'Ana Sayfa',
                'icon' => '🏠',
                'color' => '#38bdf8',
                'bg_color' => 'rgba(56, 189, 248, 0.15)',
                'border_color' => 'rgba(56, 189, 248, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 2. Ödeme Sayfası (Checkout)
        if (str_starts_with($pathWithoutQuery, '/checkout')) {
            $info = [
                'type' => 'checkout',
                'title' => 'Ödeme Sayfası (Checkout)',
                'subtitle' => 'Sepet Onayı & Adres / Ödeme',
                'badge' => 'Ödeme Ekranı',
                'icon' => '🛒',
                'color' => '#10b981',
                'bg_color' => 'rgba(16, 185, 129, 0.15)',
                'border_color' => 'rgba(16, 185, 129, 0.35)',
                'is_product' => false,
                'is_checkout' => true,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 3. Sipariş Başarılı
        if (str_starts_with($pathWithoutQuery, '/order/success')) {
            $parts = explode('/', trim($pathWithoutQuery, '/'));
            $orderNo = $parts[2] ?? '';
            $info = [
                'type' => 'order_success',
                'title' => $orderNo ? "Sipariş Alındı (#{$orderNo})" : 'Sipariş Başarıyla Tamamlandı',
                'subtitle' => 'Satış Başarılı (Teşekkür Sayfası)',
                'badge' => 'Satış Yapıldı',
                'icon' => '🎉',
                'color' => '#10b981',
                'bg_color' => 'rgba(16, 185, 129, 0.15)',
                'border_color' => 'rgba(16, 185, 129, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 4. Sipariş Başarısız
        if (str_starts_with($pathWithoutQuery, '/order/fail')) {
            $info = [
                'type' => 'order_fail',
                'title' => 'Ödeme Başarısız Oldu',
                'subtitle' => 'Kart veya Bakiye Hatası',
                'badge' => 'Ödeme Uyarısı',
                'icon' => '⚠️',
                'color' => '#ef4444',
                'bg_color' => 'rgba(239, 68, 68, 0.15)',
                'border_color' => 'rgba(239, 68, 68, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 5. Ürün Detayı veya Yorumları
        if (str_starts_with($pathWithoutQuery, '/urun/')) {
            $isReviews = str_ends_with($pathWithoutQuery, '/yorumlar');
            $parts = explode('/', trim($pathWithoutQuery, '/'));
            $slug = $parts[1] ?? null;

            $product = null;
            if ($slug) {
                $slugClean = explode('?', $slug)[0];
                if (!array_key_exists($slugClean, static::$productCache)) {
                    $found = \App\Models\Product::where('slug', $slugClean)
                        ->orWhere('slug', urldecode($slugClean))
                        ->with('images')
                        ->first();

                    if (!$found && !empty($cleanTitle) && strcasecmp($cleanTitle, 'Patenli Ayakkabılar') !== 0) {
                        $found = \App\Models\Product::where('name', $cleanTitle)
                            ->orWhere('name', 'like', '%' . $cleanTitle . '%')
                            ->with('images')
                            ->first();
                    }

                    static::$productCache[$slugClean] = $found;
                }
                $product = static::$productCache[$slugClean];
            }

            if (!$product && !empty($cleanTitle) && strcasecmp($cleanTitle, 'Patenli Ayakkabılar') !== 0) {
                $product = \App\Models\Product::where('name', $cleanTitle)
                    ->orWhere('name', 'like', '%' . $cleanTitle . '%')
                    ->with('images')
                    ->first();
            }

            $productTitle = $product ? $product->name : ($cleanTitle ?: \Illuminate\Support\Str::headline($slugClean ?? 'urun'));
            if ($isReviews) {
                $productTitle .= ' (Yorumlar)';
            }

            $productImage = $product ? ($product->images->first()?->image_url ?? $product->images->first()?->raw_image_url) : null;
            $productPrice = $product ? ($product->discount_price ?: $product->price) : null;

            $info = [
                'type' => 'product',
                'title' => $productTitle,
                'subtitle' => $isReviews ? 'Müşteri Değerlendirmeleri' : ($productPrice ? number_format($productPrice, 2) . ' ₺ • İnceliyor' : 'Ürün İnceleme'),
                'badge' => $isReviews ? 'Ürün Yorumları' : 'Ürün Detayı',
                'icon' => $isReviews ? '⭐' : '👟',
                'color' => '#ff7849',
                'bg_color' => 'rgba(255, 78, 0, 0.15)',
                'border_color' => 'rgba(255, 78, 0, 0.35)',
                'is_product' => true,
                'is_checkout' => false,
                'product' => $product,
                'image' => $productImage,
                'price' => $productPrice,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 6. Tüm Ürünler Kataloğu
        if ($pathWithoutQuery === '/patenli-ayakkabilar' || $pathWithoutQuery === '/urunler') {
            $info = [
                'type' => 'catalog',
                'title' => $cleanTitle ?: 'Tüm Paten Modelleri (Katalog)',
                'subtitle' => 'Tüm Patenli Ayakkabılar Kataloğu',
                'badge' => 'Katalog',
                'icon' => '🛍️',
                'color' => '#a855f7',
                'bg_color' => 'rgba(168, 85, 247, 0.15)',
                'border_color' => 'rgba(168, 85, 247, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 7. Kategori Sayfası
        if (str_starts_with($pathWithoutQuery, '/kategori/')) {
            $parts = explode('/', trim($pathWithoutQuery, '/'));
            $slug = $parts[1] ?? '';
            $categoryName = null;
            if ($slug) {
                $category = \App\Models\Category::where('slug', $slug)->first();
                if ($category) {
                    $categoryName = $category->name . ' Modelleri';
                }
            }
            $title = $categoryName ?: ($cleanTitle ?: \Illuminate\Support\Str::headline($slug) . ' Kategorisi');

            $info = [
                'type' => 'category',
                'title' => $title,
                'subtitle' => 'Kategori Kataloğu',
                'badge' => 'Kategori',
                'icon' => '🏷️',
                'color' => '#8b5cf6',
                'bg_color' => 'rgba(139, 92, 246, 0.15)',
                'border_color' => 'rgba(139, 92, 246, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 8. Sipariş Takip
        if (str_starts_with($pathWithoutQuery, '/siparis-takip')) {
            $info = [
                'type' => 'tracking',
                'title' => 'Sipariş & Kargo Takibi',
                'subtitle' => 'Kargo Durumu Sorgulama',
                'badge' => 'Sipariş Takip',
                'icon' => '📦',
                'color' => '#06b6d4',
                'bg_color' => 'rgba(6, 182, 212, 0.15)',
                'border_color' => 'rgba(6, 182, 212, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 9. İletişim
        if (str_starts_with($pathWithoutQuery, '/iletisim')) {
            $info = [
                'type' => 'contact',
                'title' => 'İletişim & Canlı Destek',
                'subtitle' => 'Müşteri Hizmetleri & Form',
                'badge' => 'İletişim',
                'icon' => '📞',
                'color' => '#14b8a6',
                'bg_color' => 'rgba(20, 184, 166, 0.15)',
                'border_color' => 'rgba(20, 184, 166, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 10. Blog / Rehber
        if (str_starts_with($pathWithoutQuery, '/blog')) {
            $parts = explode('/', trim($pathWithoutQuery, '/'));
            $slug = $parts[1] ?? null;
            if ($slug) {
                $post = \App\Models\BlogPost::where('slug', $slug)->first();
                $title = $post ? $post->title : ($cleanTitle ?: \Illuminate\Support\Str::headline($slug));
                $badge = 'Blog Yazısı';
                $subtitle = 'Rehber & Bilgi İçeriği';
                $icon = '📖';
            } else {
                $title = 'Blog & Paten Rehberleri';
                $badge = 'Blog & Rehber';
                $subtitle = 'Makaleler & Kullanım İpuçları';
                $icon = '📰';
            }

            $info = [
                'type' => 'blog',
                'title' => $title,
                'subtitle' => $subtitle,
                'badge' => $badge,
                'icon' => $icon,
                'color' => '#ec4899',
                'bg_color' => 'rgba(236, 72, 153, 0.15)',
                'border_color' => 'rgba(236, 72, 153, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 11. Hesabım & Giriş
        if (str_starts_with($pathWithoutQuery, '/hesabim')) {
            $subtitle = 'Müşteri Paneli';
            $title = 'Hesabım';
            if (str_contains($pathWithoutQuery, 'siparis')) {
                $title = 'Siparişlerim';
                $subtitle = 'Geçmiş Sipariş Listesi';
            } elseif (str_contains($pathWithoutQuery, 'profil')) {
                $title = 'Profil Bilgilerim';
                $subtitle = 'Hesap Ayarları';
            }

            $info = [
                'type' => 'account',
                'title' => $title,
                'subtitle' => $subtitle,
                'badge' => 'Müşteri Hesabı',
                'icon' => '👤',
                'color' => '#6366f1',
                'bg_color' => 'rgba(99, 102, 241, 0.15)',
                'border_color' => 'rgba(99, 102, 241, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        if ($pathWithoutQuery === '/login') {
            $info = [
                'type' => 'auth',
                'title' => 'Üye Girişi',
                'subtitle' => 'Kullanıcı Giriş Ekranı',
                'badge' => 'Giriş',
                'icon' => '🔐',
                'color' => '#64748b',
                'bg_color' => 'rgba(100, 116, 139, 0.15)',
                'border_color' => 'rgba(100, 116, 139, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        if ($pathWithoutQuery === '/register') {
            $info = [
                'type' => 'auth',
                'title' => 'Yeni Üye Kaydı',
                'subtitle' => 'Üyelik Formu',
                'badge' => 'Kayıt',
                'icon' => '📝',
                'color' => '#10b981',
                'bg_color' => 'rgba(16, 185, 129, 0.15)',
                'border_color' => 'rgba(16, 185, 129, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        if ($pathWithoutQuery === '/sifremi-unuttum') {
            $info = [
                'type' => 'auth',
                'title' => 'Şifre Sıfırlama',
                'subtitle' => 'Şifremi Unuttum Ekranı',
                'badge' => 'Güvenlik',
                'icon' => '🔑',
                'color' => '#64748b',
                'bg_color' => 'rgba(100, 116, 139, 0.15)',
                'border_color' => 'rgba(100, 116, 139, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        if (str_contains($pathWithoutQuery, 'cekilis')) {
            $info = [
                'type' => 'campaign',
                'title' => 'Instagram Çekilişi',
                'subtitle' => 'Hediye Paten Kampanyası',
                'badge' => 'Kampanya',
                'icon' => '🎁',
                'color' => '#e11d48',
                'bg_color' => 'rgba(225, 29, 72, 0.15)',
                'border_color' => 'rgba(225, 29, 72, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 12. Kurumsal Sayfalar (Hakkımızda, SSS, İade, vb.)
        $staticSlug = trim($pathWithoutQuery, '/');
        $pageRecord = \App\Models\Page::where('slug', $staticSlug)->first();
        if ($pageRecord) {
            $info = [
                'type' => 'corporate',
                'title' => $pageRecord->title,
                'subtitle' => 'Kurumsal Bilgi',
                'badge' => 'Kurumsal',
                'icon' => 'ℹ️',
                'color' => '#94a3b8',
                'bg_color' => 'rgba(148, 163, 184, 0.15)',
                'border_color' => 'rgba(148, 163, 184, 0.35)',
                'is_product' => false,
                'is_checkout' => false,
            ];
            return static::$pageInfoCache[$cacheKey] = $info;
        }

        // 13. Genel / Diğer Sayfalar
        $title = $cleanTitle;
        if (empty($title) || strcasecmp($title, 'Patenli Ayakkabılar') === 0) {
            $title = \Illuminate\Support\Str::headline($staticSlug ?: 'Sayfa');
        }

        $info = [
            'type' => 'general',
            'title' => $title,
            'subtitle' => $pathWithoutQuery,
            'badge' => 'Sayfa',
            'icon' => '📄',
            'color' => '#94a3b8',
            'bg_color' => 'rgba(148, 163, 184, 0.12)',
            'border_color' => 'rgba(148, 163, 184, 0.3)',
            'is_product' => false,
            'is_checkout' => false,
        ];
        return static::$pageInfoCache[$cacheKey] = $info;
    }

    public function getPageInfoAttribute(): array
    {
        return static::resolvePageInfo($this->current_path, $this->current_title);
    }

    public function getCurrentProductAttribute(): ?\App\Models\Product
    {
        $info = $this->page_info;
        if (!empty($info['product'])) {
            return $info['product'];
        }

        if (!empty($this->current_path) && str_starts_with($this->current_path, '/urun/')) {
            $parts = explode('/', trim($this->current_path, '/'));
            $slug = $parts[1] ?? null;
            if ($slug) {
                $slug = explode('?', $slug)[0];
                if (!array_key_exists($slug, static::$productCache)) {
                    static::$productCache[$slug] = \App\Models\Product::where('slug', $slug)
                        ->orWhere('slug', urldecode($slug))
                        ->with('images')
                        ->first();
                }
                $product = static::$productCache[$slug];
                if ($product) return $product;
            }
        }

        if (!empty($this->current_title)) {
            $clean = static::cleanTitle($this->current_title);
            if (!empty($clean) && strcasecmp($clean, 'Patenli Ayakkabılar') !== 0) {
                return \App\Models\Product::where('name', $clean)->with('images')->first();
            }
        }

        return null;
    }

    public function getCurrentProductImageAttribute(): ?string
    {
        $info = $this->page_info;
        if (!empty($info['image'])) {
            return $info['image'];
        }

        $product = $this->current_product;
        if (!$product) return null;
        return $product->images->first()?->image_url ?? $product->images->first()?->raw_image_url;
    }

    /**
     * Ziyaretçiye yönlendirme emri kuyrukla
     */
    public function queueRedirect(string $targetUrl, ?string $message = null, int $countdown = 0): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'redirect',
                'target_url' => $targetUrl,
                'message' => $message,
                'countdown' => $countdown,
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Ziyaretçiye özel teklif / kupon / bildirim popup'ı fırlat
     */
    public function queueOffer(string $title, string $message, ?string $couponCode = null, ?string $actionButton = null, ?string $actionUrl = null): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'offer',
                'title' => $title,
                'message' => $message,
                'coupon_code' => $couponCode,
                'action_button' => $actionButton,
                'action_url' => $actionUrl,
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Ziyaretçiye genel alert bildirim göster
     */
    public function queueAlert(string $message, string $title = 'Bildirim', string $type = 'info'): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'alert',
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Sayfayı uzaktan yenilet
     */
    public function queueReload(): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'reload',
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Oturumu kapat / sayfadan at
     */
    public function queueKick(): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'kick',
                'target_url' => '/',
                'message' => 'Oturumunuz yönetici tarafından sonlandırıldı.',
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Ziyaretçiyi engelle
     */
    public function blockVisitor(): void
    {
        $this->update([
            'is_blocked' => true,
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'blocked',
                'message' => 'Web sitemize erişiminiz sınırlandırılmıştır.',
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }
}

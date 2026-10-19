<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'is_indexable' => 'boolean',
        'published_at' => 'datetime',
        'aio_target_keywords' => 'array',
        'faq_schema' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (BlogPost $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->published_at)) {
                $post->published_at = now();
            }
            static::autoFillSeo($post);
        });

        static::updating(function (BlogPost $post) {
            if (empty($post->meta_title) || $post->isDirty('title')) {
                static::autoFillSeo($post);
            }
        });
    }

    /**
     * Otomatik SEO meta verilerini üret
     */
    protected static function autoFillSeo(BlogPost $post): void
    {
        if (empty($post->meta_title)) {
            $suffix = ' | Patenli Ayakkabılar';
            $baseTitle = trim((string) $post->title);
            if (mb_strlen($baseTitle . $suffix) <= 75) {
                $post->meta_title = $baseTitle . $suffix;
            } else {
                $shortBase = Str::words($baseTitle, 7, '');
                $post->meta_title = mb_strlen($shortBase . $suffix) <= 75
                    ? ($shortBase . $suffix)
                    : mb_substr($baseTitle, 0, 70);
            }
        }

        if (empty($post->meta_description)) {
            $source = trim(strip_tags($post->excerpt ?: ($post->content ?? '')));
            $post->meta_description = mb_substr(Str::limit($source, 155, '...'), 0, 160);
        }

        if (empty($post->image_alt)) {
            $post->image_alt = mb_substr($post->title . ' - Patenli Ayakkabı Rehberi', 0, 255);
        }

        if (empty($post->og_image) && !empty($post->image_path)) {
            $post->og_image = $post->image_path;
        }
    }

    /**
     * Tıklama oranı (CTR) odaklı ve yarım kesilmiş marka eklerini onaran SEO Başlığı
     */
    public function getSeoTitleAttribute(): string
    {
        $raw = trim((string) ($this->meta_title ?? ''));
        $baseTitle = trim((string) $this->title);
        $suffix = ' | Patenli Ayakkabılar';

        // Eski mb_substr(..., 0, 70) nedeniyle "| Patenli" şeklinde yarım kesilmiş başlıkları onar
        if ($raw === '' || preg_match('/\|\s*Patenli(\s*Ayakkab(ı|ıl|ıla)?)?$/ui', $raw)) {
            return mb_strlen($baseTitle . $suffix) <= 78
                ? ($baseTitle . $suffix)
                : $baseTitle;
        }

        return $raw;
    }

    /**
     * Tıklama oranı (CTR) odaklı blog meta açıklaması
     */
    public function getSeoDescriptionAttribute(): string
    {
        $raw = trim((string) ($this->meta_description ?? ''));
        if ($raw !== '' && mb_strlen($raw) >= 90) {
            return $raw;
        }

        $source = trim(strip_tags($this->excerpt ?: ($this->content ?? '')));
        $clean = preg_replace('/\s+/u', ' ', $source);
        $cta = ' Işıklı ve tekerlekli patenli ayakkabı modellerini keşfedin.';

        if (mb_strlen($clean) <= (160 - mb_strlen($cta))) {
            return $clean . $cta;
        }

        return Str::limit($clean, 155, '...');
    }

    /**
     * Blog içeriğindeki aşırı/tekrarlı otomatik linkleri temizler,
     * ana sayfaya (/) ve ilgili ürün kategorilerine doğal iç bağlantılar (her hedefe en fazla 1 adet) ekler.
     */
    public function renderContentWithNaturalLinks(): string
    {
        $content = (string) ($this->content ?? '');
        if ($content === '') {
            return '';
        }

        // İlk h1 veya h2'yi kaldır (hero alanında zaten H1 olarak gösteriliyor)
        $content = preg_replace('/^\s*<h[12][^>]*>.*?<\/h[12]>\s*/is', '', $content, 1) ?? $content;

        // Önceki scriptlerden kalan aşırı tekrarlı veya kırık (/guvenlik-ekipmanlari gibi) otomatik linkleri sade metne çevir
        $cleanPattern = '/<a\s+href="[^"]*"\s+class="text-teal-600 font-semibold hover:underline"[^>]*>(.*?)<\/a>/ui';
        $content = preg_replace($cleanPattern, '$1', $content) ?? $content;

        // Hedef URL bazında doğal anahtar kelime grupları (her hedef URL yazıda en fazla 1 kez linklenir)
        $linkTargets = [
            '/' => [
                'title' => 'Patenli Ayakkabı Modelleri ve Fiyatları - Ana Sayfa',
                'keywords' => ['patenli ayakkabı', 'Patenli Ayakkabılar'],
            ],
            '/patenli-ayakkabilar' => [
                'title' => 'Tüm Patenli Ayakkabı Modelleri ve Fiyatları',
                'keywords' => ['patenli ayakkabı modelleri', 'tekerlekli ayakkabı modelleri', 'patenli ayakkabılar', 'tekerlekli spor ayakkabı', 'tekerlekli ayakkabı'],
            ],
            '/kategori/kiz-cocuk' => [
                'title' => 'Kız Çocuk Patenli Ayakkabı Modelleri',
                'keywords' => ['kız çocuk patenli ayakkabı', 'kız çocuk', 'ışıklı patenli ayakkabı', 'led ışıklı'],
            ],
            '/kategori/erkek-cocuk' => [
                'title' => 'Erkek Çocuk Patenli Ayakkabı Modelleri',
                'keywords' => ['erkek çocuk patenli ayakkabı', 'erkek çocuk', 'çocuk patenli ayakkabı', 'çocuk pateni'],
            ],
            '/kategori/yetiskin-ve-cocuk' => [
                'title' => 'Yetişkin ve Çocuk Patenli Ayakkabı Modelleri',
                'keywords' => ['çift tekerlekli', 'iki tekerlekli', 'tek tekerlekli', 'yetişkin ve çocuk'],
            ],
            '/kategori/guvenlik-ekipmanlari' => [
                'title' => 'Paten Güvenlik ve Koruyucu Ekipmanları',
                'keywords' => ['koruyucu ekipman', 'güvenlik ekipmanları', 'güvenlik ekipmanı', 'kask ve dizlik', 'kask', 'dizlik'],
            ],
            '/beden-rehberi' => [
                'title' => 'Patenli Ayakkabı Numara ve Beden Rehberi',
                'keywords' => ['beden tablosu', 'beden rehberi', 'doğru beden', 'numara seçimi', 'ayak ölçümü'],
            ],
        ];

        foreach ($linkTargets as $url => $config) {
            $linkedForThisTarget = false;

            foreach ($config['keywords'] as $keyword) {
                if ($linkedForThisTarget) {
                    break;
                }

                $escapedKeyword = preg_quote($keyword, '/');
                $pattern = '/(<h[1-6][^>]*>.*?<\/h[1-6]>|<a\b[^>]*>.*?<\/a>)|(?!(?:[^<]+>|[^>]+<\/a>))(?<![\p{L}\p{N}])(' . $escapedKeyword . ')(?![\p{L}\p{N}])/ius';

                $content = preg_replace_callback($pattern, function ($matches) use ($url, $config, &$linkedForThisTarget) {
                    if (!empty($matches[1]) || $linkedForThisTarget) {
                        return $matches[0];
                    }

                    $linkedForThisTarget = true;
                    $matchedText = $matches[2];
                    return '<a href="' . e($url) . '" class="text-teal-600 font-semibold hover:underline" title="' . e($config['title']) . '">' . $matchedText . '</a>';
                }, $content) ?? $content;
            }
        }

        return $content;
    }
}

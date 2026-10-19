<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $guarded = [];

    protected $appends = ['image_url', 'raw_image_url', 'srcset'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Görselin saf (orijinal, wsrv.nl proxy'siz) doğrudan URL'sini döndürür.
     * Google Merchant Center, Sitemap ve Schema.org için gereklidir.
     */
    public function getRawImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('favicon.png');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * Görselin CDN optimize edilmiş URL'sini döndürür.
     * Web sitesi ön yüzündeki template'lerde $image->image_url olarak kullanılır.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->getResponsiveUrl(800);
    }

    /**
     * Belirtilen genişlikte WebP CDN URL'si üretir (Mobil LCP & Responsive Görsel Optimizasyonu).
     */
    public function getResponsiveUrl(int $width = 800): string
    {
        $originalUrl = $this->raw_image_url;

        // Geliştirme ortamında veya çoktan optimize edilmiş CDN url'siyse orijinali döndür
        if (app()->environment('local') || str_contains($originalUrl, 'wsrv.nl')) {
            return $originalUrl;
        }

        // URL'yi temizle (http/https kısmını at)
        $cleanUrl = preg_replace('#^https?://#', '', $originalUrl);

        // Ücretsiz ve hızlı görsel CDN'i (wsrv.nl) ile modern WEBP formatına çevir
        // n=-1 parametresi hareketli GIF'lerin animasyonlarını korur
        return 'https://wsrv.nl/?url=' . urlencode($cleanUrl) . '&w=' . $width . '&output=webp&q=82&we&n=-1';
    }

    /**
     * Mobil ve masaüstü ekranlar için duyarlı (responsive) WebP srcset dizgesi döndürür.
     */
    public function getSrcsetAttribute(): string
    {
        $originalUrl = $this->raw_image_url;

        if (app()->environment('local') || str_contains($originalUrl, 'wsrv.nl')) {
            return '';
        }

        return $this->getResponsiveUrl(400) . ' 400w, ' . $this->getResponsiveUrl(800) . ' 800w';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $guarded = [];

    protected $appends = ['image_url', 'raw_image_url'];

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
        $originalUrl = Storage::disk('public')->url($this->image_path);
        
        // Geliştirme ortamında veya çoktan optimize edilmiş CDN url'siyse orijinali döndür
        if (app()->environment('local') || str_contains($originalUrl, 'wsrv.nl')) {
            return $originalUrl;
        }

        // URL'yi temizle (http/https kısmını at)
        $cleanUrl = preg_replace('#^https?://#', '', $originalUrl);
        
        // Ücretsiz ve inanılmaz hızlı görsel CDN'i (wsrv.nl) ile
        // Boyutu max 800px genişliğe sınırla ve modern WEBP formatına çevir
        // n=-1 parametresi hareketli GIF'lerin animasyonlarını (tüm kareleri) korumasını sağlar
        return 'https://wsrv.nl/?url=' . urlencode($cleanUrl) . '&w=800&output=webp&we&n=-1';
    }
}

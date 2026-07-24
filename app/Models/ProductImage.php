<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $guarded = [];

    protected $appends = ['image_url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Görselin tam URL'sini döndürür.
     * Tüm template'lerde $image->image_url olarak kullanılır.
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
        return 'https://wsrv.nl/?url=' . urlencode($cleanUrl) . '&w=800&output=webp&we';
    }
}

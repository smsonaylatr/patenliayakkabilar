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
     * Görselin doğrudan birinci parti (first-party) URL'sini döndürür.
     * Google SERP çoklu ürün görseli (multi-image collage) ve Google Images
     * indekslemesi için tüm görseller kendi alan adımızdan sunulmalıdır.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->raw_image_url;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_indexable' => 'boolean',
        'status' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
            // SEO alanlarını otomatik doldur
            static::autoFillSeo($category);
        });

        static::updating(function (Category $category) {
            if (empty($category->meta_title) || $category->isDirty('name')) {
                static::autoFillSeo($category);
            }
        });
    }

    /**
     * Otomatik SEO meta verilerini üret
     */
    protected static function autoFillSeo(Category $category): void
    {
        if (empty($category->meta_title)) {
            $category->meta_title = mb_substr($category->name . ' Patenli Ayakkabı Modelleri | Patenli Ayakkabılar', 0, 70);
        }

        if (empty($category->meta_description)) {
            $category->meta_description = mb_substr(
                $category->name . ' kategorisindeki en popüler patenli ayakkabı modelleri. Güvenli alışveriş, hızlı kargo ve uygun fiyat seçenekleriyle.',
                0,
                160
            );
        }
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Aktif ürün sayısı
     */
    public function activeProductCount(): int
    {
        return $this->products()->where('status', true)->count();
    }
}

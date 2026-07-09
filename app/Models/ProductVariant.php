<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];

    public const COLOR_OPTIONS = [
        'Beyaz'       => 'Beyaz',
        'Siyah'       => 'Siyah',
        'Kırmızı'     => 'Kırmızı',
        'Mavi'        => 'Mavi',
        'Pembe'       => 'Pembe',
        'Pudra'       => 'Pudra',
        'Yeşil'       => 'Yeşil',
        'Mor'         => 'Mor',
        'Turuncu'     => 'Turuncu',
        'Sarı'        => 'Sarı',
        'Gri'         => 'Gri',
        'Lacivert'    => 'Lacivert',
        'Bej'         => 'Bej',
        'Bordo'       => 'Bordo',
        'Haki'        => 'Haki',
        'Turkuaz'     => 'Turkuaz',
        'Altın'       => 'Altın',
        'Gümüş'       => 'Gümüş',
        'Fuşya'       => 'Fuşya',
        'Lila'        => 'Lila',
        'Kahverengi'  => 'Kahverengi',
        'Neon Yeşil'  => 'Neon Yeşil',
        'Neon Pembe'  => 'Neon Pembe',
        'Gökkuşağı'   => 'Gökkuşağı',
    ];

    protected function casts(): array
    {
        return [
            'color' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // SKU otomatik üret
        static::creating(function (ProductVariant $variant) {
            if (empty($variant->sku) && $variant->product_id) {
                $product = Product::find($variant->product_id);
                if ($product) {
                    $slug = strtoupper(\Illuminate\Support\Str::slug($product->name));
                    // Birden fazla renk varsa ilk iki harflerini birleştir
                    $colors = $variant->color ?? [];
                    if (is_string($colors)) {
                        $colors = [$colors];
                    }
                    $colorCode = collect($colors)
                        ->map(fn ($c) => mb_strtoupper(mb_substr($c, 0, 2)))
                        ->implode('-') ?: 'XX';
                    $variant->sku = $slug . '-' . $colorCode . '-' . ($variant->size ?? '00');
                }
            }
        });

        // Fiyatlar yanlış girildiyse (indirim > fiyat) yerlerini değiştir
        static::saving(function (ProductVariant $variant) {
            if ($variant->discount_price && $variant->price && $variant->discount_price > $variant->price) {
                $temp = $variant->price;
                $variant->price = $variant->discount_price;
                $variant->discount_price = $temp;
            }
        });

        // Varyant kaydedildiğinde/silindiğinde ürün fiyat/stoğunu güncelle
        static::saved(function (ProductVariant $variant) {
            $variant->product?->syncFromVariants();
        });

        static::deleted(function (ProductVariant $variant) {
            $variant->product?->syncFromVariants();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

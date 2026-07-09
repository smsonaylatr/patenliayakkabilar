<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // SKU otomatik üret
        static::creating(function (ProductVariant $variant) {
            if (empty($variant->sku) && $variant->product_id) {
                $product = Product::find($variant->product_id);
                if ($product) {
                    $slug = strtoupper(\Illuminate\Support\Str::slug($product->name));
                    $colorCode = mb_strtoupper(mb_substr($variant->color ?? 'XX', 0, 2));
                    $variant->sku = $slug . '-' . $colorCode . '-' . ($variant->size ?? '00');
                }
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

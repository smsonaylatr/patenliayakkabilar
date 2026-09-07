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

        // Fiyatlar yanlış girildiyse (indirim > fiyat) yerlerini değiştir ve SKU temizle
        static::saving(function (ProductVariant $variant) {
            if (!empty($variant->sku)) {
                $variant->sku = \App\Services\SchemaService::sanitizeSku($variant->sku);
            }
            if ($variant->discount_price && $variant->price && $variant->discount_price > $variant->price) {
                $temp = $variant->price;
                $variant->price = $variant->discount_price;
                $variant->discount_price = $temp;
            }
        });

        // Varyant kaydedildiğinde/silindiğinde ürün fiyat/stoğunu güncelle
        static::saved(function (ProductVariant $variant) {
            $variant->product?->syncFromVariants();
            if ($variant->stock > 0 && $variant->product?->status) {
                \App\Services\StockNotificationService::processNotifications($variant->product, $variant);
            }
        });

        static::deleted(function (ProductVariant $variant) {
            $variant->product?->syncFromVariants();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockNotifications()
    {
        return $this->hasMany(StockNotification::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }

    /**
     * Güvenli atomik stok düşürme (race condition korumalı).
     * WHERE stock >= qty koşuluyla negatif stok oluşması engellenir.
     *
     * @return bool Yeterli stok var mıydı ve düşüldü mü
     */
    public function safeDecrement(int $qty, ?string $reference = null, ?string $note = null): bool
    {
        if ($qty <= 0) return false;

        $oldStock = (int) $this->stock;
        $affected = static::where('id', $this->id)
            ->where('stock', '>=', $qty)
            ->update(['stock' => \Illuminate\Support\Facades\DB::raw("stock - {$qty}")]);

        if ($affected > 0) {
            $this->refresh();
            StockMovement::record(
                productId: $this->product_id,
                variantId: $this->id,
                type: StockMovement::TYPE_SALE,
                quantity: $qty,
                oldStock: $oldStock,
                newStock: (int) $this->stock,
                reference: $reference,
                note: $note,
            );
            return true;
        }

        return false;
    }

    /**
     * Güvenli stok artırma (iptal, iade, yenileme).
     */
    public function safeIncrement(int $qty, string $type = 'restock', ?string $reference = null, ?string $note = null): void
    {
        if ($qty <= 0) return;

        $oldStock = (int) $this->stock;
        $this->increment('stock', $qty);
        $this->refresh();

        StockMovement::record(
            productId: $this->product_id,
            variantId: $this->id,
            type: $type,
            quantity: $qty,
            oldStock: $oldStock,
            newStock: (int) $this->stock,
            reference: $reference,
            note: $note,
        );
    }

    /**
     * Stok durumu label'ı
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) return 'Tükendi';
        if ($this->stock <= 3) return 'Kritik';
        if ($this->stock <= 5) return 'Düşük';
        return 'Yeterli';
    }
}

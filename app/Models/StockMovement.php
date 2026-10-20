<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $guarded = [];

    /**
     * Hareket tipleri
     */
    public const TYPE_SALE       = 'sale';        // Satış (stok düşme)
    public const TYPE_CANCEL     = 'cancel';      // İptal (stok geri yükleme)
    public const TYPE_RESTOCK    = 'restock';     // Stok yenileme (admin)
    public const TYPE_ADJUSTMENT = 'adjustment';  // Manuel düzeltme
    public const TYPE_RETURN     = 'return';      // İade
    public const TYPE_SYNC       = 'sync';        // Porego senkronizasyonu

    public const TYPES = [
        self::TYPE_SALE       => 'Satış',
        self::TYPE_CANCEL     => 'İptal',
        self::TYPE_RESTOCK    => 'Stok Yenileme',
        self::TYPE_ADJUSTMENT => 'Manuel Düzeltme',
        self::TYPE_RETURN     => 'İade',
        self::TYPE_SYNC       => 'Senkronizasyon',
    ];

    protected function casts(): array
    {
        return [
            'quantity'  => 'integer',
            'old_stock' => 'integer',
            'new_stock' => 'integer',
        ];
    }

    // ─── İlişkiler ───

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Yardımcı Metodlar ───

    /**
     * Stok hareketi kaydet
     */
    public static function record(
        int $productId,
        ?int $variantId,
        string $type,
        int $quantity,
        int $oldStock,
        int $newStock,
        ?string $reference = null,
        ?string $note = null,
        ?int $createdBy = null,
    ): self {
        return self::create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'type'       => $type,
            'quantity'   => $quantity,
            'old_stock'  => $oldStock,
            'new_stock'  => $newStock,
            'reference'  => $reference,
            'note'       => $note,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Belirli bir sipariş referansı ve varyant/ürün için net düşülmüş stok miktarını hesaplar.
     * Pozitif değer = şu an sipariş için stoktan düşülmüş (ve henüz iade/iptal ile geri alınmamış) net adet.
     */
    public static function getNetDeductedQty(string $reference, ?int $variantId, ?int $productId = null): int
    {
        if (empty($reference) || $reference === 'N/A') {
            return 0;
        }

        $query = self::where('reference', $reference);

        if ($variantId) {
            $query->where('variant_id', $variantId);
        } elseif ($productId) {
            $query->where('product_id', $productId)->whereNull('variant_id');
        } else {
            return 0;
        }

        return (int) $query->selectRaw('COALESCE(SUM(CAST(old_stock AS SIGNED) - CAST(new_stock AS SIGNED)), 0) as net_deducted')
            ->value('net_deducted');
    }

    /**
     * Siparişin geri yüklenebilir (henüz iade/iptal edilmemiş) stok miktarını döndürür.
     */
    public static function getRestorableQty(string $reference, ?int $variantId, ?int $productId, int $requestedQty): int
    {
        if ($requestedQty <= 0) {
            return 0;
        }

        if (empty($reference) || $reference === 'N/A') {
            return $requestedQty;
        }

        // Eğer bu sipariş için tabloda hiç hareket yoksa (eski sipariş), istenen miktarı kabul et
        $hasAnyForOrder = self::where('reference', $reference)->exists();
        if (!$hasAnyForOrder) {
            return $requestedQty;
        }

        $netDeducted = self::getNetDeductedQty($reference, $variantId, $productId);
        if ($netDeducted <= 0) {
            return 0;
        }

        return min($requestedQty, $netDeducted);
    }

    /**
     * Hareket tipi Türkçe label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Hareket yönü: pozitif mi negatif mi
     */
    public function getDirectionAttribute(): string
    {
        return $this->new_stock >= $this->old_stock ? 'in' : 'out';
    }

    /**
     * Değişim miktarı (işaretli)
     */
    public function getDeltaAttribute(): int
    {
        return $this->new_stock - $this->old_stock;
    }
}

<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sms_consent' => 'boolean',
        'is_invoiced' => 'boolean',
        'gib_invoice_date' => 'datetime',
        'porego_sync_locked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('created_at');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    /**
     * Siparişin Kick Speed marka ürün içerip içermediğini kontrol eder.
     */
    public function hasKickSpeedProducts(): bool
    {
        $this->loadMissing(['items.product', 'items.variant']);

        foreach ($this->items as $item) {
            // 1. İlişkili ürünün brand alanını kontrol et
            $brand = trim((string)($item->product?->brand ?? ''));
            if ($brand !== '' && preg_match('/kick[\s_-]?speed/i', $brand)) {
                return true;
            }

            // 2. Sipariş kalemi ürün adını veya ürün adını kontrol et
            $itemProductName = trim((string)($item->product_name ?? ''));
            if ($itemProductName !== '' && preg_match('/kick[\s_-]?speed/i', $itemProductName)) {
                return true;
            }

            $productName = trim((string)($item->product?->name ?? ''));
            if ($productName !== '' && preg_match('/kick[\s_-]?speed/i', $productName)) {
                return true;
            }

            // 3. SKU alanını kontrol et (KS-..., KICKSPEED-...)
            $sku = trim((string)($item->product?->sku ?? ($item->variant?->sku ?? '')));
            if ($sku !== '' && preg_match('/^KS[-_]|kick[\s_-]?speed/i', $sku)) {
                return true;
            }
        }

        return false;
    }
}

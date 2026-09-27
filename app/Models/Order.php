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

    public function scopeSource($query, string $source)
    {
        return $query->where('traffic_source', $source);
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

    /**
     * Siparişin geldiği tam kaynak / giriş bağlantısını döndürür.
     */
    public function getSourceUrlAttribute(): string
    {
        if (!empty($this->landing_url)) {
            return $this->landing_url;
        }

        $baseUrl = rtrim(config('app.url', 'https://patenliayakkabilar.com'), '/');

        $params = array_filter([
            'utm_source'   => $this->utm_source,
            'utm_medium'   => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_term'     => $this->utm_term,
            'utm_content'  => $this->utm_content,
            'gclid'        => $this->gclid,
        ]);

        if (!empty($params)) {
            return $baseUrl . '/?' . http_build_query($params);
        }

        $sourceLower = strtolower($this->traffic_source ?? '');

        if (str_contains($sourceLower, 'instagram')) {
            return $this->referrer ?: ($baseUrl . '/?utm_source=instagram&utm_medium=bio');
        }

        if (str_contains($sourceLower, 'google ads')) {
            return $this->referrer ?: ($this->gclid ? ($baseUrl . '/?gclid=' . $this->gclid) : ($baseUrl . '/?utm_source=google&utm_medium=cpc'));
        }

        if (str_contains($sourceLower, 'google')) {
            return $this->referrer ?: 'https://www.google.com/search?q=patenliayakkabilar.com';
        }

        if (str_contains($sourceLower, 'tiktok')) {
            return $this->referrer ?: ($baseUrl . '/?utm_source=tiktok&utm_medium=bio');
        }

        if (str_contains($sourceLower, 'whatsapp')) {
            return $this->referrer ?: ($baseUrl . '/?utm_source=whatsapp&utm_medium=chat');
        }

        if (str_contains($sourceLower, 'admin')) {
            return $baseUrl . '/admin/orders';
        }

        if (!empty($this->referrer)) {
            return $this->referrer;
        }

        return $baseUrl . '/';
    }
}

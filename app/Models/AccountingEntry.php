<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model
{
    protected $guarded = [];

    /**
     * İşlem tipleri
     */
    public const TYPE_SALE       = 'sale';       // Satış geliri
    public const TYPE_REFUND     = 'refund';     // İade (gider)
    public const TYPE_SHIPPING   = 'shipping';   // Kargo geliri
    public const TYPE_DISCOUNT   = 'discount';   // İndirim (gider)
    public const TYPE_ADJUSTMENT = 'adjustment'; // Manuel düzeltme

    public const TYPES = [
        self::TYPE_SALE       => 'Satış',
        self::TYPE_REFUND     => 'İade',
        self::TYPE_SHIPPING   => 'Kargo',
        self::TYPE_DISCOUNT   => 'İndirim',
        self::TYPE_ADJUSTMENT => 'Manuel Düzeltme',
    ];

    /**
     * İade nedenleri
     */
    public const RETURN_REASONS = [
        'defective'      => 'Ürün Kusurlu / Hasarlı',
        'wrong_product'  => 'Yanlış Ürün Gönderildi',
        'wrong_size'     => 'Yanlış Beden',
        'not_as_expected' => 'Beklentileri Karşılamadı',
        'changed_mind'   => 'Müşteri Fikrini Değiştirdi',
        'late_delivery'  => 'Geç Teslimat',
        'other'          => 'Diğer',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    // ─── İlişkiler ───

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Yardımcı Metodlar ───

    /**
     * Satış kaydı oluştur (sipariş teslim edildiğinde)
     */
    public static function recordSale(Order $order, ?int $createdBy = null): self
    {
        return self::create([
            'order_id'    => $order->id,
            'type'        => self::TYPE_SALE,
            'amount'      => $order->grand_total,
            'description' => "Sipariş #{$order->order_number} satış geliri",
            'reference'   => $order->order_number,
            'created_by'  => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * İade kaydı oluştur (sipariş iade edildiğinde)
     */
    public static function recordRefund(
        Order $order,
        ?string $returnReason = null,
        ?string $note = null,
        ?int $createdBy = null,
    ): self {
        return self::create([
            'order_id'      => $order->id,
            'type'          => self::TYPE_REFUND,
            'amount'        => -1 * abs($order->grand_total),
            'description'   => "Sipariş #{$order->order_number} iade",
            'reference'     => $order->order_number,
            'return_reason' => $returnReason,
            'note'          => $note,
            'created_by'    => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Tip label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * İade nedeni label
     */
    public function getReturnReasonLabelAttribute(): string
    {
        if (!$this->return_reason) return '-';
        return self::RETURN_REASONS[$this->return_reason] ?? $this->return_reason;
    }
}

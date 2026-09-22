<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Log;

class OrderItemObserver
{
    /**
     * Sipariş kalemi oluşturulduğunda stok düş + tutar güncelle.
     * Not: Sadece admin panelden manuel ekleme için çalışır.
     * Checkout flow'da stok düşme ayrıca yönetilir (Checkout::decrementStockForOrder).
     */
    public function created(OrderItem $item): void
    {
        // total_price hesapla
        $item->total_price = $item->quantity * $item->unit_price;
        $item->saveQuietly();

        // Checkout flow'dan geliyorsa stok düşürme — ödeme yöntemine göre
        // Checkout veya OrderObserver ayrıca yönetir.
        // Sadece admin panelden manuel ekleme durumunda stok düş.
        $order = $item->order;
        $isCheckoutFlow = $order && in_array($order->payment_status, ['pending', 'awaiting_payment'])
            && $order->wasRecentlyCreated;

        if (!$isCheckoutFlow) {
            $this->decrementStock($item);
        }

        // Sipariş toplamlarını güncelle
        $this->recalculateOrderTotals($item);
    }

    /**
     * Sipariş kalemi güncellendiğinde stok farkını yönet + tutar güncelle.
     */
    public function updated(OrderItem $item): void
    {
        // total_price güncelle
        if ($item->wasChanged(['quantity', 'unit_price'])) {
            $item->total_price = $item->quantity * $item->unit_price;
            $item->saveQuietly();
        }

        // Miktar değiştiyse stok farkını yönet
        if ($item->wasChanged('quantity')) {
            $oldQty = (int) $item->getOriginal('quantity');
            $newQty = (int) $item->quantity;
            $diff = $newQty - $oldQty;

            if ($diff > 0) {
                // Daha fazla ürün eklendi → stok düş
                $this->decrementStockByAmount($item, $diff);
            } elseif ($diff < 0) {
                // Daha az ürün → stok geri yükle
                $this->incrementStockByAmount($item, abs($diff));
            }
        }

        // Varyant değiştiyse eski varyantın stoğunu geri yükle, yeni varyanttan düş
        if ($item->wasChanged('product_variant_id')) {
            $oldVariantId = $item->getOriginal('product_variant_id');
            $oldProductId = $item->getOriginal('product_id');
            $oldQty = (int) $item->getOriginal('quantity');

            // Eski stoğu geri yükle
            if ($oldVariantId) {
                $oldVariant = \App\Models\ProductVariant::find($oldVariantId);
                if ($oldVariant) {
                    $oldVariant->safeIncrement(
                        $oldQty,
                        StockMovement::TYPE_ADJUSTMENT,
                        $item->order?->order_number,
                        'Sipariş kalemi varyant değişikliği — eski varyant stoğu geri yüklendi'
                    );
                    $oldVariant->product?->syncFromVariants();
                }
            } elseif ($oldProductId) {
                $oldProduct = \App\Models\Product::find($oldProductId);
                $oldProduct?->safeIncrement(
                    $oldQty,
                    StockMovement::TYPE_ADJUSTMENT,
                    $item->order?->order_number,
                    'Sipariş kalemi ürün değişikliği — eski ürün stoğu geri yüklendi'
                );
            }

            // Yeni stoğu düş
            $this->decrementStock($item);
        }

        // Sipariş toplamlarını güncelle
        if ($item->wasChanged(['quantity', 'unit_price', 'product_id', 'product_variant_id'])) {
            $this->recalculateOrderTotals($item);
        }
    }

    /**
     * Sipariş kalemi silindiğinde stok geri yükle + tutar güncelle.
     * Sipariş iptal/iade cascade'inde çalışmaması için order durumunu kontrol eder.
     */
    public function deleted(OrderItem $item): void
    {
        $order = $item->order;

        // Sipariş iptal veya iade durumundaysa, stok yönetimi OrderObserver'a bırakılır
        if ($order && in_array($order->status, ['cancelled', 'returned'])) {
            Log::info("OrderItemObserver: Sipariş #{$order->order_number} iptal/iade durumunda, stok geri yükleme atlanıyor (OrderObserver yönetir).");
            $this->recalculateOrderTotals($item);
            return;
        }

        // Admin panelden manuel silme — stoğu geri yükle
        $this->incrementStockByAmount($item, $item->quantity);

        // Sipariş toplamlarını güncelle
        $this->recalculateOrderTotals($item);
    }

    /**
     * Stok düş (kalem oluşturma için)
     */
    private function decrementStock(OrderItem $item): void
    {
        $this->decrementStockByAmount($item, $item->quantity);
    }

    /**
     * Belirtilen miktar kadar stok düş
     */
    private function decrementStockByAmount(OrderItem $item, int $qty): void
    {
        if ($qty <= 0) return;

        $orderNumber = $item->order?->order_number ?? 'N/A';

        if ($item->product_variant_id && $item->variant) {
            $success = $item->variant->safeDecrement(
                $qty,
                $orderNumber,
                "Sipariş kalemi ekleme/güncelleme ile stok düşüldü"
            );

            if ($success) {
                $item->variant->product?->syncFromVariants();
            } else {
                Log::warning("OrderItemObserver: Stok yetersiz — Varyant #{$item->product_variant_id}, miktar: {$qty}");
            }
        } elseif ($item->product_id && $item->product) {
            $success = $item->product->safeDecrement(
                $qty,
                $orderNumber,
                "Sipariş kalemi ekleme/güncelleme ile stok düşüldü"
            );

            if (!$success) {
                Log::warning("OrderItemObserver: Stok yetersiz — Ürün #{$item->product_id}, miktar: {$qty}");
            }
        }
    }

    /**
     * Belirtilen miktar kadar stok geri yükle
     */
    private function incrementStockByAmount(OrderItem $item, int $qty): void
    {
        if ($qty <= 0) return;

        $orderNumber = $item->order?->order_number ?? 'N/A';

        if ($item->product_variant_id && $item->variant) {
            $item->variant->safeIncrement(
                $qty,
                StockMovement::TYPE_ADJUSTMENT,
                $orderNumber,
                "Sipariş kalemi silme/güncelleme ile stok geri yüklendi"
            );
            $item->variant->product?->syncFromVariants();
        } elseif ($item->product_id && $item->product) {
            $item->product->safeIncrement(
                $qty,
                StockMovement::TYPE_ADJUSTMENT,
                $orderNumber,
                "Sipariş kalemi silme/güncelleme ile stok geri yüklendi"
            );
        }
    }

    /**
     * Sipariş tutar alanlarını yeniden hesapla
     * subtotal = SUM(items.quantity × items.unit_price)
     * grand_total = subtotal + shipping_price - discount_total
     */
    private function recalculateOrderTotals(OrderItem $item): void
    {
        $order = $item->order;
        if (!$order) return;

        // Silinmiş kalem dahil olmadan yeniden hesapla
        $subtotal = $order->items()
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0;

        $order->subtotal = round($subtotal, 2);
        $order->grand_total = round(
            $order->subtotal + (float) $order->shipping_price - (float) $order->discount_total,
            2
        );
        $order->saveQuietly();
    }
}

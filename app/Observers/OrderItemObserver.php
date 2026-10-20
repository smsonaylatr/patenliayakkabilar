<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Log;

class OrderItemObserver
{
    /**
     * Bu sipariş için stok hareketi yapılmalı mı?
     * İptal/iade edilmiş veya henüz ödemesi alınmamış KK/Havale siparişlerinde stok hareketi yapılmaz.
     */
    private function shouldAffectStock(?Order $order): bool
    {
        if (!$order) {
            return false;
        }

        if (in_array($order->status, ['cancelled', 'returned'])) {
            return false;
        }

        if (in_array($order->payment_method, ['credit_card', 'wire_transfer']) && $order->payment_status !== 'paid') {
            return false;
        }

        return true;
    }

    /**
     * Sipariş kalemi oluşturulduğunda stok düş + tutar güncelle.
     * Not: Sadece admin panelden manuel ekleme için çalışır.
     * Checkout flow'da OrderItem::withoutEvents() ile observer bypass edilir.
     */
    public function created(OrderItem $item): void
    {
        // total_price hesapla (original dizisini ezmemek için doğrudan sorgu ile güncelle)
        $newTotal = round((int) $item->quantity * (float) $item->unit_price, 2);
        $item->total_price = $newTotal;
        OrderItem::whereKey($item->id)->update(['total_price' => $newTotal]);

        $order = $item->order;

        if (!$this->shouldAffectStock($order)) {
            Log::info("OrderItemObserver::created: Sipariş #{$order?->order_number} durumu ({$order?->status} / {$order?->payment_status}) nedeniyle stok düşürme atlanıyor.");
            $this->recalculateOrderTotals($item);
            return;
        }

        // Stok düş (admin panelden ekleme durumunda)
        $this->decrementStock($item);

        // Sipariş toplamlarını güncelle
        $this->recalculateOrderTotals($item);
    }

    /**
     * Sipariş kalemi güncellendiğinde stok farkını yönet + tutar güncelle.
     */
    public function updated(OrderItem $item): void
    {
        // KRİTİK: Herhangi bir save/update çağrısından ÖNCE orijinal değerleri yakala!
        // Aksi halde saveQuietly() -> syncOriginal() çalışarak eski product_variant_id değerini ezer.
        $oldVariantId = $item->getOriginal('product_variant_id');
        $newVariantId = $item->product_variant_id;
        $oldProductId = $item->getOriginal('product_id');
        $newProductId = $item->product_id;
        $oldQty = (int) $item->getOriginal('quantity');
        $newQty = (int) $item->quantity;

        $variantChanged = $item->wasChanged('product_variant_id') && (int) $oldVariantId !== (int) $newVariantId;
        $productChanged = $item->wasChanged('product_id') && (int) $oldProductId !== (int) $newProductId;
        $qtyChanged = $item->wasChanged('quantity') && $oldQty !== $newQty;
        $priceChanged = $item->wasChanged('unit_price');

        // total_price güncelle (Model::syncOriginal tetiklememek için query builder kullan)
        if ($qtyChanged || $priceChanged) {
            $newTotal = round($newQty * (float) $item->unit_price, 2);
            $item->total_price = $newTotal;
            OrderItem::whereKey($item->id)->update(['total_price' => $newTotal]);
        }

        $order = $item->order;

        if ($this->shouldAffectStock($order)) {
            $orderNumber = $order?->order_number ?? 'N/A';

            if ($variantChanged || $productChanged) {
                $oldVariant = $oldVariantId ? ProductVariant::find($oldVariantId) : null;
                $newVariant = $newVariantId ? ProductVariant::find($newVariantId) : null;

                // 1. Eski varyantın/ürünün stoğunu eski miktar ($oldQty) kadar geri yükle
                if ($oldVariant) {
                    $restoreNote = $newVariant
                        ? "Sipariş kalemi değişimi ({$oldVariant->size} → {$newVariant->size} numara) — eski numara stoğu geri yüklendi"
                        : "Sipariş kalemi varyant değişikliği ({$oldVariant->size} numara) — eski varyant stoğu geri yüklendi";

                    $oldVariant->safeIncrement(
                        $oldQty,
                        StockMovement::TYPE_ADJUSTMENT,
                        $orderNumber,
                        $restoreNote
                    );
                    $oldVariant->product?->syncFromVariants();
                } elseif ($oldProductId) {
                    $oldProduct = Product::find($oldProductId);
                    $oldProduct?->safeIncrement(
                        $oldQty,
                        StockMovement::TYPE_ADJUSTMENT,
                        $orderNumber,
                        'Sipariş kalemi ürün değişikliği — eski ürün stoğu geri yüklendi'
                    );
                }

                // 2. Yeni varyantın/ürünün stoğunu yeni miktar ($newQty) kadar düşür
                if ($newVariant) {
                    $deductNote = $oldVariant
                        ? "Sipariş kalemi değişimi ({$oldVariant->size} → {$newVariant->size} numara) — yeni numara stoktan düşüldü"
                        : "Sipariş kalemi varyant değişikliği ({$newVariant->size} numara) — yeni varyant stoktan düşüldü";

                    $success = $newVariant->safeDecrement($newQty, $orderNumber, $deductNote);
                    if ($success) {
                        $newVariant->product?->syncFromVariants();
                    } else {
                        Log::warning("OrderItemObserver: Değişimde stok yetersiz — Varyant #{$newVariantId}, miktar: {$newQty}");
                    }
                } elseif ($newProductId) {
                    $newProduct = Product::find($newProductId);
                    if ($newProduct) {
                        $success = $newProduct->safeDecrement(
                            $newQty,
                            $orderNumber,
                            'Sipariş kalemi ürün değişikliği — yeni ürün stoktan düşüldü'
                        );
                        if (!$success) {
                            Log::warning("OrderItemObserver: Değişimde stok yetersiz — Ürün #{$newProductId}, miktar: {$newQty}");
                        }
                    }
                }
            } elseif ($qtyChanged) {
                // Varyant değişmedi, sadece miktar değişti → aradaki farkı uygula
                $diff = $newQty - $oldQty;

                if ($diff > 0) {
                    $this->decrementStockByAmount($item, $diff);
                } elseif ($diff < 0) {
                    $this->incrementStockByAmount($item, abs($diff));
                }
            }
        } else {
            Log::info("OrderItemObserver::updated: Sipariş #{$order?->order_number} durumu ({$order?->status} / {$order?->payment_status}) nedeniyle stok değişimi atlanıyor.");
        }

        // Sipariş toplamlarını güncelle
        if ($qtyChanged || $priceChanged || $productChanged || $variantChanged) {
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

        if (!$this->shouldAffectStock($order)) {
            Log::info("OrderItemObserver::deleted: Sipariş #{$order?->order_number} durumu ({$order?->status} / {$order?->payment_status}) nedeniyle stok geri yükleme atlanıyor.");
            $this->recalculateOrderTotals($item);
            return;
        }

        // Admin panelden manuel silme — stoğu geri yükle
        $this->incrementStockByAmount($item, (int) $item->quantity);

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

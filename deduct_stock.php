<?php

/**
 * Mevcut siparişlerin stoklarını düşen tek seferlik script.
 * Kullanım: php artisan tinker < deduct_stock.php
 * VEYA sunucuda: php deduct_stock.php
 */

// Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\StockMovement;

echo "=== Mevcut Sipariş Stok Düşürme ===\n\n";

// İptal ve teslim edilmiş hariç tüm aktif siparişler
$orders = Order::whereNotIn('status', ['cancelled'])
    ->with(['items.variant', 'items.product'])
    ->get();

echo "Toplam sipariş: " . $orders->count() . "\n\n";

$totalDeducted = 0;

foreach ($orders as $order) {
    foreach ($order->items as $item) {
        $qty = (int) $item->quantity;
        if ($qty <= 0) continue;

        if ($item->variant) {
            $oldStock = (int) $item->variant->stock;
            $item->variant->decrement('stock', $qty);
            $item->variant->refresh();

            StockMovement::create([
                'product_id' => $item->product_id,
                'variant_id' => $item->product_variant_id,
                'type'       => 'sale',
                'quantity'   => $qty,
                'old_stock'  => $oldStock,
                'new_stock'  => (int) $item->variant->stock,
                'reference'  => $order->order_number,
                'note'       => 'Mevcut sipariş stok düşürme (tek seferlik)',
                'created_by' => null,
            ]);

            // Ürün toplam stoğunu senkronize et
            $item->product?->syncFromVariants();

            echo "  ✅ #{$order->order_number} → {$item->product_name} (Beden: {$item->variant->size}) -{$qty} adet\n";
        } elseif ($item->product) {
            $oldStock = (int) $item->product->stock;
            $item->product->decrement('stock', $qty);
            $item->product->refresh();

            StockMovement::create([
                'product_id' => $item->product_id,
                'variant_id' => null,
                'type'       => 'sale',
                'quantity'   => $qty,
                'old_stock'  => $oldStock,
                'new_stock'  => (int) $item->product->stock,
                'reference'  => $order->order_number,
                'note'       => 'Mevcut sipariş stok düşürme (tek seferlik)',
                'created_by' => null,
            ]);

            echo "  ✅ #{$order->order_number} → {$item->product_name} -{$qty} adet\n";
        }

        $totalDeducted += $qty;
    }
}

echo "\n=== TAMAMLANDI ===\n";
echo "Toplam düşülen stok: {$totalDeducted} adet\n";
echo "İşlenen sipariş: " . $orders->count() . "\n";

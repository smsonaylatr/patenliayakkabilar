<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductVariant;

class SyncProductStocks extends Command
{
    protected $signature = 'products:sync-stocks {--id= : Belirli bir ürün ID} {--fix-negative : Negatif stokları sıfıra düzelt}';
    protected $description = 'Tüm ürünlerin stok ve fiyatlarını varyantlardan yeniden hesaplar, negatif stokları düzeltir';

    public function handle(): int
    {
        $fixNegative = $this->option('fix-negative');

        // 1. Negatif varyant stoklarını düzelt
        if ($fixNegative) {
            $negativeVariants = ProductVariant::where('stock', '<', 0)->get();
            if ($negativeVariants->isNotEmpty()) {
                $this->warn("⚠️  {$negativeVariants->count()} varyant negatif stokta!");
                foreach ($negativeVariants as $variant) {
                    $oldStock = $variant->stock;
                    $variant->update(['stock' => 0]);
                    $productName = $variant->product?->name ?? 'Bilinmeyen';
                    $this->line("  🔧 Varyant #{$variant->id} ({$productName} - Beden {$variant->size}): {$oldStock} → 0");
                }
            } else {
                $this->info('✅ Negatif stoklu varyant bulunamadı.');
            }
        }

        // 2. Ürün stoklarını varyantlardan senkronize et
        $query = Product::with('variants');

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $products = $query->get();

        $this->info("Toplam {$products->count()} ürün kontrol ediliyor...");

        $fixed = 0;
        foreach ($products as $product) {
            if ($product->variants->isEmpty()) {
                continue;
            }

            $oldStock = (int) $product->stock;
            $variantTotal = $product->variants->sum('stock');

            if ($oldStock !== $variantTotal) {
                $product->syncFromVariants();
                $product->refresh();
                $this->line("  ✅ [{$product->id}] {$product->name}: stok {$oldStock} → {$product->stock}");
                $fixed++;
            }
        }

        if ($fixed === 0) {
            $this->info('Tüm ürün stokları zaten senkronize.');
        } else {
            $this->info("Toplam {$fixed} ürün düzeltildi.");
        }

        return self::SUCCESS;
    }
}

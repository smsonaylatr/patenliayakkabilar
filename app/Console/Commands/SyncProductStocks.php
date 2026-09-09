<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class SyncProductStocks extends Command
{
    protected $signature = 'products:sync-stocks {--id= : Belirli bir ürün ID}';
    protected $description = 'Tüm ürünlerin stok ve fiyatlarını varyantlardan yeniden hesaplar';

    public function handle(): int
    {
        $query = Product::with('variants');

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $products = $query->get();

        $this->info("Toplam {$products->count()} ürün kontrol ediliyor...");

        $fixed = 0;
        foreach ($products as $product) {
            $oldStock = (int) $product->stock;
            $variantTotal = $product->variants->sum('stock');

            if ($oldStock !== $variantTotal && $product->variants->count() > 0) {
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

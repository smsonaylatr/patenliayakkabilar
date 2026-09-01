<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // SKU'su boş olan ürünlere otomatik SKU ata
        Product::withTrashed()
            ->where(function ($q) {
                $q->whereNull('sku')->orWhere('sku', '');
            })
            ->each(function (Product $product) {
                $cleanSlug = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', Str::slug($product->name ?: 'PRODUCT')));
                if (strlen($cleanSlug) > 15) {
                    $cleanSlug = substr($cleanSlug, 0, 15);
                }
                $product->sku = 'PATEN-' . $product->id . ($cleanSlug ? '-' . $cleanSlug : '');
                $product->saveQuietly();
            });
    }

    public function down(): void
    {
        // Geri alınamaz
    }
};
